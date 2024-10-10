<?php
/**
 * Class SPPWP_Protection
 *
 * This class handles the protection mechanisms for the Smart Password Protect plugin.
 *
 * @package SmartPasswordProtect
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * SPPWP_Protection Class
 *
 * Handles password protection functionality including session management,
 * protection checks, and password form display.
 *
 * @since 1.0.0
 */
class SPPWP_Protection {

	/**
	 * Stores error messages for password validation
	 *
	 * @var string
	 */
	private $error_message = '';

	/**
	 * Initialize the protection functionality
	 *
	 * Sets up action hooks for session handling, protection checks,
	 * and style enqueuing.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'plugins_loaded', array( $this, 'start_session' ) );
		add_action( 'template_redirect', array( $this, 'check_protection' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue styles for the password form
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style(
			'sppwp-password-form',
			SPPWP_ASSETS_URL . 'css/password-form.css',
			array(),
			SPPWP_VERSION
		);
	}

	/**
	 * Start the PHP session if not already started
	 *
	 * @return void
	 */
	public function start_session() {
		if ( ! session_id() ) {
			session_start();
		}
	}

	/**
	 * Check if the current page needs protection
	 *
	 * Validates user authentication status, IP allowance,
	 * and password submission. Shows password form if needed.
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

		$is_authenticated = false;
		if ( isset( $_SESSION['sppwp_authenticated'] ) ) {
			$is_authenticated = wp_validate_boolean( sanitize_text_field( $_SESSION['sppwp_authenticated'] ) );
		}

		if ( $is_authenticated ) {
			return;
		}

		if ( ! empty( $_POST ) && check_admin_referer( 'sppwp_nonce_action', 'sppwp_nonce' ) ) {
			if ( isset( $_POST['sppwp_password'] ) ) {
				$submitted_password = sanitize_text_field( wp_unslash( $_POST['sppwp_password'] ) );
				if ( empty( $submitted_password ) ) {
					$this->error_message = esc_html__( 'Password is required.', 'smart-password-protect' );
				} elseif ( $submitted_password === $password ) {
					$_SESSION['sppwp_authenticated'] = true;
					return;
				} else {
					$this->error_message = esc_html__( 'Incorrect password. Please try again.', 'smart-password-protect' );
				}
			}
		}

		wp_enqueue_style( 'sppwp-password-form' );
		$this->show_password_form();
		exit;
	}

	/**
	 * Display the password protection form
	 *
	 * Includes the password form template file.
	 *
	 * @return void
	 */
	private function show_password_form() {
		include SPPWP_DIR . 'includes/templates/password-form.php';
	}
}
