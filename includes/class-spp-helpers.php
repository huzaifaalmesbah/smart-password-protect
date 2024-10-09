<?php
/**
 * Helper functions for the Smart Password Protect plugin.
 *
 * This file contains the SPP_Helpers class, which provides utility functions
 * for retrieving the public IP address of the current user.
 *
 * @package SmartPasswordProtect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class SPP_Helpers
 *
 * Contains helper functions for the Smart Password Protect plugin.
 */
class SPP_Helpers {

	/**
	 * Get the public IP address of the current user.
	 *
	 * This method attempts to retrieve the public IP address using various services
	 * and falls back to server variables if external services fail.
	 *
	 * @return string The public IP address or 'UNKNOWN' if not found.
	 */
	public static function get_public_ip() {
		$public_ip_services = array(
			'https://api.ipify.org',
			'https://ipecho.net/plain',
			'https://icanhazip.com',
			'https://ident.me',
		);

		foreach ( $public_ip_services as $service ) {
			$ip = self::fetch_ip_from_service( $service );
			if ( $ip ) {
				return $ip;
			}
		}

		return self::get_ip_from_server();
	}

	/**
	 * Fetch IP address from an external service.
	 *
	 * This method uses wp_remote_get() to fetch the IP address from the given URL.
	 *
	 * @param string $url The URL of the IP service.
	 * @return string|false The IP address if valid, false otherwise.
	 */
	private static function fetch_ip_from_service( $url ) {
		$response = wp_remote_get( $url, array( 'timeout' => 5 ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$ip = wp_remote_retrieve_body( $response );

		return ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) ? trim( $ip ) : false;
	}

	/**
	 * Get IP address from server variables.
	 *
	 * This method attempts to retrieve the IP address from various server variables.
	 *
	 * @return string The IP address or 'UNKNOWN' if not found.
	 */
	private static function get_ip_from_server() {
		$headers = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip_list = explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) );
				foreach ( $ip_list as $ip ) {
					$ip = trim( $ip );
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
						return $ip;
					}
				}
			}
		}

		return 'UNKNOWN';
	}
}
