<?php
/**
 * Plugin Name:       Denis B
 * Plugin URI:        https://github.com/denischweya/denisb-plugin
 * Description:       Blocks and editor tools for the Denis B theme: the Products mega menu, the products catalogue, the theme switcher, the next case study link, rotating headline words and SEO fields. Built on Swishfolio Core.
 * Version:           2.0.0
 * Requires at least: 6.7
 * Requires PHP:      8.0
 * Requires Plugins:  swishfolio-core
 * Author:            Denis Bosire
 * Author URI:        https://denis.swishfolio.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       denisb
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

define( 'DENISB_VERSION', '2.0.0' );
define( 'DENISB_FILE', __FILE__ );
define( 'DENISB_DIR', plugin_dir_path( __FILE__ ) );
define( 'DENISB_URL', plugin_dir_url( __FILE__ ) );

require_once DENISB_DIR . 'includes/blocks.php';
require_once DENISB_DIR . 'includes/projects.php';
require_once DENISB_DIR . 'includes/seo.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once DENISB_DIR . 'includes/class-cli.php';
	WP_CLI::add_command( 'denisb', 'DenisB_CLI' );
}

register_activation_hook(
	__FILE__,
	static function () {
		denisb_filter_project_args_hook();
		flush_rewrite_rules();
	}
);
