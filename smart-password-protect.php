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
 * Tested up to: 6.6.1
 * Requires PHP: 7.0
 * Version: 1.0.0
 *
 * @package SmartPasswordProtect
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Define
define('SPP_VERSION', '1.6');
define('SPP_DIR', plugin_dir_path(__FILE__));
define('SPP_URL', plugin_dir_url(__FILE__));
define('SPP_ASSETS_URL', SPP_URL . 'assets/');
define('SPP_BASENAME', plugin_basename(__FILE__));
define('SPP_FILE', __FILE__);


// Include the necessary files
require_once(plugin_dir_path(__FILE__) . 'includes/class-spp-settings.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-spp-protection.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-spp-helpers.php');

// Initialize the plugin
function spp_init() {
    $spp_settings = new SPP_Settings();
    $spp_settings->init();

    $spp_protection = new SPP_Protection();
    $spp_protection->init();
}
add_action('plugins_loaded', 'spp_init');
