<?php
/**
 * Next case study link.
 *
 * @package DenisB
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$denisb_next = denisb_next_project( (int) ( $block->context['postId'] ?? get_the_ID() ) );
if ( ! $denisb_next ) {
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'data-screen-label' => 'Next', 'style' => 'padding:clamp(72px,9vw,128px) 0 0' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <a class="h-bb2b0e" href="<?php echo esc_url( get_permalink( $denisb_next ) ); ?>" style="display:flex;flex-direction:column;gap:14px;padding:clamp(28px,5vw,56px) 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
      <span style="font-family:'Geist Mono',monospace;font-size:14px;letter-spacing:.06em;text-transform:uppercase;color:var(--muted)"><?php echo wp_kses_post( $attributes['label'] ); ?></span>
      <span style="display:flex;justify-content:space-between;align-items:center;gap:24px">
        <span style="font-family:'Bebas Neue',sans-serif;font-size:clamp(56px,9vw,136px);line-height:.88"><?php echo esc_html( get_the_title( $denisb_next ) ); ?></span>
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="flex:none"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
      </span>
    </a>
  </section>
