<?php
/**
 * Plugin Name:       Denis B
 * Plugin URI:        https://github.com/denischweya/denisb-plugin
 * Description:       Content types, fields and settings for the Denis B portfolio theme: case studies, products, experience, services, process, testimonials and FAQs, plus a WP-CLI importer.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            Denis Bosire
 * Author URI:        https://denis.swishfolio.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       denisb
 *
 * @package DenisB
 */

defined( 'ABSPATH' ) || exit;

define( 'DENISB_VERSION', '1.0.0' );
define( 'DENISB_FILE', __FILE__ );
define( 'DENISB_DIR', plugin_dir_path( __FILE__ ) );
define( 'DENISB_URL', plugin_dir_url( __FILE__ ) );

require_once DENISB_DIR . 'includes/fields.php';
require_once DENISB_DIR . 'includes/post-types.php';
require_once DENISB_DIR . 'includes/meta-boxes.php';
require_once DENISB_DIR . 'includes/settings.php';
require_once DENISB_DIR . 'includes/helpers.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once DENISB_DIR . 'includes/class-cli.php';
	WP_CLI::add_command( 'denisb', 'DenisB_CLI' );
}

register_activation_hook(
	__FILE__,
	static function () {
		denisb_register_post_types();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
