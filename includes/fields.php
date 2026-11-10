<?php
/**
 * Field definitions for every content type and the settings page.
 *
 * Field types:
 * - text, url, email, textarea, checkbox, image (attachment ID), number
 * - lines: one item per line, parts separated by " | ". The `cols` key names the parts.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post meta fields, keyed by post type.
 *
 * @return array<string, array<string, array>>
 */
function denisb_post_fields(): array {
	$fields = array(
		'db_project'     => array(
			'kicker'          => array( 'label' => __( 'Kicker', 'denisb' ), 'type' => 'text', 'help' => __( 'Shown first in the case study header, for example "Client project".', 'denisb' ) ),
			'category'        => array( 'label' => __( 'Category', 'denisb' ), 'type' => 'text', 'help' => __( 'For example "Redesign · Booking · WordPress".', 'denisb' ) ),
			'tagline'         => array( 'label' => __( 'Tagline', 'denisb' ), 'type' => 'textarea' ),
			'live_url'        => array( 'label' => __( 'Live site URL', 'denisb' ), 'type' => 'url' ),
			'repo_url'        => array( 'label' => __( 'Source code URL', 'denisb' ), 'type' => 'url' ),
			'host'            => array( 'label' => __( 'Browser bar text', 'denisb' ), 'type' => 'text', 'help' => __( 'Leave empty to use the live site domain.', 'denisb' ) ),
			'scroll_duration' => array( 'label' => __( 'Screenshot scroll duration', 'denisb' ), 'type' => 'text', 'help' => __( 'For example "9s". Leave empty for a still screenshot.', 'denisb' ) ),
			'meta'            => array( 'label' => __( 'Facts', 'denisb' ), 'type' => 'lines', 'cols' => array( 'k', 'v' ), 'help' => __( 'One per line: Label | Value', 'denisb' ) ),
			'challenge'       => array( 'label' => __( 'The challenge', 'denisb' ), 'type' => 'textarea' ),
			'approach'        => array( 'label' => __( 'The approach', 'denisb' ), 'type' => 'textarea' ),
			'features'        => array( 'label' => __( 'What I built', 'denisb' ), 'type' => 'lines', 'cols' => array( 't', 'd' ), 'help' => __( 'One per line: Title | Description', 'denisb' ) ),
			'tech'            => array( 'label' => __( 'Technologies', 'denisb' ), 'type' => 'lines', 'cols' => array( 'name' ), 'help' => __( 'One per line.', 'denisb' ) ),
			'quote'           => array( 'label' => __( 'Quote', 'denisb' ), 'type' => 'textarea' ),
			'quote_by'        => array( 'label' => __( 'Quote by', 'denisb' ), 'type' => 'text' ),
			'client'          => array( 'label' => __( 'Client (work list)', 'denisb' ), 'type' => 'text' ),
			'stack'           => array( 'label' => __( 'Stack (work list)', 'denisb' ), 'type' => 'text' ),
			'list_image'      => array( 'label' => __( 'Work list thumbnail', 'denisb' ), 'type' => 'image', 'help' => __( 'Leave empty to use the featured image.', 'denisb' ) ),
			'in_work_list'    => array( 'label' => __( 'Show in the Client work list on the homepage', 'denisb' ), 'type' => 'checkbox' ),
		),
		'db_product'     => array(
			'product_type' => array( 'label' => __( 'Type', 'denisb' ), 'type' => 'select', 'options' => array( 'plugin' => 'Plugin', 'theme' => 'Theme', 'saas' => 'SaaS' ) ),
			'in_grid'      => array( 'label' => __( 'Show on the Products page', 'denisb' ), 'type' => 'checkbox' ),
			'grid_name'    => array( 'label' => __( 'Products page name', 'denisb' ), 'type' => 'text', 'help' => __( 'Defaults to the title.', 'denisb' ) ),
			'grid_kind'    => array( 'label' => __( 'Products page label', 'denisb' ), 'type' => 'text', 'help' => __( 'For example "Plugin · Job board".', 'denisb' ) ),
			'grid_desc'    => array( 'label' => __( 'Products page description', 'denisb' ), 'type' => 'textarea' ),
			'thumb_style'  => array( 'label' => __( 'Products page thumbnail', 'denisb' ), 'type' => 'select', 'options' => array( 'shot' => 'Screenshot', 'icon' => 'Icon', 'letter' => 'Letter' ) ),
			'thumb_image'  => array( 'label' => __( 'Thumbnail image', 'denisb' ), 'type' => 'image' ),
			'thumb_url'    => array( 'label' => __( 'Thumbnail image URL', 'denisb' ), 'type' => 'url', 'help' => __( 'Used when no thumbnail image is chosen, for example a WordPress.org screenshot.', 'denisb' ) ),
			'letter'       => array( 'label' => __( 'Letter', 'denisb' ), 'type' => 'text' ),
			'external_url' => array( 'label' => __( 'External URL', 'denisb' ), 'type' => 'url', 'help' => __( 'When set, cards link here instead of to this product page.', 'denisb' ) ),
			'link_label'   => array( 'label' => __( 'Card link label', 'denisb' ), 'type' => 'text', 'help' => __( 'For example "WordPress.org ↗". Defaults to "Details and downloads →".', 'denisb' ) ),
			'on_home'      => array( 'label' => __( 'Show in "Things I’ve shipped" on the homepage', 'denisb' ), 'type' => 'checkbox' ),
			'home_name'    => array( 'label' => __( 'Homepage name', 'denisb' ), 'type' => 'text', 'help' => __( 'Defaults to the title.', 'denisb' ) ),
			'home_order'   => array( 'label' => __( 'Homepage position', 'denisb' ), 'type' => 'number' ),
			'home_kind'    => array( 'label' => __( 'Homepage label', 'denisb' ), 'type' => 'text' ),
			'home_desc'    => array( 'label' => __( 'Homepage description', 'denisb' ), 'type' => 'textarea' ),
			'home_url'     => array( 'label' => __( 'Homepage link URL', 'denisb' ), 'type' => 'url', 'help' => __( 'Defaults to the product page.', 'denisb' ) ),
			'home_link'    => array( 'label' => __( 'Homepage link label', 'denisb' ), 'type' => 'text' ),
			'in_menu'      => array( 'label' => __( 'Show in the Products menu', 'denisb' ), 'type' => 'checkbox' ),
			'menu_kind'    => array( 'label' => __( 'Menu label', 'denisb' ), 'type' => 'text' ),
			'menu_name'    => array( 'label' => __( 'Menu name', 'denisb' ), 'type' => 'text', 'help' => __( 'Defaults to the title.', 'denisb' ) ),
			'menu_desc'    => array( 'label' => __( 'Menu description', 'denisb' ), 'type' => 'text' ),
			'menu_url'     => array( 'label' => __( 'Menu link URL', 'denisb' ), 'type' => 'url' ),
			'menu_badge'   => array( 'label' => __( 'Menu badge', 'denisb' ), 'type' => 'text', 'help' => __( 'For example "New".', 'denisb' ) ),
			'menu_icon'    => array( 'label' => __( 'Menu icon', 'denisb' ), 'type' => 'image', 'help' => __( 'Defaults to the thumbnail image. Without an image the letter is shown.', 'denisb' ) ),
			'menu_order_n' => array( 'label' => __( 'Menu position', 'denisb' ), 'type' => 'number' ),
			'eyebrow'      => array( 'label' => __( 'Page eyebrow', 'denisb' ), 'type' => 'text' ),
			'headline'     => array( 'label' => __( 'Page headline (H1)', 'denisb' ), 'type' => 'text' ),
			'lead'         => array( 'label' => __( 'Page intro', 'denisb' ), 'type' => 'textarea' ),
			'hero_buttons' => array( 'label' => __( 'Page buttons', 'denisb' ), 'type' => 'lines', 'cols' => array( 'label', 'url', 'style' ), 'help' => __( 'One per line: Label | URL | primary (optional)', 'denisb' ) ),
			'banner'       => array( 'label' => __( 'Page banner', 'denisb' ), 'type' => 'image' ),
			'banner_alt'   => array( 'label' => __( 'Banner alt text', 'denisb' ), 'type' => 'text' ),
			'seo_title'    => array( 'label' => __( 'SEO title', 'denisb' ), 'type' => 'text' ),
			'seo_desc'     => array( 'label' => __( 'SEO description', 'denisb' ), 'type' => 'textarea' ),
			'schema'       => array( 'label' => __( 'JSON-LD', 'denisb' ), 'type' => 'code', 'help' => __( 'Optional structured data, output as-is in a JSON-LD script tag.', 'denisb' ) ),
		),
		'db_experience'  => array(
			'when' => array( 'label' => __( 'Dates', 'denisb' ), 'type' => 'text', 'help' => __( 'For example "2020 to Present".', 'denisb' ) ),
			'role' => array( 'label' => __( 'Role', 'denisb' ), 'type' => 'text' ),
		),
		'db_testimonial' => array(
			'cite' => array( 'label' => __( 'Name and role', 'denisb' ), 'type' => 'text' ),
		),
	);

	/**
	 * Filters the post meta field definitions.
	 *
	 * @param array $fields Fields keyed by post type, then meta key.
	 */
	return apply_filters( 'denisb/post_fields', $fields );
}

/**
 * Settings page fields, grouped into sections.
 *
 * @return array<string, array{title:string, fields:array}>
 */
function denisb_settings_sections(): array {
	$sections = array(
		'hero'     => array(
			'title'  => __( 'Hero', 'denisb' ),
			'fields' => array(
				'name'            => array( 'label' => __( 'Name', 'denisb' ), 'type' => 'text' ),
				'tagline'         => array( 'label' => __( 'Role and location', 'denisb' ), 'type' => 'text' ),
				'avatar'          => array( 'label' => __( 'Portrait', 'denisb' ), 'type' => 'image' ),
				'show_avatar'     => array( 'label' => __( 'Show the portrait', 'denisb' ), 'type' => 'checkbox' ),
				'available'       => array( 'label' => __( 'Show the availability badge', 'denisb' ), 'type' => 'checkbox' ),
				'available_text'  => array( 'label' => __( 'Availability text', 'denisb' ), 'type' => 'text' ),
				'headline_before' => array( 'label' => __( 'Headline start', 'denisb' ), 'type' => 'text' ),
				'rotating_words'  => array( 'label' => __( 'Rotating words', 'denisb' ), 'type' => 'lines', 'cols' => array( 'word' ), 'help' => __( 'One per line.', 'denisb' ) ),
				'headline_after'  => array( 'label' => __( 'Headline end', 'denisb' ), 'type' => 'text' ),
				'intro'           => array( 'label' => __( 'Intro', 'denisb' ), 'type' => 'textarea' ),
				'cta_label'       => array( 'label' => __( 'Button label', 'denisb' ), 'type' => 'text' ),
				'cta_url'         => array( 'label' => __( 'Button link', 'denisb' ), 'type' => 'text' ),
				'vetted_label'    => array( 'label' => __( 'Links label', 'denisb' ), 'type' => 'text' ),
				'vetted_links'    => array( 'label' => __( 'Links', 'denisb' ), 'type' => 'lines', 'cols' => array( 'label', 'url' ), 'help' => __( 'One per line: Label | URL', 'denisb' ) ),
			),
		),
		'featured' => array(
			'title'  => __( 'Featured project', 'denisb' ),
			'fields' => array(
				'featured_project' => array( 'label' => __( 'Case study', 'denisb' ), 'type' => 'post', 'post_type' => 'db_project' ),
				'featured_label'   => array( 'label' => __( 'Label', 'denisb' ), 'type' => 'text' ),
				'featured_note'    => array( 'label' => __( 'Note', 'denisb' ), 'type' => 'text' ),
				'featured_desc'    => array( 'label' => __( 'Description', 'denisb' ), 'type' => 'textarea' ),
				'featured_link'    => array( 'label' => __( 'Second button', 'denisb' ), 'type' => 'lines', 'cols' => array( 'label', 'url' ), 'help' => __( 'Label | URL', 'denisb' ) ),
				'featured_host'    => array( 'label' => __( 'Browser bar text', 'denisb' ), 'type' => 'text' ),
				'featured_image'   => array( 'label' => __( 'Screenshot', 'denisb' ), 'type' => 'image' ),
				'featured_alt'     => array( 'label' => __( 'Screenshot alt text', 'denisb' ), 'type' => 'text' ),
				'featured_scroll'  => array( 'label' => __( 'Scroll duration', 'denisb' ), 'type' => 'text' ),
				'featured_facts'   => array( 'label' => __( 'Facts', 'denisb' ), 'type' => 'lines', 'cols' => array( 'k', 'v' ), 'help' => __( 'Label | Value', 'denisb' ) ),
			),
		),
		'spotlight' => array(
			'title'  => __( 'Spotlight case study', 'denisb' ),
			'fields' => array(
				'spot_project' => array( 'label' => __( 'Case study', 'denisb' ), 'type' => 'post', 'post_type' => 'db_project' ),
				'spot_label'   => array( 'label' => __( 'Label', 'denisb' ), 'type' => 'text' ),
				'spot_desc'    => array( 'label' => __( 'Description', 'denisb' ), 'type' => 'textarea' ),
				'spot_link'    => array( 'label' => __( 'Second button', 'denisb' ), 'type' => 'lines', 'cols' => array( 'label', 'url' ), 'help' => __( 'Label | URL', 'denisb' ) ),
				'spot_image'   => array( 'label' => __( 'Screenshot', 'denisb' ), 'type' => 'image' ),
				'spot_alt'     => array( 'label' => __( 'Screenshot alt text', 'denisb' ), 'type' => 'text' ),
				'spot_scroll'  => array( 'label' => __( 'Scroll duration', 'denisb' ), 'type' => 'text' ),
				'spot_tags'    => array( 'label' => __( 'Tags', 'denisb' ), 'type' => 'lines', 'cols' => array( 'tag' ) ),
				'spot_quote'   => array( 'label' => __( 'Quote', 'denisb' ), 'type' => 'textarea' ),
				'spot_by'      => array( 'label' => __( 'Quote by', 'denisb' ), 'type' => 'text' ),
			),
		),
		'sections' => array(
			'title'  => __( 'Homepage sections', 'denisb' ),
			'fields' => array(
				'shipped_title' => array( 'label' => __( 'Products heading', 'denisb' ), 'type' => 'text' ),
				'shipped_desc'  => array( 'label' => __( 'Products note', 'denisb' ), 'type' => 'text' ),
				'work_title'    => array( 'label' => __( 'Client work heading', 'denisb' ), 'type' => 'text' ),
				'work_desc'     => array( 'label' => __( 'Client work note', 'denisb' ), 'type' => 'text' ),
				'services_title' => array( 'label' => __( 'Services heading', 'denisb' ), 'type' => 'text' ),
				'stack'         => array( 'label' => __( 'Stack', 'denisb' ), 'type' => 'lines', 'cols' => array( 'name' ), 'help' => __( 'One per line.', 'denisb' ) ),
				'process_title' => array( 'label' => __( 'Process heading', 'denisb' ), 'type' => 'text' ),
				'exp_title'     => array( 'label' => __( 'Experience heading', 'denisb' ), 'type' => 'text' ),
				'faq_title'     => array( 'label' => __( 'FAQ heading', 'denisb' ), 'type' => 'text' ),
			),
		),
		'products' => array(
			'title'  => __( 'Products page', 'denisb' ),
			'fields' => array(
				'products_title'    => array( 'label' => __( 'Heading', 'denisb' ), 'type' => 'text' ),
				'products_lead'     => array( 'label' => __( 'Intro', 'denisb' ), 'type' => 'textarea' ),
				'products_seo'      => array( 'label' => __( 'SEO title', 'denisb' ), 'type' => 'text' ),
				'products_seo_desc' => array( 'label' => __( 'SEO description', 'denisb' ), 'type' => 'textarea' ),
				'menu_note'         => array( 'label' => __( 'Products menu note', 'denisb' ), 'type' => 'text' ),
			),
		),
		'contact'  => array(
			'title'  => __( 'Contact and footer', 'denisb' ),
			'fields' => array(
				'contact_title'  => array( 'label' => __( 'Heading', 'denisb' ), 'type' => 'text' ),
				'contact_text'   => array( 'label' => __( 'Text', 'denisb' ), 'type' => 'textarea' ),
				'contact_email'  => array( 'label' => __( 'Email', 'denisb' ), 'type' => 'email' ),
				'contact_label'  => array( 'label' => __( 'Button label', 'denisb' ), 'type' => 'text' ),
				'nav_cta'        => array( 'label' => __( 'Header button label', 'denisb' ), 'type' => 'text' ),
				'footer_left'    => array( 'label' => __( 'Footer text', 'denisb' ), 'type' => 'text' ),
				'footer_credit'  => array( 'label' => __( 'Footer credit', 'denisb' ), 'type' => 'lines', 'cols' => array( 'before', 'label', 'url' ), 'help' => __( 'Before | Link text | URL', 'denisb' ) ),
				'seo_title'      => array( 'label' => __( 'Homepage SEO title', 'denisb' ), 'type' => 'text' ),
				'seo_desc'       => array( 'label' => __( 'Homepage SEO description', 'denisb' ), 'type' => 'textarea' ),
			),
		),
	);

	/**
	 * Filters the settings page sections.
	 *
	 * @param array $sections Sections keyed by ID.
	 */
	return apply_filters( 'denisb/settings_sections', $sections );
}

/**
 * Flat list of settings fields keyed by name.
 *
 * @return array<string, array>
 */
function denisb_settings_fields(): array {
	$all = array();
	foreach ( denisb_settings_sections() as $section ) {
		$all += $section['fields'];
	}
	return $all;
}

/**
 * Sanitizes one value for a field definition.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function denisb_sanitize_field( $value, array $field ) {
	switch ( $field['type'] ) {
		case 'checkbox':
			return $value ? '1' : '';
		case 'url':
			return esc_url_raw( trim( (string) $value ) );
		case 'email':
			return sanitize_email( (string) $value );
		case 'image':
		case 'post':
		case 'number':
			return '' === $value || null === $value ? '' : (string) absint( $value );
		case 'select':
			$value = (string) $value;
			return isset( $field['options'][ $value ] ) ? $value : '';
		case 'code':
			return current_user_can( 'unfiltered_html' ) ? trim( (string) $value ) : wp_kses_post( (string) $value );
		case 'textarea':
		case 'lines':
			return sanitize_textarea_field( (string) $value );
		default:
			return sanitize_text_field( (string) $value );
	}
}
