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

		// General Settings Section
		$this->add_settings_section( 'general' );

		// IP Settings Section
		$this->add_settings_section( 'ips' );
	}

	/**
	 * Add the settings section and fields for a specific tab.
	 *
	 * @param string $tab The tab name ('general' or 'ips').
	 */
	protected function add_settings_section( $tab ) {
		if ( 'general' === $tab ) {
			add_settings_section(
				'sppwp_plugin_section_general',
				esc_html__( 'General Settings', 'smart-password-protect' ),
				null,
				'sppwp_settings_general'
			);

			$this->add_settings_fields( 'general' );
		} elseif ( 'ips' === $tab ) {
			add_settings_section(
				'sppwp_plugin_section_ips',
				esc_html__( 'IP Settings', 'smart-password-protect' ),
				null,
				'sppwp_settings_ips'
			);

			$this->add_settings_fields( 'ips' );
		}
	}

	/**
	 * Add the settings fields for a specific tab.
	 *
	 * @param string $tab The tab name ('general' or 'ips').
	 */
	protected function add_settings_fields( $tab ) {
		if ( 'general' === $tab ) {
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

			add_settings_field(
				'sppwp_remember_me',
				esc_html__( 'Remember Me (Days)', 'smart-password-protect' ),
				array( $this, 'remember_me_render' ),
				'sppwp_settings_general',
				'sppwp_plugin_section_general'
			);
		} elseif ( 'ips' === $tab ) {
			add_settings_field(
				'sppwp_allowed_ips',
				esc_html__( 'Allowed IP Addresses', 'smart-password-protect' ),
				array( $this, 'allowed_ips_render' ),
				'sppwp_settings_ips',
				'sppwp_plugin_section_ips'
			);
		}
	}

	/**
	 * Render the options page with modified form structure.
	 */
	public function options_page() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		?>
		<div class="wrap sppwp-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="?page=sppwp-settings&tab=general" class="nav-tab <?php echo 'general' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'General Settings', 'smart-password-protect' ); ?>
				</a>
				<a href="?page=sppwp-settings&tab=ips" class="nav-tab <?php echo 'ips' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'IP Settings', 'smart-password-protect' ); ?>
				</a>
			</h2>
			<div class="sppwp-settings-container">
				<form action="options.php" method="post" class="sppwp-form">
					<?php
					settings_fields( 'sppwp_settings' );

					// Always output both sections, but hide inactive one with CSS.
					?>
					<div class="sppwp-tab-content" id="general-settings" style="<?php echo 'general' === $active_tab ? 'display:block;' : 'display:none;'; ?>">
						<?php do_settings_sections( 'sppwp_settings_general' ); ?>
					</div>
					
					<div class="sppwp-tab-content" id="ip-settings" style="<?php echo 'ips' === $active_tab ? 'display:block;' : 'display:none;'; ?>">
						<?php do_settings_sections( 'sppwp_settings_ips' ); ?>
					</div>
					
					<?php
					// Hidden fields to preserve data from inactive tab
					$options = get_option( 'sppwp_options' );
					if ( 'general' === $active_tab ) {
						// Preserve IP settings
						$allowed_ips = isset( $options['sppwp_allowed_ips'] ) ? $options['sppwp_allowed_ips'] : '[]';
						echo '<input type="hidden" name="sppwp_options[sppwp_allowed_ips]" value="' . esc_attr( $allowed_ips ) . '">';
					} else {
						// Preserve general settings
						$password    = isset( $options['sppwp_password'] ) ? $options['sppwp_password'] : '';
						$enabled     = isset( $options['sppwp_enabled'] ) ? $options['sppwp_enabled'] : '0';
						$remember_me = isset( $options['sppwp_remember_me'] ) ? $options['sppwp_remember_me'] : '7';

						echo '<input type="hidden" name="sppwp_options[sppwp_password]" value="' . esc_attr( $password ) . '">';
						echo '<input type="hidden" name="sppwp_options[sppwp_enabled]" value="' . esc_attr( $enabled ) . '">';
						echo '<input type="hidden" name="sppwp_options[sppwp_remember_me]" value="' . esc_attr( $remember_me ) . '">';
					}
					?>
					
					<?php submit_button( 'Save Settings', 'button button-primary' ); ?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the password field.
	 */
	public function password_render() {
		$options = get_option( 'sppwp_options' );
		?>
		<input type="text" 
				name="sppwp_options[sppwp_password]" 
				id="sppwp_password" 
				class="sppwp-input" 
				value="<?php echo isset( $options['sppwp_password'] ) ? esc_attr( $options['sppwp_password'] ) : ''; ?>">
		<?php
	}

	/**
	 * Render the enabled checkbox.
	 */
	public function enabled_render() {
		$options = get_option( 'sppwp_options' );
		$enabled = isset( $options['sppwp_enabled'] ) ? $options['sppwp_enabled'] : 0;
		?>
		<label class="sppwp-switch">
			<input type="checkbox" 
					name="sppwp_options[sppwp_enabled]" 
					value="1" 
					id="sppwp_enabled" 
					<?php checked( 1, $enabled ); ?>>
			<span class="sppwp-slider"></span>
		</label>
		<p class="sppwp-description"><?php esc_html_e( 'Enter a password before enabling, otherwise it will not work.', 'smart-password-protect' ); ?></p>
		<?php
	}

	/**
	 * Render the remember me days field.
	 */
	public function remember_me_render() {
		$options = get_option( 'sppwp_options' );
		$days    = isset( $options['sppwp_remember_me'] ) ? intval( $options['sppwp_remember_me'] ) : 7;
		?>
		<input type="number" 
				name="sppwp_options[sppwp_remember_me]" 
				id="sppwp_remember_me" 
				class="sppwp-input" 
				value="<?php echo esc_attr( $days ); ?>" 
				min="1">
		<p class="sppwp-description"><?php esc_html_e( 'Number of days to remember the user\'s authentication.', 'smart-password-protect' ); ?></p>
		<?php
	}

	/**
	 * Render the allowed IPs field.
	 */
	public function allowed_ips_render() {
		$options     = get_option( 'sppwp_options' );
		$allowed_ips = isset( $options['sppwp_allowed_ips'] ) ? json_decode( $options['sppwp_allowed_ips'], true ) : array();
		?>
		<div id="ip-repeater" class="sppwp-ip-repeater">
			<div class="sppwp-ip-field">
				<input type="text" 
						id="new-ip" 
						class="sppwp-input" 
						placeholder="<?php esc_attr_e( 'Enter IP address', 'smart-password-protect' ); ?>">
				<button type="button" 
						id="add-ip" 
						class="sppwp-button sppwp-button-primary">
					<?php esc_html_e( 'Add IP', 'smart-password-protect' ); ?>
				</button>
			</div>

			<table class="sppwp-ip-table">
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
                                    <td>
                                        <button type="button" class="remove-ip sppwp-button sppwp-button-danger">X</button>
                                    </td>
                                  </tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" 
				name="sppwp_options[sppwp_allowed_ips]" 
				id="sppwp_allowed_ips" 
				value="<?php echo esc_attr( wp_json_encode( $allowed_ips ) ); ?>">
		<?php
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_styles( $hook ) {
		if ( 'settings_page_sppwp-settings' !== $hook ) {
			return;
		}
		wp_enqueue_style(
			'sppwp-admin',
			SPPWP_ASSETS_URL . 'css/sppwp-admin.css',
			array(),
			SPPWP_VERSION
		);
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'settings_page_sppwp-settings' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'sppwp-admin',
			SPPWP_ASSETS_URL . 'js/sppwp-admin.js',
			array( 'jquery' ),
			SPPWP_VERSION,
			true
		);

		$options     = get_option( 'sppwp_options' );
		$allowed_ips = isset( $options['sppwp_allowed_ips'] ) ? json_decode( $options['sppwp_allowed_ips'], true ) : array();

		wp_localize_script(
			'sppwp-admin',
			'SPPWP_Data',
			array(
				'allowed_ips' => $allowed_ips,
				'nonce'       => wp_create_nonce( 'sppwp_nonce' ),
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'messages'    => array(
					'invalid_ip' => esc_html__( 'Please enter a valid IP address.', 'smart-password-protect' ),
					'ip_exists'  => esc_html__( 'This IP address already exists in the list.', 'smart-password-protect' ),
				),
			)
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $options The submitted options.
	 * @return array The sanitized options.
	 */
	public function sanitize_settings( $options ) {
		// Validation for password when enabling protection
		if ( isset( $options['sppwp_enabled'] ) && '1' === $options['sppwp_enabled'] ) {
			if ( empty( $options['sppwp_password'] ) ) {
				add_settings_error(
					'sppwp_options',
					'password_error',
					esc_html__( 'Password is required to enable protection.', 'smart-password-protect' ),
					'error'
				);
				unset( $options['sppwp_enabled'] );
			}
		} else {
			$options['sppwp_enabled'] = '0';
		}

		// Validation for allowed IP addresses
		if ( isset( $options['sppwp_allowed_ips'] ) ) {
			$allowed_ips = json_decode( $options['sppwp_allowed_ips'], true );

			if ( is_array( $allowed_ips ) ) {
				$validated_ips = array();

				foreach ( $allowed_ips as $ip ) {
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
						$validated_ips[] = $ip;
					} else {
						add_settings_error(
							'sppwp_options',
							'ip_error',
							sprintf(
								/* translators: %s: IP address that failed validation */
								esc_html__( 'Invalid IP address: %s', 'smart-password-protect' ),
								esc_html( $ip )
							),
							'error'
						);
					}
				}

				$options['sppwp_allowed_ips'] = wp_json_encode( $validated_ips );
			}
		}

		// Sanitize remember me days
		if ( isset( $options['sppwp_remember_me'] ) ) {
			$options['sppwp_remember_me'] = max( 1, intval( $options['sppwp_remember_me'] ) );
		}

		return $options;
	}
}