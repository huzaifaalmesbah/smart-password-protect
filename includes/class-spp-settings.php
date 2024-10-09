<?php
/**
 * Settings page for the Smart Password Protect plugin.
 *
 * This file contains the SPP_Settings class, which handles the admin settings
 * page for the Smart Password Protect plugin.
 *
 * @package SmartPasswordProtect
 */

/**
 * Class SPP_Settings
 *
 * Handles the admin settings page for the Smart Password Protect plugin.
 */
class SPP_Settings {

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
			'spp-settings',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Initialize settings.
	 */
	public function settings_init() {
		register_setting( 'spp_settings', 'spp_options' );

		// General Settings Section.
		add_settings_section(
			'spp_plugin_section_general',
			esc_html__( 'General Settings', 'smart-password-protect' ),
			null,
			'spp_settings_general'
		);

		add_settings_field(
			'spp_enabled',
			esc_html__( 'Enable Protection', 'smart-password-protect' ),
			array( $this, 'enabled_render' ),
			'spp_settings_general',
			'spp_plugin_section_general'
		);

		add_settings_field(
			'spp_password',
			esc_html__( 'Password', 'smart-password-protect' ),
			array( $this, 'password_render' ),
			'spp_settings_general',
			'spp_plugin_section_general'
		);

		// IP Settings Section.
		add_settings_section(
			'spp_plugin_section_ips',
			esc_html__( 'IP Settings', 'smart-password-protect' ),
			null,
			'spp_settings_ips'
		);

		add_settings_field(
			'spp_allowed_ips',
			esc_html__( 'Allowed IP Addresses', 'smart-password-protect' ),
			array( $this, 'allowed_ips_render' ),
			'spp_settings_ips',
			'spp_plugin_section_ips'
		);
	}

	/**
	 * Render the enabled checkbox.
	 */
	public function enabled_render() {
		$options = get_option( 'spp_options' );
		$enabled = isset( $options['spp_enabled'] ) ? $options['spp_enabled'] : 0;
		?>
		<label class="switch">
			<input type="checkbox" name="spp_options[spp_enabled]" value="1" <?php checked( 1, $enabled ); ?>>
			<span class="slider round"></span>
		</label>
		<?php
	}

	/**
	 * Render the password field.
	 */
	public function password_render() {
		$options = get_option( 'spp_options' );
		?>
		<input type="text" name="spp_options[spp_password]" value="<?php echo isset( $options['spp_password'] ) ? esc_attr( $options['spp_password'] ) : ''; ?>">
		<?php
	}

	/**
	 * Render the allowed IPs field.
	 */
	public function allowed_ips_render() {
		$options     = get_option( 'spp_options' );
		$allowed_ips = isset( $options['spp_allowed_ips'] ) ? json_decode( $options['spp_allowed_ips'], true ) : array();
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
		<input type="hidden" name="spp_options[spp_allowed_ips]" id="spp_allowed_ips" value="<?php echo esc_attr( wp_json_encode( $allowed_ips ) ); ?>">
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
					settings_fields( 'spp_settings' );
					do_settings_sections( 'spp_settings_general' );
					?>
				</div>

				<div id="ip-settings" class="tab-content" style="display:none;">
					<?php
					do_settings_sections( 'spp_settings_ips' );
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
		wp_enqueue_style( 'spp-admin', SPP_ASSETS_URL . 'css/spp-admin.css', array(), SPP_VERSION );
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'spp-script', SPP_ASSETS_URL . 'js/spp-script.js', array( 'jquery' ), SPP_VERSION, true );

		// Pass IPs to JS.
		$options     = get_option( 'spp_options' );
		$allowed_ips = isset( $options['spp_allowed_ips'] ) ? json_decode( $options['spp_allowed_ips'], true ) : array();

		wp_localize_script(
			'spp-script',
			'SPP_Data',
			array(
				'allowed_ips' => $allowed_ips,
			)
		);
	}
}
