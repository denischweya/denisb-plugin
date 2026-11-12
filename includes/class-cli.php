<?php
/**
 * WP-CLI commands.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/**
 * Imports Denis B content.
 */
class DenisB_CLI {

	/**
	 * Base directory for relative media paths.
	 *
	 * @var string
	 */
	private string $media_dir = '';

	/**
	 * Media references found in the file, by token number.
	 *
	 * @var array<int, string>
	 */
	private array $tokens = array();

	/**
	 * Term references, by token number, as "taxonomy:slug".
	 *
	 * @var array<int, string>
	 */
	private array $term_tokens = array();

	/**
	 * Imports content from a JSON file. Running it again updates the same posts.
	 *
	 * Posts carry block markup. Media files are listed under "media" as
	 * token => path; in the markup, {{media:<token>}} becomes the file URL and
	 * the number 9900<token> (zero-padded to four digits) becomes the
	 * attachment ID, so block attributes such as "id":99000012 and classes
	 * such as wp-image-99000012 stay valid. Likewise 9800<n> becomes the ID of
	 * the term listed at position n of "term_tokens" (as "taxonomy:slug").
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Path to the JSON file.
	 *
	 * [--media-dir=<dir>]
	 * : Folder that media paths resolve against. Defaults to the JSON file's folder.
	 *
	 * ## EXAMPLES
	 *
	 *     wp denisb import seed/content.json --media-dir=seed/media --user=admin
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function import( array $args, array $assoc_args ): void {
		$file = $args[0];
		if ( ! is_readable( $file ) ) {
			WP_CLI::error( "Cannot read $file." );
		}
		$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $data ) ) {
			WP_CLI::error( 'The file is not valid JSON.' );
		}
		$this->media_dir = rtrim( $assoc_args['media-dir'] ?? dirname( (string) realpath( $file ) ), '/' );
		$this->tokens      = array_map( 'strval', (array) ( $data['media'] ?? array() ) );
		$this->term_tokens = array_values( (array) ( $data['term_tokens'] ?? array() ) );

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		foreach ( $data['terms'] ?? array() as $taxonomy => $terms ) {
			foreach ( $terms as $slug => $name ) {
				if ( ! term_exists( $slug, $taxonomy ) ) {
					wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
				}
			}
		}

		$count = 0;
		foreach ( $data['posts'] ?? array() as $item ) {
			$this->import_post( $item );
			++$count;
		}
		WP_CLI::log( "Imported $count posts." );

		foreach ( $data['options'] ?? array() as $key => $value ) {
			update_option( $key, is_string( $value ) ? $this->resolve_ref( $value ) : $value );
		}

		flush_rewrite_rules();
		WP_CLI::success( 'Import finished.' );
	}

	/**
	 * Creates or updates one post.
	 *
	 * @param array $item Post data.
	 */
	private function import_post( array $item ): void {
		$type   = $item['type'];
		$slug   = sanitize_title( $item['slug'] ?? $item['title'] );
		$parent = empty( $item['parent'] ) ? 0 : (int) $this->resolve_ref( 'post:' . $type . ':' . $item['parent'] );
		$found  = get_posts(
			array(
				'post_type'      => $type,
				'name'           => $slug,
				'post_parent'    => $parent,
				'post_status'    => 'any',
				'posts_per_page' => 1,
			)
		);

		$postarr = array(
			'post_type'    => $type,
			'post_name'    => $slug,
			'post_title'   => $item['title'],
			'post_content' => $this->media( (string) ( $item['content'] ?? '' ) ),
			'post_excerpt' => $item['excerpt'] ?? '',
			'post_status'  => $item['status'] ?? 'publish',
			'menu_order'   => (int) ( $item['order'] ?? 0 ),
			'post_parent'  => $parent,
		);
		if ( $found ) {
			$postarr['ID'] = $found[0]->ID;
		}
		// Block markup is stored as given; kses would strip SVG and data attributes.
		kses_remove_filters();
		$id = wp_insert_post( wp_slash( $postarr ), true );
		kses_init_filters();
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "$type $slug: " . $id->get_error_message() );
			return;
		}

		if ( isset( $item['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', $item['template'] );
		}
		foreach ( $item['meta'] ?? array() as $key => $value ) {
			update_post_meta( $id, $key, is_string( $value ) ? wp_slash( $this->media( $value ) ) : $value );
		}
		foreach ( $item['terms'] ?? array() as $taxonomy => $slugs ) {
			wp_set_object_terms( $id, $slugs, $taxonomy );
		}
		if ( ! empty( $item['thumbnail'] ) ) {
			set_post_thumbnail( $id, $this->attachment( (string) $item['thumbnail'] ) );
		}
		WP_CLI::log( "  $type/$slug" );
	}

	/**
	 * Replaces media tokens with uploaded file URLs and attachment IDs.
	 *
	 * @param string $text Text with tokens.
	 */
	private function media( string $text ): string {
		$text = (string) preg_replace_callback(
			'/\{\{media:(\d+)\}\}/',
			fn( $m ) => (string) wp_get_attachment_url( $this->attachment( $m[1] ) ),
			$text
		);
		$text = (string) preg_replace_callback(
			'/\b9900(\d{4})\b/',
			fn( $m ) => isset( $this->tokens[ (string) (int) $m[1] ] ) ? (string) $this->attachment( (string) (int) $m[1] ) : $m[0],
			$text
		);
		return (string) preg_replace_callback(
			'/\b9800(\d{4})\b/',
			function ( $m ) {
				list( $taxonomy, $slug ) = array_pad( explode( ':', $this->term_tokens[ (int) $m[1] ] ?? '' ), 2, '' );
				$term = $taxonomy ? get_term_by( 'slug', $slug, $taxonomy ) : false;
				return $term ? (string) $term->term_id : $m[0];
			},
			$text
		);
	}

	/**
	 * Resolves "post:<type>:<path>" references to IDs.
	 *
	 * @param string $value Raw value.
	 */
	private function resolve_ref( string $value ): string {
		if ( preg_match( '/^post:([a-z_]+):(.+)$/', $value, $m ) ) {
			$post = get_page_by_path( $m[2], OBJECT, $m[1] );
			return $post ? (string) $post->ID : '';
		}
		return $value;
	}

	/**
	 * Uploads a media file once and returns its attachment ID.
	 *
	 * @param string $token Token number from the "media" list.
	 */
	private function attachment( string $token ): int {
		$ref = $this->tokens[ $token ] ?? '';
		if ( '' === $ref ) {
			WP_CLI::warning( "Unknown media token $token" );
			return 0;
		}
		$found = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_denisb_source', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $ref, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( $found ) {
			return (int) $found[0];
		}

		$path = $this->media_dir . '/' . ltrim( $ref, '/' );
		if ( ! is_readable( $path ) ) {
			WP_CLI::warning( "Missing media file $path" );
			return 0;
		}
		$tmp = wp_tempnam( basename( $path ) );
		copy( $path, $tmp );

		// Keep tall full-page screenshots at their original size.
		add_filter( 'big_image_size_threshold', '__return_false' );
		$id = media_handle_sideload(
			array(
				'name'     => basename( $path ),
				'tmp_name' => $tmp,
			),
			0
		);
		remove_filter( 'big_image_size_threshold', '__return_false' );
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "Could not import $ref: " . $id->get_error_message() );
			wp_delete_file( $tmp );
			return 0;
		}
		update_post_meta( $id, '_denisb_source', $ref );
		return (int) $id;
	}
}
