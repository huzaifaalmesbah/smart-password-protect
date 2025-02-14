<?php
/**
 * Plugin Name: Smart Password Protect
 * Plugin URI: https://wordpress.org/plugins/smart-password-protect/
 * Description: Protect your WordPress site with a password or IP address.
 * Author: Huzaifa Al Mesbah
 * Author URI: https://profiles.wordpress.org/huzaifaalmesbah/
 * Text Domain: smart-password-protect
 * Domain Path: /languages
 * License: GPLv2 or later
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * Version: 1.0.1
 *
 * @package SmartPasswordProtect
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants for the plugin.
define( 'SPPWP_VERSION', '1.0.1' );
define( 'SPPWP_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPPWP_URL', plugin_dir_url( __FILE__ ) );
define( 'SPPWP_ASSETS_URL', SPPWP_URL . 'assets/' );
define( 'SPPWP_BASENAME', plugin_basename( __FILE__ ) );
define( 'SPPWP_FILE', __FILE__ );

// Include the manager class.
require_once SPPWP_DIR . 'includes/class-sppwp-manager.php';

/**
 * Initialize the Smart Password Protect plugin.
 *
 * @return void
 */
function sppwp_init() {
	// Load the plugin text domain for translations.
	load_plugin_textdomain( 'smart-password-protect', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	$sppwp_manager = new SPPWP_Manager();
	$sppwp_manager->init();
}

add_action( 'plugins_loaded', 'sppwp_init' );
