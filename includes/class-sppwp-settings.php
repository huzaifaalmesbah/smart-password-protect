<?php
/**
 * Settings page for the Smart Password Protect plugin.
 *
 * This file contains the SPPWP_Settings class, which handles the admin settings
 * page for the Smart Password Protect plugin.
 *
 * @package SmartPasswordProtect
 */

/**
 * Class SPPWP_Settings
 *
 * Handles the admin settings page for the Smart Password Protect plugin.
 */
class SPPWP_Settings {

	/**
	 * Initialize the settings page.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add the admin menu item.
	 */
	public function add_admin_menu() {
		add_options_page(
			'Smart Password Protect',
			'Smart Password Protect',
			'manage_options',
			'sppwp-settings',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Initialize settings.
	 */
	public function settings_init() {
		register_setting( 'sppwp_settings', 'sppwp_options', array( $this, 'sanitize_settings' ) );

		// General Settings Section.
		add_settings_section(
			'sppwp_plugin_section_general',
			esc_html__( 'General Settings', 'smart-password-protect' ),
			null,
			'sppwp_settings_general'
		);

		add_settings_field(
			'sppwp_password',
			esc_html__( 'Password', 'smart-password-protect' ),
			array( $this, 'password_render' ),
			'sppwp_settings_general',
			'sppwp_plugin_section_general'
		);
		add_settings_field(
			'sppwp_enabled',
			esc_html__( 'Enable Protection', 'smart-password-protect' ),
			array( $this, 'enabled_render' ),
			'sppwp_settings_general',
			'sppwp_plugin_section_general'
		);
		// IP Settings Section.
		add_settings_section(
			'sppwp_plugin_section_ips',
			esc_html__( 'IP Settings', 'smart-password-protect' ),
			null,
			'sppwp_settings_ips'
		);

		add_settings_field(
			'sppwp_allowed_ips',
			esc_html__( 'Allowed IP Addresses', 'smart-password-protect' ),
			array( $this, 'allowed_ips_render' ),
			'sppwp_settings_ips',
			'sppwp_plugin_section_ips'
		);
	}

	/**
	 * Render the password field.
	 */
	public function password_render() {
		$options = get_option( 'sppwp_options' );
		?>
		<input type="text" name="sppwp_options[sppwp_password]" id="sppwp_password" value="<?php echo isset( $options['sppwp_password'] ) ? esc_attr( $options['sppwp_password'] ) : ''; ?>">
		<?php
	}

	/**
	 * Render the enabled checkbox.
	 */
	public function enabled_render() {
		$options = get_option( 'sppwp_options' );
		$enabled = isset( $options['sppwp_enabled'] ) ? $options['sppwp_enabled'] : 0;
		?>
		<label class="switch">
			<input type="checkbox" name="sppwp_options[sppwp_enabled]" value="1" id="sppwp_enabled" <?php checked( 1, $enabled ); ?>>
			<span class="slider round"></span>
		</label>
		<p class="description"><?php esc_html_e( 'Enter a password before enabling, otherwise it will not work.', 'smart-password-protect' ); ?></p>
		<?php
	}

	/**
	 * Render the allowed IPs field.
	 */
	public function allowed_ips_render() {
		$options     = get_option( 'sppwp_options' );
		$allowed_ips = isset( $options['sppwp_allowed_ips'] ) ? json_decode( $options['sppwp_allowed_ips'], true ) : array();
		?>
		<div id="ip-repeater">
		<div class="ip-field">
			<input type="text" id="new-ip" placeholder="Enter IP address">
			<button type="button" id="add-ip" class="button"><?php esc_html_e( 'Add IP', 'smart-password-protect' ); ?></button>
		</div>

			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Allowed IPs', 'smart-password-protect' ); ?></th>
						<th><?php esc_html_e( 'Remove', 'smart-password-protect' ); ?></th>
					</tr>
				</thead>
				<tbody id="allowed-ips-list">
					<?php
					if ( ! empty( $allowed_ips ) ) {
						foreach ( $allowed_ips as $ip ) {
							echo '<tr data-ip="' . esc_attr( $ip ) . '">
                                    <td>' . esc_html( $ip ) . '</td>
                                    <td><button type="button" class="remove-ip button">X</button></td>
                                  </tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="sppwp_options[sppwp_allowed_ips]" id="sppwp_allowed_ips" value="<?php echo esc_attr( wp_json_encode( $allowed_ips ) ); ?>">
		<?php
	}

	/**
	 * Render the options page.
	 */
	public function options_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="#general-settings" class="nav-tab nav-tab-active"><?php esc_html_e( 'General Settings', 'smart-password-protect' ); ?></a>
				<a href="#ip-settings" class="nav-tab"><?php esc_html_e( 'IP Settings', 'smart-password-protect' ); ?></a>
			</h2>

			<form action="options.php" method="post">
				<div id="general-settings" class="tab-content">
					<?php
					settings_fields( 'sppwp_settings' );
					do_settings_sections( 'sppwp_settings_general' );
					?>
				</div>

				<div id="ip-settings" class="tab-content" style="display:none;">
					<?php
					do_settings_sections( 'sppwp_settings_ips' );
					?>
				</div>

				<?php submit_button( 'Save Settings' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue admin styles.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'sppwp-admin', SPPWP_ASSETS_URL . 'css/sppwp-admin.css', array(), SPPWP_VERSION );
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'sppwp-script', SPPWP_ASSETS_URL . 'js/sppwp-script.js', array( 'jquery' ), SPPWP_VERSION, true );

		// Pass IPs to JS.
		$options     = get_option( 'sppwp_options' );
		$allowed_ips = isset( $options['sppwp_allowed_ips'] ) ? json_decode( $options['sppwp_allowed_ips'], true ) : array();

		wp_localize_script(
			'sppwp-script',
			'SPPWP_Data',
			array(
				'allowed_ips' => $allowed_ips,
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $options The submitted options.
	 *
	 * @return array The sanitized options.
	 */
	public function sanitize_settings( $options ) {
		// Validation for password when enabling protection.
		if ( isset( $options['sppwp_enabled'] ) && '1' === $options['sppwp_enabled'] ) {
			if ( empty( $options['sppwp_password'] ) ) {
				add_settings_error( 'sppwp_options', 'password_error', esc_html__( 'Password is required to enable protection.', 'smart-password-protect' ), 'error' );
				unset( $options['sppwp_enabled'] );  // Do not save 'enabled' if no password is provided.
			}
		} else {
			$options['sppwp_enabled'] = '0';
		}

		// Validation for allowed IP addresses.
		if ( isset( $options['sppwp_allowed_ips'] ) ) {
			$allowed_ips = json_decode( $options['sppwp_allowed_ips'], true );

			if ( is_array( $allowed_ips ) ) {
				$validated_ips = array();

				foreach ( $allowed_ips as $ip ) {
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
						$validated_ips[] = $ip;
					} else {
						// Translators: %s is the IP address.
						add_settings_error( 'sppwp_options', 'ip_error', sprintf( esc_html__( 'Invalid IP address: %s', 'smart-password-protect' ), esc_html( $ip ) ), 'error' );
					}
				}

				// Re-save only validated IPs.
				$options['sppwp_allowed_ips'] = wp_json_encode( $validated_ips );
			}
		}

		return $options;
	}
}
