<?php
/**
 * Class SPPWP_Manager
 *
 * This class manages the loading and initialization of the Smart Password Protect plugin components.
 *
 * @package SmartPasswordProtect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class SPPWP_Manager
 *
 * This class handles the loading and initialization of the Smart Password Protect plugin.
 */
class SPPWP_Manager {

	/**
	 * Initialize the SPPWP_Manager class.
	 *
	 * This method loads and initializes the necessary classes for the plugin.
	 *
	 * @return void
	 */
	public function init() {
		$this->load_classes();
		$this->initialize_classes();
		add_filter( 'plugin_action_links_' . SPPWP_BASENAME, array( $this, 'add_plugin_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Add settings link to the plugin action links.
	 *
	 * @param array $links Existing plugin action links.
	 * @return array Updated plugin action links.
	 */
	public function add_plugin_settings_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=sppwp-settings' ) . '">' . esc_html__( 'Settings', 'smart-password-protect' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Add custom row meta links for the plugin.
	 *
	 * @param array  $links Existing row meta links.
	 * @param string $file Current plugin file.
	 * @return array Updated row meta links.
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( SPPWP_BASENAME === $file ) {
			$row_meta = array(
				'support' => '<a href="https://wordpress.org/support/plugin/smart-password-protect" target="_blank">' . esc_html__( 'Support', 'smart-password-protect' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Load required classes.
	 *
	 * @return void
	 */
	private function load_classes() {
		require_once SPPWP_DIR . 'includes/class-sppwp-settings.php'; // Include settings class.
		require_once SPPWP_DIR . 'includes/class-sppwp-protection.php'; // Include protection class.
		require_once SPPWP_DIR . 'includes/class-sppwp-helpers.php'; // Include helper functions class.
	}

	/**
	 * Initialize the loaded classes.
	 *
	 * @return void
	 */
	private function initialize_classes() {
		$sppwp_settings = new SPPWP_Settings();
		$sppwp_settings->init();

		$sppwp_protection = new SPPWP_Protection();
		$sppwp_protection->init();
	}
}
