<?php
/**
 * The Denis B settings page and admin menu.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

const DENISB_OPTION = 'denisb_settings';

add_action(
	'admin_menu',
	static function () {
		add_menu_page(
			__( 'Denis B', 'denisb' ),
			__( 'Denis B', 'denisb' ),
			'manage_options',
			'denisb',
			'denisb_render_settings_page',
			'dashicons-id-alt',
			3
		);
		add_submenu_page( 'denisb', __( 'Site settings', 'denisb' ), __( 'Site settings', 'denisb' ), 'manage_options', 'denisb', 'denisb_render_settings_page', 0 );
	}
);

add_action(
	'admin_init',
	static function () {
		register_setting(
			'denisb',
			DENISB_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'denisb_sanitize_settings',
				'default'           => array(),
			)
		);
	}
);

/**
 * Sanitizes the whole settings array.
 *
 * @param mixed $input Raw input.
 * @return array<string, string>
 */
function denisb_sanitize_settings( $input ): array {
	$input = is_array( $input ) ? $input : array();
	$out   = array();
	foreach ( denisb_settings_fields() as $key => $field ) {
		$out[ $key ] = denisb_sanitize_field( $input[ $key ] ?? '', $field );
	}
	return $out;
}

/**
 * Renders the settings page.
 */
function denisb_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$values = (array) get_option( DENISB_OPTION, array() );
	echo '<div class="wrap"><h1>' . esc_html__( 'Denis B site settings', 'denisb' ) . '</h1>';
	echo '<p>' . esc_html__( 'Text for the homepage, products page, contact section and footer. Lists such as experience, services and FAQs are edited under their own menu items.', 'denisb' ) . '</p>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'denisb' );
	foreach ( denisb_settings_sections() as $id => $section ) {
		echo '<h2 id="' . esc_attr( 'denisb-' . $id ) . '">' . esc_html( $section['title'] ) . '</h2><table class="form-table" role="presentation"><tbody>';
		foreach ( $section['fields'] as $key => $field ) {
			denisb_render_field_row( DENISB_OPTION . '[' . $key . ']', 'denisb-opt-' . $key, $values[ $key ] ?? '', $field );
		}
		echo '</tbody></table>';
	}
	submit_button();
	echo '</form></div>';
}
