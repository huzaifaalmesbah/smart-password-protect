<?php
/**
 * Helper functions for the Smart Password Protect plugin.
 *
 * This file contains the SPPWP_Helpers class, which provides utility functions
 * for retrieving the public IP address of the current user.
 *
 * @package SmartPasswordProtect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class SPPWP_Helpers
 *
 * Contains helper functions for the Smart Password Protect plugin.
 */
class SPPWP_Helpers {

	/**
	 * Get the public IP address of the current user.
	 *
	 * This method retrieves the public IP address using server variables only.
	 *
	 * @return string The public IP address or 'UNKNOWN' if not found.
	 */
	public static function get_public_ip() {
		return self::get_ip_from_server();
	}

	/**
	 * Get IP address from server variables.
	 *
	 * This method attempts to retrieve the IP address from various server variables.
	 *
	 * @return string The IP address or 'UNKNOWN' if not found.
	 */
	private static function get_ip_from_server() {
		$headers = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				// Sanitize and get the first valid IP from a potentially comma-separated list.
				$ip_list = explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) );
				foreach ( $ip_list as $ip ) {
					$ip = trim( $ip );
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
						return $ip; // Return the first valid IP found.
					}
				}
			}
		}

		return 'UNKNOWN';
	}
}
