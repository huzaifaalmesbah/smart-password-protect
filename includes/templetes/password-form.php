<?php
/**
 * Password Form Template for Smart Password Protect Plugin
 *
 * This file displays the password form for users to gain access
 * to the protected content. It includes fields for password entry
 * and displays any error messages if authentication fails.
 *
 * @package SmartPasswordProtect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<style>
	.spp-password-form {
		max-width: 400px;
		margin: 50px auto;
		padding: 20px;
		border: 1px solid #ccc;
		border-radius: 5px;
		background-color: #fff;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	}

	.spp-password-form h2 {
		margin-bottom: 20px;
		font-size: 24px;
		color: #333;
		text-align: center;
	}

	.spp-password-form p {
		margin-bottom: 15px;
		color: #555;
		text-align: center;
	}

	.spp-password-form input[type="password"],
	.spp-password-form input[type="submit"] {
		width: 100%;
		padding: 10px;
		margin-top: 10px;
		border: 1px solid #ccc;
		border-radius: 3px;
		box-sizing: border-box;
		font-size: 16px;
	}

	.spp-password-form input[type="password"]:focus,
	.spp-password-form input[type="submit"]:hover {
		border-color: #0073aa;
		outline: none;
	}

	.spp-password-form input[type="submit"] {
		background-color: #0073aa;
		color: white;
		cursor: pointer;
		transition: background-color 0.3s;
	}

	.spp-password-form input[type="submit"]:hover {
		background-color: #005177;
	}

	.spp-password-form .spp-password-error {
		color: #ff0000;
		text-align: center;
		margin-top: 10px;
	}
</style>

<form method="post" class="spp-password-form">
	<h2><?php esc_html_e( 'Password Required', 'smart-password-protect' ); ?></h2>
	<p><?php esc_html_e( 'Please enter the website password to access:', 'smart-password-protect' ); ?></p>
	<input type="password" name="spp_password" required placeholder="<?php esc_attr_e( 'Enter your password', 'smart-password-protect' ); ?>">
	<?php wp_nonce_field( 'spp_nonce_action', 'spp_nonce' ); ?>
	<input type="submit" value="<?php esc_attr_e( 'Login', 'smart-password-protect' ); ?>">
	<?php if ( ! empty( $this->error_message ) ) : ?>
		<p class="spp-password-error"><?php echo esc_html( $this->error_message ); ?></p>
	<?php endif; ?>
</form>
