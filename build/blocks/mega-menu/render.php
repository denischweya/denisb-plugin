<?php
/**
 * Mega menu: a trigger link and a panel of items.
 *
 * @package DenisB
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$denisb_url     = $attributes['url'] ? $attributes['url'] : '/products/';
$denisb_path    = (string) wp_parse_url( $denisb_url, PHP_URL_PATH );
$denisb_request = '/' . ltrim( (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
$denisb_current = '/' !== $denisb_path && '' !== $denisb_path && str_starts_with( $denisb_request, $denisb_path );

$denisb_items = '';
foreach ( $block->inner_blocks as $denisb_item ) {
	$denisb_a    = $denisb_item->attributes;
	$denisb_name = (string) ( $denisb_a['name'] ?? '' );
	$denisb_icon = denisb_image_src( (int) ( $denisb_a['iconId'] ?? 0 ), (string) ( $denisb_a['iconUrl'] ?? '' ) );
	if ( $denisb_icon ) {
		$denisb_ico = '<img class="mm-ico" src="' . esc_url( $denisb_icon ) . '" alt="" width="44" height="44" loading="lazy">';
	} else {
		$denisb_letter = (string) ( $denisb_a['letter'] ?? '' );
		$denisb_ico    = '<span class="mm-ico mm-letter" aria-hidden="true">' . esc_html( $denisb_letter ? $denisb_letter : mb_substr( wp_strip_all_tags( $denisb_name ), 0, 1 ) ) . '</span>';
	}
	$denisb_badge  = (string) ( $denisb_a['badge'] ?? '' );
	$denisb_items .= sprintf(
		'<a class="mm-item" href="%1$s">%2$s<span class="mm-txt"><span class="mm-kind">%3$s</span><span class="mm-name">%4$s%5$s</span><span class="mm-desc">%6$s</span></span></a>',
		esc_url( (string) ( $denisb_a['url'] ?? '#' ) ),
		$denisb_ico,
		wp_kses_post( (string) ( $denisb_a['kind'] ?? '' ) ),
		wp_kses_post( $denisb_name ),
		$denisb_badge ? '<span class="mm-new">' . esc_html( $denisb_badge ) . '</span>' : '',
		wp_kses_post( (string) ( $denisb_a['desc'] ?? '' ) )
	);
}

printf(
	'<div %1$s><a class="mm-trigger" href="%2$s"%3$s>%4$s<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></a><div class="mm-panel"><div class="mm-inner"><div class="mm-grid">%5$s</div><div class="mm-foot"><span>%6$s</span><a href="%2$s">%7$s</a></div></div></div></div>',
	get_block_wrapper_attributes( array( 'class' => 'mm' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_url( $denisb_url ),
	$denisb_current ? ' aria-current="page"' : '',
	wp_kses_post( $attributes['label'] ),
	$denisb_items, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	wp_kses_post( $attributes['note'] ),
	wp_kses_post( $attributes['allLabel'] )
);
