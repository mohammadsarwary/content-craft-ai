<?php
/**
 * Admin class for ContentCraft AI.
 *
 * Handles admin interface, menus, and settings.
 *
 * @package ContentCraft_AI
 */

namespace ContentCraft;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class.
 */
class Admin {

	/**
	 * Single instance.
	 *
	 * @var Admin
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Admin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_contentcraft_test_connection', array( $this, 'ajax_test_connection' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_menu() {
		// Main menu.
		add_menu_page(
			__( 'ContentCraft AI', 'contentcraft-ai' ),
			__( 'ContentCraft AI', 'contentcraft-ai' ),
			'edit_posts',
			'contentcraft',
			array( $this, 'render_dashboard' ),
			'dashicons-edit-large',
			30
		);

		// Dashboard.
		add_submenu_page(
			'contentcraft',
			__( 'Dashboard', 'contentcraft-ai' ),
			__( 'Dashboard', 'contentcraft-ai' ),
			'edit_posts',
			'contentcraft',
			array( $this, 'render_dashboard' )
		);

		// Content Studio.
		add_submenu_page(
			'contentcraft',
			__( 'Content Studio', 'contentcraft-ai' ),
			__( 'Content Studio', 'contentcraft-ai' ),
			'edit_posts',
			'contentcraft-content-studio',
			array( $this, 'render_content_studio' )
		);

		// Bulk Generator.
		add_submenu_page(
			'contentcraft',
			__( 'Bulk Generator', 'contentcraft-ai' ),
			__( 'Bulk Generator', 'contentcraft-ai' ),
			'edit_posts',
			'contentcraft-bulk',
			array( $this, 'render_bulk_generator' )
		);

		// Brand Voice.
		add_submenu_page(
			'contentcraft',
			__( 'Brand Voice', 'contentcraft-ai' ),
			__( 'Brand Voice', 'contentcraft-ai' ),
			'edit_posts',
			'contentcraft-brand',
			array( $this, 'render_brand_voice' )
		);

		// Settings.
		add_submenu_page(
			'contentcraft',
			__( 'Settings', 'contentcraft-ai' ),
			__( 'Settings', 'contentcraft-ai' ),
			'manage_options',
			'contentcraft-settings',
			array( $this, 'render_settings' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		// Only load on ContentCraft pages.
		if ( strpos( $hook, 'contentcraft' ) === false ) {
			return;
		}

		// CSS.
		wp_enqueue_style(
			'contentcraft-admin',
			CONTENTCRAFT_PLUGIN_URL . 'admin/assets/css/admin.css',
			array(),
			CONTENTCRAFT_VERSION
		);

		// JS.
		wp_enqueue_script(
			'contentcraft-admin',
			CONTENTCRAFT_PLUGIN_URL . 'admin/assets/js/admin.js',
			array( 'jquery', 'wp-api' ),
			CONTENTCRAFT_VERSION,
			true
		);

		// Localize script.
		wp_localize_script(
			'contentcraft-admin',
			'contentcraftAdmin',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'restUrl'    => rest_url( 'contentcraft/v1' ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'ajaxNonce'  => wp_create_nonce( 'contentcraft_ajax' ),
				'strings'    => array(
					'generating'         => __( 'Generating content...', 'contentcraft-ai' ),
					'success'            => __( 'Content generated successfully!', 'contentcraft-ai' ),
					'error'              => __( 'An error occurred. Please try again.', 'contentcraft-ai' ),
					'confirmOverwrite'   => __( 'This will overwrite existing content. Continue?', 'contentcraft-ai' ),
					'testing'            => __( 'Testing connection...', 'contentcraft-ai' ),
					'connected'          => __( 'Connected successfully!', 'contentcraft-ai' ),
					'connectionFailed'   => __( 'Connection failed.', 'contentcraft-ai' ),
				),
				'tones'      => contentcraft_get_tones(),
				'languages'  => contentcraft_get_languages(),
				'lengths'    => contentcraft_get_lengths(),
			)
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			'contentcraft_settings_group',
			'contentcraft_settings',
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		// General Settings Section.
		add_settings_section(
			'contentcraft_general_section',
			__( 'General Settings', 'contentcraft-ai' ),
			array( $this, 'render_general_section' ),
			'contentcraft-settings'
		);

		// Provider field.
		add_settings_field(
			'provider',
			__( 'AI Provider', 'contentcraft-ai' ),
			array( $this, 'render_provider_field' ),
			'contentcraft-settings',
			'contentcraft_general_section'
		);

		// API Base URL field.
		add_settings_field(
			'api_base_url',
			__( 'FastAPI Base URL', 'contentcraft-ai' ),
			array( $this, 'render_api_url_field' ),
			'contentcraft-settings',
			'contentcraft_general_section'
		);

		// API Secret field.
		add_settings_field(
			'api_secret',
			__( 'API Secret', 'contentcraft-ai' ),
			array( $this, 'render_api_secret_field' ),
			'contentcraft-settings',
			'contentcraft_general_section'
		);

		// Model Name field.
		add_settings_field(
			'model_name',
			__( 'Model Name', 'contentcraft-ai' ),
			array( $this, 'render_model_name_field' ),
			'contentcraft-settings',
			'contentcraft_general_section'
		);

		// Default Tone field.
		add_settings_field(
			'default_tone',
			__( 'Default Tone', 'contentcraft-ai' ),
			array( $this, 'render_default_tone_field' ),
			'contentcraft-settings',
			'contentcraft_general_section'
		);

		// Default Language field.
		add_settings_field(
			'default_language',
			__( 'Default Language', 'contentcraft-ai' ),
			array( $this, 'render_default_language_field' ),
			'contentcraft-settings',
			'contentcraft_general_section'
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Settings input.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		if ( isset( $input['provider'] ) ) {
			$sanitized['provider'] = sanitize_text_field( $input['provider'] );
		}

		if ( isset( $input['api_base_url'] ) ) {
			$sanitized['api_base_url'] = esc_url_raw( $input['api_base_url'] );
		}

		if ( isset( $input['api_secret'] ) ) {
			$sanitized['api_secret'] = sanitize_text_field( $input['api_secret'] );
		}

		if ( isset( $input['model_name'] ) ) {
			$sanitized['model_name'] = sanitize_text_field( $input['model_name'] );
		}

		if ( isset( $input['default_tone'] ) ) {
			$sanitized['default_tone'] = sanitize_text_field( $input['default_tone'] );
		}

		if ( isset( $input['default_language'] ) ) {
			$sanitized['default_language'] = sanitize_text_field( $input['default_language'] );
		}

		if ( isset( $input['rate_limit'] ) ) {
			$sanitized['rate_limit'] = absint( $input['rate_limit'] );
		}

		if ( isset( $input['cache_ttl'] ) ) {
			$sanitized['cache_ttl'] = absint( $input['cache_ttl'] );
		}

		if ( isset( $input['seo_options'] ) && is_array( $input['seo_options'] ) ) {
			$sanitized['seo_options'] = contentcraft_sanitize_array( $input['seo_options'] );
		}

		if ( isset( $input['brand_profile'] ) && is_array( $input['brand_profile'] ) ) {
			$sanitized['brand_profile'] = contentcraft_sanitize_array( $input['brand_profile'] );
		}

		return $sanitized;
	}

	/**
	 * Render general section description.
	 */
	public function render_general_section() {
		echo '<p>' . esc_html__( 'Configure your AI provider and connection settings.', 'contentcraft-ai' ) . '</p>';
	}

	/**
	 * Render provider field.
	 */
	public function render_provider_field() {
		$value     = contentcraft_get_option( 'provider', 'openai' );
		$providers = contentcraft_get_providers();
		?>
		<select name="contentcraft_settings[provider]" id="provider">
			<?php foreach ( $providers as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description">
			<?php esc_html_e( 'Select your AI provider. Make sure to configure API keys in FastAPI backend.', 'contentcraft-ai' ); ?>
		</p>
		<?php
	}

	/**
	 * Render API URL field.
	 */
	public function render_api_url_field() {
		$value = contentcraft_get_option( 'api_base_url', 'http://localhost:8000' );
		?>
		<input type="url" name="contentcraft_settings[api_base_url]" id="api_base_url" value="<?php echo esc_attr( $value ); ?>" class="regular-text" required />
		<p class="description">
			<?php esc_html_e( 'FastAPI backend URL (e.g., http://localhost:8000 or https://api.yourdomain.com)', 'contentcraft-ai' ); ?>
		</p>
		<?php
	}

	/**
	 * Render API secret field.
	 */
	public function render_api_secret_field() {
		$value = contentcraft_get_option( 'api_secret', '' );
		?>
		<input type="password" name="contentcraft_settings[api_secret]" id="api_secret" value="<?php echo esc_attr( $value ); ?>" class="regular-text" required />
		<p class="description">
			<?php esc_html_e( 'Shared secret token for authentication (set in FastAPI backend APP_SECRET).', 'contentcraft-ai' ); ?>
		</p>
		<button type="button" class="button" id="test-connection">
			<?php esc_html_e( 'Test Connection', 'contentcraft-ai' ); ?>
		</button>
		<span id="connection-status"></span>
		<?php
	}

	/**
	 * Render model name field.
	 */
	public function render_model_name_field() {
		$value = contentcraft_get_option( 'model_name', 'gpt-4o-mini' );
		?>
		<input type="text" name="contentcraft_settings[model_name]" id="model_name" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'Model name (e.g., gpt-4o-mini, claude-3-sonnet, llama2).', 'contentcraft-ai' ); ?>
		</p>
		<?php
	}

	/**
	 * Render default tone field.
	 */
	public function render_default_tone_field() {
		$value = contentcraft_get_option( 'default_tone', 'professional' );
		$tones = contentcraft_get_tones();
		?>
		<select name="contentcraft_settings[default_tone]" id="default_tone">
			<?php foreach ( $tones as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render default language field.
	 */
	public function render_default_language_field() {
		$value     = contentcraft_get_option( 'default_language', 'en' );
		$languages = contentcraft_get_languages();
		?>
		<select name="contentcraft_settings[default_language]" id="default_language">
			<?php foreach ( $languages as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * AJAX handler for test connection.
	 */
	public function ajax_test_connection() {
		check_ajax_referer( 'contentcraft_ajax', 'nonce' );

		if ( ! contentcraft_user_can_manage_settings() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'contentcraft-ai' ) ) );
		}

		$result = API_Client::instance()->test_connection();

		if ( $result['connected'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * Render dashboard page.
	 */
	public function render_dashboard() {
		include CONTENTCRAFT_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Render content studio page.
	 */
	public function render_content_studio() {
		include CONTENTCRAFT_PLUGIN_DIR . 'admin/views/content-studio.php';
	}

	/**
	 * Render bulk generator page.
	 */
	public function render_bulk_generator() {
		include CONTENTCRAFT_PLUGIN_DIR . 'admin/views/bulk-generator.php';
	}

	/**
	 * Render brand voice page.
	 */
	public function render_brand_voice() {
		include CONTENTCRAFT_PLUGIN_DIR . 'admin/views/brand-voice.php';
	}

	/**
	 * Render settings page.
	 */
	public function render_settings() {
		include CONTENTCRAFT_PLUGIN_DIR . 'admin/views/settings.php';
	}
}


