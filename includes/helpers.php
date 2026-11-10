<?php
/**
 * Template helpers used by the Denis B theme.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns a settings value.
 *
 * @param string $key     Setting key.
 * @param string $default Value when empty.
 */
function denisb_option( string $key, string $default = '' ): string {
	$values = (array) get_option( DENISB_OPTION, array() );
	$value  = (string) ( $values[ $key ] ?? '' );
	return '' === $value ? $default : $value;
}

/**
 * Returns a post meta value.
 *
 * @param string           $key  Meta key.
 * @param int|WP_Post|null $post Post, defaults to the current post.
 */
function denisb_meta( string $key, $post = null ): string {
	$post = get_post( $post );
	return $post ? (string) get_post_meta( $post->ID, $key, true ) : '';
}

/**
 * Splits a "lines" value into rows of named columns.
 *
 * @param string   $raw  Raw textarea value.
 * @param string[] $cols Column names.
 * @return array<int, array<string, string>>
 */
function denisb_parse_lines( string $raw, array $cols ): array {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = 1 === count( $cols ) ? array( $line ) : array_map( 'trim', explode( '|', $line, count( $cols ) ) );
		$row   = array();
		foreach ( $cols as $i => $col ) {
			$row[ $col ] = $parts[ $i ] ?? '';
		}
		$rows[] = $row;
	}
	return $rows;
}

/**
 * Returns the rows of a "lines" post meta field.
 *
 * @param string           $key  Meta key.
 * @param int|WP_Post|null $post Post.
 * @return array<int, array<string, string>>
 */
function denisb_meta_lines( string $key, $post = null ): array {
	$post  = get_post( $post );
	$field = $post ? ( denisb_post_fields()[ $post->post_type ][ $key ] ?? null ) : null;
	return $field ? denisb_parse_lines( denisb_meta( $key, $post ), $field['cols'] ) : array();
}

/**
 * Returns the rows of a "lines" setting.
 *
 * @param string $key Setting key.
 * @return array<int, array<string, string>>
 */
function denisb_option_lines( string $key ): array {
	$field = denisb_settings_fields()[ $key ] ?? null;
	return $field ? denisb_parse_lines( denisb_option( $key ), $field['cols'] ) : array();
}

/**
 * Returns an attachment URL, or '' when there is none.
 *
 * @param int|string $id   Attachment ID.
 * @param string     $size Image size.
 */
function denisb_image_url( $id, string $size = 'full' ): string {
	$id = absint( $id );
	return $id ? (string) wp_get_attachment_image_url( $id, $size ) : '';
}

/**
 * Returns published posts of a Denis B type in menu order.
 *
 * @param string $type Post type.
 * @param array  $args Extra WP_Query arguments.
 * @return WP_Post[]
 */
function denisb_posts( string $type, array $args = array() ): array {
	return get_posts(
		$args + array(
			'post_type'        => $type,
			'posts_per_page'   => -1,
			'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'suppress_filters' => false,
		)
	);
}

/**
 * Returns posts with a checkbox meta turned on.
 *
 * @param string $type Post type.
 * @param string $flag Meta key.
 * @return WP_Post[]
 */
function denisb_flagged( string $type, string $flag ): array {
	return denisb_posts(
		$type,
		array(
			'meta_key'   => $flag, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
}

/**
 * Returns the host part of a URL without "www.".
 *
 * @param string $url URL.
 */
function denisb_host( string $url ): string {
	$host = (string) wp_parse_url( $url, PHP_URL_HOST );
	return '' === $host ? $url : preg_replace( '/^www\./', '', $host );
}
