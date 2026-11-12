<?php
/**
 * Block registration and editor assets.
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

/*
 * Register every block built into build/blocks.
 */
add_action(
	'init',
	static function () {
		foreach ( glob( DENISB_DIR . 'build/blocks/*/block.json' ) as $file ) {
			register_block_type( dirname( $file ) );
		}
	}
);

/*
 * Editor tools: rotating headline words and the SEO panel.
 */
add_action(
	'enqueue_block_editor_assets',
	static function () {
		$asset_file = DENISB_DIR . 'build/editor/index.asset.php';
		if ( ! is_readable( $asset_file ) ) {
			return;
		}
		$asset = require $asset_file;
		wp_enqueue_script( 'denisb-editor', DENISB_URL . 'build/editor/index.js', $asset['dependencies'], $asset['version'], true );
		wp_set_script_translations( 'denisb-editor', 'denisb' );
	}
);

/*
 * The mega menu sits inside core/navigation, so give it a list item wrapper.
 */
add_filter(
	'block_core_navigation_listable_blocks',
	static function ( array $blocks ): array {
		$blocks[] = 'denisb/mega-menu';
		return $blocks;
	}
);

/*
 * Apply the saved colour theme before the first paint, so the page never
 * flashes the wrong theme. The theme switcher block changes it.
 */
add_action(
	'wp_head',
	static function () {
		echo "<script>try{var t=localStorage.getItem('dbTheme');if(t&&t!=='system')document.documentElement.setAttribute('data-theme',t)}catch(e){}</script>\n";
	},
	1
);

/**
 * Returns an inline SVG icon used by the blocks.
 *
 * @param string $name   Icon name: arrow, out, back, chevron.
 * @param int    $size   Width and height in pixels.
 * @param float  $stroke Stroke width.
 */
function denisb_icon( string $name, int $size = 15, float $stroke = 2 ): string {
	$paths = array(
		'arrow'   => 'M5 12h14M13 6l6 6-6 6',
		'out'     => 'M7 17 17 7M8 7h9v9',
		'back'    => 'M19 12H5M11 18l-6-6 6-6',
		'chevron' => 'm6 9 6 6 6-6',
	);
	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%2$s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="%3$s"></path></svg>',
		$size,
		esc_attr( (string) $stroke ),
		$paths[ $name ] ?? $paths['arrow']
	);
}

/**
 * Returns an image URL from an attachment ID, falling back to a stored URL.
 *
 * @param int    $id  Attachment ID.
 * @param string $url Fallback URL.
 */
function denisb_image_src( int $id, string $url = '' ): string {
	$src = $id ? wp_get_attachment_image_url( $id, 'full' ) : '';
	return $src ? (string) $src : $url;
}

/*
 * Swishfolio Core's dynamic blocks do not print the block's extra classes,
 * so block style variations (is-style-*) and custom classes chosen in the
 * editor would not reach the page. Add them to the wrapper element.
 */
add_filter(
	'render_block',
	static function ( string $html, array $block ): string {
		$name    = (string) ( $block['blockName'] ?? '' );
		$classes = trim( (string) ( $block['attrs']['className'] ?? '' ) );
		if ( '' === $classes || ! str_starts_with( $name, 'swishfolio-core/' ) || '' === trim( $html ) ) {
			return $html;
		}
		$processor = new WP_HTML_Tag_Processor( $html );
		if ( ! $processor->next_tag() ) {
			return $html;
		}
		foreach ( preg_split( '/\s+/', $classes ) as $class ) {
			$processor->add_class( $class );
		}
		return $processor->get_updated_html();
	},
	10,
	2
);
