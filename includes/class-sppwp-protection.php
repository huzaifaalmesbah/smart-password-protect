<?php
/**
 * SPPWP Protection Class
 *
 * This file contains the SPPWP_Protection class which handles the password protection
 * functionality for the Smart Password Protect WordPress plugin.
 *
 * @package SmartPasswordProtect
 */

/**
 * SPPWP_Protection Class
 *
 * Manages the password protection functionality including authentication,
 * cookie handling, and form display.
 */
class SPPWP_Protection {

	/**
	 * Error message for password form
	 *
	 * @var string
	 */
	private $error_message = '';

	/**
	 * Name of the authentication cookie
	 *
	 * @var string
	 */
	private $cookie_name = 'sppwp_auth';

	/**
	 * Initialize the protection functionality
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'check_protection' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles for the password form
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'sppwp-password-form',
			SPPWP_ASSETS_URL . 'css/password-form.css',
			array(),
			SPPWP_VERSION
		);
	}

	/**
	 * Check if protection is needed and handle authentication
	 *
	 * @return void
	 */
	public function check_protection() {
		$options = get_option( 'sppwp_options' );
		if ( ! isset( $options['sppwp_enabled'] ) || '1' !== $options['sppwp_enabled'] ) {
			return;
		}
		if ( is_user_logged_in() ) {
			return;
		}

		$allowed_ips = isset( $options['sppwp_allowed_ips'] ) ? json_decode( $options['sppwp_allowed_ips'], true ) : array();
		$allowed_ips = array_map( 'trim', $allowed_ips );
		$allowed_ips = array_filter(
			$allowed_ips,
			function ( $ip ) {
				return filter_var( $ip, FILTER_VALIDATE_IP );
			}
		);

		$password  = isset( $options['sppwp_password'] ) ? $options['sppwp_password'] : '';
		$client_ip = SPPWP_Helpers::get_public_ip();
		if ( in_array( $client_ip, $allowed_ips, true ) ) {
			return;
		}

		$remember_me_days = isset( $options['sppwp_remember_me'] ) ? intval( $options['sppwp_remember_me'] ) : 7;

		if ( $this->is_authenticated( $password ) ) {
			return;
		}

		if ( ! empty( $_POST ) && check_admin_referer( 'sppwp_nonce_action', 'sppwp_nonce' ) ) {
			if ( isset( $_POST['sppwp_password'] ) ) {
				$submitted_password = sanitize_text_field( wp_unslash( $_POST['sppwp_password'] ) );
				if ( empty( $submitted_password ) ) {
					$this->error_message = esc_html__( 'Password is required.', 'smart-password-protect' );
				} elseif ( $submitted_password === $password ) {
					$remember_me = isset( $_POST['sppwp_remember_me'] ) ? true : false;
					$this->set_authentication_cookie( $password, $remember_me_days, $remember_me );
					return;
				} else {
					$this->error_message = esc_html__( 'Incorrect password. Please try again.', 'smart-password-protect' );
				}
			}
		}

		$this->show_password_form();
		exit;
	}

	/**
	 * Check if the user is authenticated
	 *
	 * @param string $correct_password The correct password to check against.
	 * @return boolean True if authenticated, false otherwise.
	 */
	private function is_authenticated( $correct_password ) {
		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {
			$cookie_value = sanitize_text_field( $_COOKIE[ $this->cookie_name ] );
			$parts        = explode( '|', $cookie_value );
			if ( count( $parts ) === 2 ) {
				list( $hashed_password, $expiry ) = $parts;
				if ( time() < intval( $expiry ) && wp_check_password( $correct_password, $hashed_password ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Set the authentication cookie
	 *
	 * @param string  $password         The password to hash and store.
	 * @param integer $remember_me_days Number of days to remember the user.
	 * @param boolean $remember_me      Whether to set a long-term cookie.
	 * @return void
	 */
	private function set_authentication_cookie( $password, $remember_me_days, $remember_me ) {
		$expiry          = $remember_me ? time() + ( $remember_me_days * DAY_IN_SECONDS ) : time() + DAY_IN_SECONDS;
		$hashed_password = wp_hash_password( $password );
		$cookie_value    = $hashed_password . '|' . $expiry;
		$secure          = is_ssl();
		$http_only       = true;
		setcookie( $this->cookie_name, $cookie_value, $expiry, COOKIEPATH, COOKIE_DOMAIN, $secure, $http_only );
	}

	/**
	 * Display the password form
	 *
	 * @return void
	 */
	private function show_password_form() {
		include SPPWP_DIR . 'includes/templates/password-form.php';
	}
}
