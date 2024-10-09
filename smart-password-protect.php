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
 * Tested up to: 6.6.2
 * Requires PHP: 7.0
 * Version: 1.0.0
 *
 * @package SmartPasswordProtect
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants for the plugin.
define( 'SPP_VERSION', '1.0.0' );
define( 'SPP_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPP_URL', plugin_dir_url( __FILE__ ) );
define( 'SPP_ASSETS_URL', SPP_URL . 'assets/' );
define( 'SPP_BASENAME', plugin_basename( __FILE__ ) );
define( 'SPP_FILE', __FILE__ );

// Include the manager class.
require_once SPP_DIR . 'includes/class-spp-manager.php';

/**
 * Initialize the Smart Password Protect plugin.
 *
 * @return void
 */
function spp_init() {
	// Load the plugin text domain for translations.
	load_plugin_textdomain( 'smart-password-protect', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	$spp_manager = new SPP_Manager();
	$spp_manager->init();
}

add_action( 'plugins_loaded', 'spp_init' );
