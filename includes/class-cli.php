<?php
/**
 * WP-CLI commands.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/**
 * Imports and exports Denis B content.
 */
class DenisB_CLI {

	/**
	 * Base directory for relative media paths.
	 *
	 * @var string
	 */
	private string $media_dir = '';

	/**
	 * Imports content from a JSON file. Running it again updates the same posts.
	 *
	 * Values may reference files with "media:relative/path.png" (relative to
	 * --media-dir, or to the JSON file's folder) or "media:https://…". In post
	 * content use "{{media:relative/path.png}}" to insert the uploaded file's URL.
	 * Settings that point to a post use "post:<post_type>:<slug>".
	 *
	 * ## OPTIONS
	 *
	 * <file>
	 * : Path to the JSON file.
	 *
	 * [--media-dir=<dir>]
	 * : Folder that relative media paths resolve against.
	 *
	 * ## EXAMPLES
	 *
	 *     wp denisb import seed/content.json
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
		$this->media_dir = rtrim( $assoc_args['media-dir'] ?? dirname( realpath( $file ) ), '/' );

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$count = 0;
		foreach ( $data['posts'] ?? array() as $item ) {
			$this->import_post( $item );
			++$count;
		}
		WP_CLI::log( "Imported $count posts." );

		if ( ! empty( $data['settings'] ) ) {
			$settings = (array) get_option( DENISB_OPTION, array() );
			foreach ( $data['settings'] as $key => $value ) {
				$settings[ $key ] = $this->resolve( (string) $value );
			}
			update_option( DENISB_OPTION, denisb_sanitize_settings( $settings ) );
			WP_CLI::log( 'Updated settings.' );
		}

		foreach ( $data['options'] ?? array() as $key => $value ) {
			update_option( $key, $value );
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
		$type     = $item['type'];
		$slug     = sanitize_title( $item['slug'] ?? $item['title'] );
		$existing = get_posts(
			array(
				'post_type'      => $type,
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 1,
			)
		);
		$content  = $this->media_urls( (string) ( $item['content'] ?? '' ) );
		$postarr  = array(
			'post_type'    => $type,
			'post_name'    => $slug,
			'post_title'   => $item['title'],
			'post_content' => $content,
			'post_excerpt' => $item['excerpt'] ?? '',
			'post_status'  => $item['status'] ?? 'publish',
			'menu_order'   => (int) ( $item['order'] ?? 0 ),
		);
		if ( $existing ) {
			$postarr['ID'] = $existing[0]->ID;
		}
		$id = wp_insert_post( wp_slash( $postarr ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "$type $slug: " . $id->get_error_message() );
			return;
		}

		$fields = denisb_post_fields()[ $type ] ?? array();
		foreach ( $fields as $key => $field ) {
			$value = array_key_exists( $key, $item['meta'] ?? array() ) ? $this->resolve( (string) $item['meta'][ $key ] ) : '';
			$value = denisb_sanitize_field( $value, $field );
			if ( '' === $value ) {
				delete_post_meta( $id, $key );
			} else {
				update_post_meta( $id, $key, wp_slash( $value ) );
			}
		}
		if ( ! empty( $item['thumbnail'] ) ) {
			$thumb = (int) $this->resolve( $item['thumbnail'] );
			if ( $thumb ) {
				set_post_thumbnail( $id, $thumb );
			}
		}
		WP_CLI::log( "  $type/$slug" );
	}

	/**
	 * Replaces {{media:…}} placeholders with uploaded file URLs.
	 *
	 * @param string $text Text with placeholders.
	 */
	private function media_urls( string $text ): string {
		return (string) preg_replace_callback(
			'/\{\{media:([^}]+)\}\}/',
			fn( $m ) => (string) wp_get_attachment_url( $this->media( $m[1] ) ),
			$text
		);
	}

	/**
	 * Resolves "media:" and "post:" references.
	 *
	 * @param string $value Raw value.
	 */
	private function resolve( string $value ): string {
		$value = $this->media_urls( $value );
		if ( str_starts_with( $value, 'media:' ) ) {
			return (string) $this->media( substr( $value, 6 ) );
		}
		if ( preg_match( '/^post:([a-z_]+):(.+)$/', $value, $m ) ) {
			$post = get_page_by_path( $m[2], OBJECT, $m[1] );
			return $post ? (string) $post->ID : '';
		}
		return $value;
	}

	/**
	 * Uploads a file once and returns its attachment ID.
	 *
	 * @param string $ref Relative path or URL.
	 */
	private function media( string $ref ): int {
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

		if ( preg_match( '#^https?://#', $ref ) ) {
			$tmp = download_url( $ref );
			if ( is_wp_error( $tmp ) ) {
				WP_CLI::warning( "Could not download $ref: " . $tmp->get_error_message() );
				return 0;
			}
			$name = basename( (string) wp_parse_url( $ref, PHP_URL_PATH ) );
		} else {
			$path = $this->media_dir . '/' . ltrim( $ref, '/' );
			if ( ! is_readable( $path ) ) {
				WP_CLI::warning( "Missing media file $path" );
				return 0;
			}
			$tmp = wp_tempnam( basename( $path ) );
			copy( $path, $tmp );
			$name = basename( $path );
		}

		// Keep tall full-page screenshots at their original size.
		add_filter( 'big_image_size_threshold', '__return_false' );
		$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0 );
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
