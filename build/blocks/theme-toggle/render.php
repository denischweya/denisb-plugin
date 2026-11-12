<?php
/**
 * Theme switcher.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

$denisb_buttons = array(
	'system' => array( __( 'System', 'denisb' ), __( 'System theme', 'denisb' ), '<rect x="2" y="3" width="20" height="14" rx="2"></rect><path d="M8 21h8M12 17v4"></path>' ),
	'light'  => array( __( 'Light', 'denisb' ), __( 'Light theme', 'denisb' ), '<circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>' ),
	'dark'   => array( __( 'Dark', 'denisb' ), __( 'Dark theme', 'denisb' ), '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>' ),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'tt', 'role' => 'group', 'aria-label' => __( 'Colour theme', 'denisb' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php foreach ( $denisb_buttons as $denisb_key => list( $denisb_title, $denisb_label, $denisb_icon ) ) : ?>
<button type="button" data-t="<?php echo esc_attr( $denisb_key ); ?>" aria-pressed="<?php echo 'system' === $denisb_key ? 'true' : 'false'; ?>" title="<?php echo esc_attr( $denisb_title ); ?>" aria-label="<?php echo esc_attr( $denisb_label ); ?>"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $denisb_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?></svg></button>
<?php endforeach; ?>
</div>
