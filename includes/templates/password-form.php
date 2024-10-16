<?php
/**
 * Password Form Template
 *
 * @package SmartPasswordProtect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex,nofollow">
	<meta name="googlebot" content="noindex,nofollow">
	<title><?php esc_html_e( 'Password Protected', 'smart-password-protect' ); ?></title>
	<?php wp_head(); ?>
</head>
<body class="sppwp-body">
	<div class="sppwp-container">
		<form method="post" class="sppwp-password-form">
			<h2><?php esc_html_e( 'Password Required', 'smart-password-protect' ); ?></h2>
			<p><?php esc_html_e( 'Please enter the website password to access:', 'smart-password-protect' ); ?></p>
			<input type="password" name="sppwp_password" required placeholder="<?php esc_attr_e( 'Enter your password', 'smart-password-protect' ); ?>">
			<div class="sppwp-remember-me">
				<input type="checkbox" name="sppwp_remember_me" id="sppwp_remember_me" value="1">
				<label for="sppwp_remember_me"><?php esc_html_e( 'Remember Me', 'smart-password-protect' ); ?></label>
			</div>
			<?php wp_nonce_field( 'sppwp_nonce_action', 'sppwp_nonce' ); ?>
			<input type="submit" value="<?php esc_attr_e( 'Login', 'smart-password-protect' ); ?>">
			<?php if ( ! empty( $this->error_message ) ) : ?>
				<p class="sppwp-password-error"><?php echo esc_html( $this->error_message ); ?></p>
			<?php endif; ?>
		</form>
	</div>
	<?php wp_footer(); ?>
</body>
</html>