<?php
/**
 * Products catalogue: heading, filters and cards.
 *
 * @package DenisB
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$denisb_count = array(
	'all'    => 0,
	'theme'  => 0,
	'plugin' => 0,
);
$denisb_cards = '';
foreach ( $block->inner_blocks as $denisb_i => $denisb_card ) {
	$denisb_a     = $denisb_card->attributes;
	$denisb_type  = (string) ( $denisb_a['type'] ?? 'plugin' );
	$denisb_name  = (string) ( $denisb_a['name'] ?? '' );
	$denisb_plain = wp_strip_all_tags( $denisb_name );
	$denisb_img   = denisb_image_src( (int) ( $denisb_a['imageId'] ?? 0 ), (string) ( $denisb_a['imageUrl'] ?? '' ) );
	$denisb_style = (string) ( $denisb_a['thumbStyle'] ?? 'shot' );
	$denisb_load  = $denisb_i < 4 ? 'eager' : 'lazy';
	$denisb_label = (string) ( $denisb_a['linkLabel'] ?? '' );
	++$denisb_count['all'];
	if ( isset( $denisb_count[ $denisb_type ] ) ) {
		++$denisb_count[ $denisb_type ];
	}
	if ( 'letter' === $denisb_style || ! $denisb_img ) {
		$denisb_letter = (string) ( $denisb_a['letter'] ?? '' );
		$denisb_thumb  = '<span class="letter" aria-hidden="true">' . esc_html( $denisb_letter ? $denisb_letter : mb_substr( $denisb_plain, 0, 1 ) ) . '</span>';
	} elseif ( 'icon' === $denisb_style ) {
		/* translators: %s: product name. */
		$denisb_thumb = sprintf( '<img class="icon" src="%s" alt="%s" width="96" height="96" loading="%s">', esc_url( $denisb_img ), esc_attr( sprintf( __( '%s plugin icon', 'denisb' ), $denisb_plain ) ), $denisb_load );
	} else {
		/* translators: %s: product name. */
		$denisb_thumb = sprintf( '<img class="shot" src="%s" alt="%s" width="640" height="480" loading="%s">', esc_url( $denisb_img ), esc_attr( sprintf( __( '%s theme screenshot', 'denisb' ), $denisb_plain ) ), $denisb_load );
	}
	$denisb_cards .= sprintf(
		'<li data-type="%1$s"><a class="product" href="%2$s"><div class="thumb">%3$s</div><div class="pbody"><span class="mono">%4$s</span><h3>%5$s</h3><p>%6$s</p><span class="go">%7$s</span></div></a></li>' . "\n",
		esc_attr( $denisb_type ),
		esc_url( (string) ( $denisb_a['url'] ?? '#' ) ),
		$denisb_thumb,
		wp_kses_post( (string) ( $denisb_a['kind'] ?? '' ) ),
		wp_kses_post( $denisb_name ),
		wp_kses_post( (string) ( $denisb_a['desc'] ?? '' ) ),
		$denisb_label ? wp_kses_post( $denisb_label ) : esc_html__( 'Details and downloads →', 'denisb' )
	);
}
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<div class="hero">
<h1 class="display"><?php echo wp_kses_post( $attributes['title'] ); ?></h1>
<p class="lead"><?php echo wp_kses_post( $attributes['lead'] ); ?></p>
<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filter products by type', 'denisb' ); ?>">
<button type="button" data-f="all" aria-pressed="true"><?php echo esc_html( sprintf( /* translators: %d: number of products. */ __( 'All (%d)', 'denisb' ), $denisb_count['all'] ) ); ?></button>
<button type="button" data-f="theme" aria-pressed="false"><?php echo esc_html( sprintf( /* translators: %d: number of themes. */ __( 'Themes (%d)', 'denisb' ), $denisb_count['theme'] ) ); ?></button>
<button type="button" data-f="plugin" aria-pressed="false"><?php echo esc_html( sprintf( /* translators: %d: number of plugins. */ __( 'Plugins (%d)', 'denisb' ), $denisb_count['plugin'] ) ); ?></button>
</div>
<p class="count" role="status"><?php echo esc_html( sprintf( /* translators: %d: number of products. */ __( 'Showing %d products', 'denisb' ), $denisb_count['all'] ) ); ?></p>
</div>
<ul class="products">
<?php echo $denisb_cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
</ul>
</div>
