<?php
/**
 * Class SPP_Protection
 *
 * This class handles the protection mechanisms for the Smart Password Protect plugin.
 * It checks if the protection is enabled and whether the user is allowed to access the site
 * based on their IP address or password.
 *
 * @package SmartPasswordProtect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class SPP_Protection
 */
class SPP_Protection {
	/**
	 * Error message for incorrect password.
	 *
	 * @var string
	 */
	private $error_message = '';

	/**
	 * Initialize the protection class.
	 *
	 * This method sets up the necessary actions for the protection mechanism.
	 */
	public function init() {
		add_action( 'plugins_loaded', array( $this, 'start_session' ) );
		add_action( 'template_redirect', array( $this, 'check_protection' ) );
	}

	/**
	 * Start a session if one is not already started.
	 *
	 * This method checks if a session is already active and starts a new session
	 * if necessary.
	 */
	public function start_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Check protection based on user status and settings.
	 *
	 * This method checks if the protection is enabled, if the user is logged in,
	 * and whether the user's IP address is allowed. If the user is not allowed,
	 * it displays the password form.
	 */
	public function check_protection() {
		$options = get_option( 'spp_options' );
		if ( ! isset( $options['spp_enabled'] ) || '1' !== $options['spp_enabled'] ) {
			return;
		}

		if ( is_user_logged_in() ) {
			return;
		}

		$allowed_ips = isset( $options['spp_allowed_ips'] ) ? json_decode( $options['spp_allowed_ips'], true ) : array();
		$allowed_ips = array_map( 'trim', $allowed_ips );
		$allowed_ips = array_filter(
			$allowed_ips,
			function ( $ip ) {
				return filter_var( $ip, FILTER_VALIDATE_IP );
			}
		);

		$password  = isset( $options['spp_password'] ) ? $options['spp_password'] : '';
		$client_ip = SPP_Helpers::get_public_ip();

		if ( in_array( $client_ip, $allowed_ips, true ) ) {
			return;
		}

		$is_authenticated = false;
		if ( isset( $_SESSION['spp_authenticated'] ) ) {
			$is_authenticated = wp_validate_boolean( sanitize_text_field( $_SESSION['spp_authenticated'] ) );
		}

		if ( $is_authenticated ) {
			return;
		}

		// Verify nonce before processing form data.
		if ( ! empty( $_POST ) && check_admin_referer( 'spp_nonce_action', 'spp_nonce' ) ) {
			if ( isset( $_POST['spp_password'] ) ) {
				$submitted_password = sanitize_text_field( wp_unslash( $_POST['spp_password'] ) );
				if ( empty( $submitted_password ) ) {
					$this->error_message = esc_html__( 'Password is required.', 'smart-password-protect' );
				} elseif ( $submitted_password === $password ) {
					$_SESSION['spp_authenticated'] = true;
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
	 * Display the password form.
	 *
	 * This method outputs the HTML for the password form that users must fill out
	 * to access the site. It also enqueues the necessary CSS inline.
	 */
	private function show_password_form() {
		include SPP_DIR . 'includes/templetes/password-form.php';
	}
}
