<?php
/**
 * Meta boxes and the shared field renderer.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'add_meta_boxes',
	static function () {
		foreach ( denisb_post_fields() as $type => $fields ) {
			add_meta_box(
				'denisb-fields',
				__( 'Details', 'denisb' ),
				'denisb_render_meta_box',
				$type,
				'normal',
				'high'
			);
		}
	}
);

/**
 * Renders the fields meta box.
 *
 * @param WP_Post $post Current post.
 */
function denisb_render_meta_box( WP_Post $post ): void {
	$fields = denisb_post_fields()[ $post->post_type ] ?? array();
	wp_nonce_field( 'denisb_save_' . $post->ID, 'denisb_nonce' );
	echo '<table class="form-table denisb-fields" role="presentation"><tbody>';
	foreach ( $fields as $key => $field ) {
		denisb_render_field_row( 'denisb[' . $key . ']', 'denisb-' . $key, get_post_meta( $post->ID, $key, true ), $field );
	}
	echo '</tbody></table>';
}

/**
 * Renders one field as a form table row.
 *
 * @param string $name  Input name.
 * @param string $id    Input ID.
 * @param mixed  $value Current value.
 * @param array  $field Field definition.
 */
function denisb_render_field_row( string $name, string $id, $value, array $field ): void {
	$value = (string) $value;
	echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
	switch ( $field['type'] ) {
		case 'checkbox':
			printf( '<input type="checkbox" id="%s" name="%s" value="1" %s>', esc_attr( $id ), esc_attr( $name ), checked( $value, '1', false ) );
			break;
		case 'textarea':
		case 'lines':
		case 'code':
			$rows = 'textarea' === $field['type'] ? 3 : 5;
			printf(
				'<textarea id="%s" name="%s" rows="%d" class="large-text%s">%s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				(int) $rows,
				'textarea' === $field['type'] ? '' : ' code',
				esc_textarea( $value )
			);
			break;
		case 'select':
			printf( '<select id="%s" name="%s"><option value="">%s</option>', esc_attr( $id ), esc_attr( $name ), esc_html__( 'Choose…', 'denisb' ) );
			foreach ( $field['options'] as $opt => $label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $opt ), selected( $value, $opt, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		case 'post':
			$posts = get_posts(
				array(
					'post_type'      => $field['post_type'],
					'posts_per_page' => 200,
					'orderby'        => 'menu_order title',
					'order'          => 'ASC',
					'post_status'    => 'any',
				)
			);
			printf( '<select id="%s" name="%s"><option value="">%s</option>', esc_attr( $id ), esc_attr( $name ), esc_html__( 'None', 'denisb' ) );
			foreach ( $posts as $p ) {
				printf( '<option value="%d" %s>%s</option>', (int) $p->ID, selected( $value, (string) $p->ID, false ), esc_html( get_the_title( $p ) ) );
			}
			echo '</select>';
			break;
		case 'image':
			$src = $value ? wp_get_attachment_image_url( (int) $value, 'thumbnail' ) : '';
			printf(
				'<div class="denisb-image"><img src="%1$s" alt="" style="max-width:120px;height:auto;%2$s"><input type="hidden" id="%3$s" name="%4$s" value="%5$s"> <button type="button" class="button denisb-image-pick">%6$s</button> <button type="button" class="button-link denisb-image-clear">%7$s</button></div>',
				esc_url( (string) $src ),
				$src ? '' : 'display:none;',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				esc_html__( 'Choose image', 'denisb' ),
				esc_html__( 'Remove', 'denisb' )
			);
			break;
		default:
			$type = in_array( $field['type'], array( 'url', 'email', 'number' ), true ) ? $field['type'] : 'text';
			printf( '<input type="%s" id="%s" name="%s" value="%s" class="regular-text">', esc_attr( $type ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
	}
	if ( ! empty( $field['help'] ) ) {
		echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
	}
	echo '</td></tr>';
}

add_action(
	'save_post',
	static function ( int $post_id, WP_Post $post ) {
		$fields = denisb_post_fields()[ $post->post_type ] ?? null;
		if ( ! $fields || ! isset( $_POST['denisb_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['denisb_nonce'] ) ), 'denisb_save_' . $post_id ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$input = isset( $_POST['denisb'] ) ? (array) wp_unslash( $_POST['denisb'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
		foreach ( $fields as $key => $field ) {
			$value = denisb_sanitize_field( $input[ $key ] ?? '', $field );
			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	},
	10,
	2
);

add_action(
	'admin_enqueue_scripts',
	static function ( string $hook ) {
		$screen = get_current_screen();
		$ours   = $screen && ( str_starts_with( (string) $screen->post_type, 'db_' ) || 'toplevel_page_denisb' === $hook );
		if ( ! $ours ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'denisb-admin', DENISB_URL . 'assets/admin.js', array( 'jquery' ), DENISB_VERSION, true );
		wp_add_inline_style( 'common', '.denisb-fields textarea.code{font-family:Menlo,Consolas,monospace;font-size:12px}' );
	}
);
