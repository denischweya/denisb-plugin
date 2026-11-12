<?php
/**
 * Case studies use the Swishfolio Core Project post type, served from
 * /case-study/<slug>/ and ordered by the Order attribute.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hooks the Project post type arguments. Called early so activation can
 * flush the right rewrite rules.
 */
function denisb_filter_project_args_hook(): void {
	add_filter(
		'sfcore_sfcore_project_args',
		static function ( array $args ): array {
			$args['rewrite']     = array(
				'slug'       => 'case-study',
				'with_front' => false,
			);
			$args['has_archive'] = false;
			$args['supports']    = array_values( array_unique( array_merge( (array) ( $args['supports'] ?? array() ), array( 'page-attributes' ) ) ) );
			$args['labels']      = array_merge(
				(array) ( $args['labels'] ?? array() ),
				array(
					'name'          => __( 'Case studies', 'denisb' ),
					'singular_name' => __( 'Case study', 'denisb' ),
					'menu_name'     => __( 'Case studies', 'denisb' ),
					'add_new_item'  => __( 'Add case study', 'denisb' ),
					'edit_item'     => __( 'Edit case study', 'denisb' ),
					'all_items'     => __( 'All case studies', 'denisb' ),
				)
			);
			return $args;
		}
	);
}
denisb_filter_project_args_hook();

/*
 * Admin lists show case studies in their display order.
 */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ) {
		if ( is_admin() && $query->is_main_query() && 'sfcore_project' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'menu_order title' );
			$query->set( 'order', 'ASC' );
		}
	}
);

/*
 * Old links in the form /case-study/?project=<slug> or /?project=<slug>.
 */
add_action(
	'template_redirect',
	static function () {
		if ( empty( $_GET['project'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$slug = sanitize_title( wp_unslash( $_GET['project'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post = get_page_by_path( $slug, OBJECT, 'sfcore_project' );
		if ( $post && 'publish' === $post->post_status ) {
			wp_safe_redirect( get_permalink( $post ), 301 );
			exit;
		}
	}
);

/**
 * Returns the case study after the given one, wrapping to the first.
 *
 * @param int $post_id Current case study.
 */
function denisb_next_project( int $post_id ): ?WP_Post {
	$all = get_posts(
		array(
			'post_type'      => 'sfcore_project',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
	if ( count( $all ) < 2 ) {
		return null;
	}
	$ids = wp_list_pluck( $all, 'ID' );
	$pos = array_search( $post_id, $ids, true );
	return $all[ ( false === $pos ? 0 : $pos + 1 ) % count( $all ) ];
}
