<?php
/**
 * Post types and registered meta.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'denisb_register_post_types' );

/**
 * Registers the content types and their meta.
 */
function denisb_register_post_types(): void {
	$common = array(
		'show_in_rest' => true,
		'show_in_menu' => 'denisb',
		'map_meta_cap' => true,
	);

	register_post_type(
		'db_project',
		$common + array(
			'labels'      => denisb_labels( __( 'Case study', 'denisb' ), __( 'Case studies', 'denisb' ) ),
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => array( 'slug' => 'case-study', 'with_front' => false ),
			'supports'    => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
			'menu_icon'   => 'dashicons-portfolio',
		)
	);

	register_post_type(
		'db_product',
		$common + array(
			'labels'      => denisb_labels( __( 'Product', 'denisb' ), __( 'Products', 'denisb' ) ),
			'public'      => true,
			'has_archive' => 'products',
			'rewrite'     => array( 'slug' => 'products', 'with_front' => false ),
			'supports'    => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
			'menu_icon'   => 'dashicons-products',
		)
	);

	$simple = array(
		'db_experience'  => array( __( 'Experience', 'denisb' ), __( 'Experience', 'denisb' ), array( 'title', 'editor', 'page-attributes', 'custom-fields' ) ),
		'db_service'     => array( __( 'Service', 'denisb' ), __( 'Services', 'denisb' ), array( 'title', 'editor', 'page-attributes' ) ),
		'db_step'        => array( __( 'Process step', 'denisb' ), __( 'Process steps', 'denisb' ), array( 'title', 'editor', 'page-attributes' ) ),
		'db_testimonial' => array( __( 'Testimonial', 'denisb' ), __( 'Testimonials', 'denisb' ), array( 'title', 'editor', 'page-attributes', 'custom-fields' ) ),
		'db_faq'         => array( __( 'FAQ', 'denisb' ), __( 'FAQs', 'denisb' ), array( 'title', 'editor', 'page-attributes' ) ),
	);
	foreach ( $simple as $type => list( $one, $many, $supports ) ) {
		register_post_type(
			$type,
			$common + array(
				'labels'              => denisb_labels( $one, $many ),
				'public'              => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'supports'            => $supports,
			)
		);
	}

	foreach ( denisb_post_fields() as $type => $fields ) {
		foreach ( $fields as $key => $field ) {
			register_post_meta(
				$type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => static fn( $value ) => denisb_sanitize_field( $value, $field ),
					'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
				)
			);
		}
	}
}

/**
 * Builds post type labels.
 *
 * @param string $one  Singular name.
 * @param string $many Plural name.
 * @return array<string, string>
 */
function denisb_labels( string $one, string $many ): array {
	return array(
		'name'               => $many,
		'singular_name'      => $one,
		'menu_name'          => $many,
		'all_items'          => $many,
		/* translators: %s: singular post type name. */
		'add_new_item'       => sprintf( __( 'Add %s', 'denisb' ), $one ),
		/* translators: %s: singular post type name. */
		'edit_item'          => sprintf( __( 'Edit %s', 'denisb' ), $one ),
		/* translators: %s: singular post type name. */
		'new_item'           => sprintf( __( 'New %s', 'denisb' ), $one ),
		/* translators: %s: singular post type name. */
		'view_item'          => sprintf( __( 'View %s', 'denisb' ), $one ),
		/* translators: %s: plural post type name. */
		'search_items'       => sprintf( __( 'Search %s', 'denisb' ), $many ),
		'not_found'          => __( 'Nothing found.', 'denisb' ),
		'not_found_in_trash' => __( 'Nothing found in the bin.', 'denisb' ),
	);
}

/*
 * Order lists by menu order everywhere in the admin, matching the front end.
 */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		$type = $query->get( 'post_type' );
		if ( is_string( $type ) && str_starts_with( $type, 'db_' ) && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		}
	}
);

/*
 * Case studies and products have no use for the block editor's title placeholder
 * defaults, but product pages are written in the block editor. Case studies use
 * fields only.
 */
add_filter(
	'use_block_editor_for_post_type',
	static function ( bool $use, string $type ) {
		return in_array( $type, array( 'db_project', 'db_service', 'db_step', 'db_faq', 'db_testimonial', 'db_experience' ), true ) ? false : $use;
	},
	10,
	2
);

/*
 * Case study URLs from the old static site used ?project=slug.
 */
add_action(
	'template_redirect',
	static function () {
		if ( empty( $_GET['project'] ) || is_admin() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$slug = sanitize_title( wp_unslash( $_GET['project'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post = get_page_by_path( $slug, OBJECT, 'db_project' );
		if ( $post ) {
			wp_safe_redirect( get_permalink( $post ), 301 );
			exit;
		}
	}
);
