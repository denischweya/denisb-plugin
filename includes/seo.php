<?php
/**
 * SEO fields, edited in the editor's SEO panel, and the tags they print.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post types with the SEO panel.
 */
function denisb_seo_post_types(): array {
	return (array) apply_filters( 'denisb/seo_post_types', array( 'page', 'post', 'sfcore_project' ) );
}

add_action(
	'init',
	static function () {
		foreach ( denisb_seo_post_types() as $type ) {
			foreach ( array( '_denisb_seo_title', '_denisb_seo_desc', '_denisb_schema' ) as $key ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'default'           => '',
						'show_in_rest'      => true,
						'sanitize_callback' => '_denisb_schema' === $key ? 'denisb_sanitize_schema' : 'sanitize_textarea_field',
						'auth_callback'     => static fn( $allowed, $meta_key, $post_id ) => current_user_can( 'edit_post', $post_id ),
					)
				);
			}
		}
	}
);

/**
 * Keeps JSON-LD only when it parses.
 *
 * @param string $value Raw JSON.
 */
function denisb_sanitize_schema( $value ): string {
	$value = trim( (string) $value );
	return ( '' === $value || null === json_decode( $value ) ) ? '' : $value;
}

add_filter(
	'pre_get_document_title',
	static function ( string $title ): string {
		if ( is_singular() ) {
			$custom = (string) get_post_meta( get_queried_object_id(), '_denisb_seo_title', true );
			if ( $custom ) {
				return $custom;
			}
		}
		return $title;
	}
);

add_action(
	'wp_head',
	static function () {
		if ( ! is_singular() ) {
			return;
		}
		$id     = get_queried_object_id();
		$desc   = (string) get_post_meta( $id, '_denisb_seo_desc', true );
		$schema = (string) get_post_meta( $id, '_denisb_schema', true );
		$desc   = $desc ? $desc : (string) get_the_excerpt( $id );
		if ( $desc ) {
			$title = wp_get_document_title();
			$image = (string) get_the_post_thumbnail_url( $id, 'large' );
			printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $desc ) );
			echo "<meta name=\"robots\" content=\"index,follow,max-image-preview:large\">\n";
			printf( "<meta property=\"og:type\" content=\"website\">\n<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( get_bloginfo( 'name' ) ) );
			printf( "<meta property=\"og:title\" content=\"%s\">\n<meta property=\"og:description\" content=\"%s\">\n<meta property=\"og:url\" content=\"%s\">\n", esc_attr( $title ), esc_attr( $desc ), esc_url( (string) get_permalink( $id ) ) );
			printf( "<meta name=\"twitter:card\" content=\"%s\">\n<meta name=\"twitter:title\" content=\"%s\">\n<meta name=\"twitter:description\" content=\"%s\">\n", $image ? 'summary_large_image' : 'summary', esc_attr( $title ), esc_attr( $desc ) );
			if ( $image ) {
				printf( "<meta property=\"og:image\" content=\"%1\$s\">\n<meta name=\"twitter:image\" content=\"%1\$s\">\n", esc_url( $image ) );
			}
		}
		if ( $schema && null !== json_decode( $schema ) ) {
			echo '<script type="application/ld+json">' . wp_json_encode( json_decode( $schema ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
		}
	},
	5
);
