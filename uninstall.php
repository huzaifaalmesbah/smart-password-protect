<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package SmartPasswordProtect
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete the plugin options from the database.
delete_option( 'spp_options' );
