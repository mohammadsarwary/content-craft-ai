<?php
/**
 * Plugin Name:       ContentCraft AI
 * Plugin URI:        https://github.com/contentcraft-ai/contentcraft-ai
 * Description:       AI-powered content generation for WordPress posts, pages, and WooCommerce products. Generate SEO-optimized content, product descriptions, alt-text, and more using OpenAI, Anthropic, or Ollama.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            ContentCraft AI Team
 * Author URI:        https://contentcraft.ai
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       contentcraft-ai
 * Domain Path:       /languages
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'CONTENTCRAFT_VERSION', '1.0.0' );
define( 'CONTENTCRAFT_PLUGIN_FILE', __FILE__ );
define( 'CONTENTCRAFT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTENTCRAFT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONTENTCRAFT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Check minimum requirements.
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	add_action( 'admin_notices', 'contentcraft_php_version_notice' );
	return;
}

global $wp_version;
if ( version_compare( $wp_version, '5.8', '<' ) ) {
	add_action( 'admin_notices', 'contentcraft_wp_version_notice' );
	return;
}

/**
 * Display admin notice for PHP version requirement.
 */
function contentcraft_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: Required PHP version */
				esc_html__( 'ContentCraft AI requires PHP version %s or higher. Please upgrade PHP.', 'contentcraft-ai' ),
				'7.4'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Display admin notice for WordPress version requirement.
 */
function contentcraft_wp_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: Required WordPress version */
				esc_html__( 'ContentCraft AI requires WordPress version %s or higher. Please upgrade WordPress.', 'contentcraft-ai' ),
				'5.8'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Load the autoloader.
 */
require_once CONTENTCRAFT_PLUGIN_DIR . 'includes/class-autoloader.php';

// Register autoloader.
ContentCraft\Autoloader::register();

/**
 * Main plugin class initialization.
 */
final class ContentCraft_AI {

	/**
	 * Single instance of the class.
	 *
	 * @var ContentCraft_AI
	 */
	private static $instance = null;

	/**
	 * Get single instance.
	 *
	 * @return ContentCraft_AI
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
		$this->init_hooks();
		$this->init_components();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		// Load plugin text domain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Activation and deactivation hooks.
		register_activation_hook( CONTENTCRAFT_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( CONTENTCRAFT_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize plugin components.
	 */
	private function init_components() {
		// Admin interface.
		if ( is_admin() ) {
			ContentCraft\Admin::instance();
		}

		// REST API.
		ContentCraft\REST_API::instance();

		// Logger.
		ContentCraft\Logger::instance();

		// WooCommerce integration (if WooCommerce is active).
		if ( class_exists( 'WooCommerce' ) ) {
			ContentCraft\WooCommerce_Integration::instance();
		}

		// Load hooks.
		require_once CONTENTCRAFT_PLUGIN_DIR . 'includes/hooks.php';
	}

	/**
	 * Load plugin text domain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'contentcraft-ai',
			false,
			dirname( CONTENTCRAFT_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Plugin activation.
	 */
	public function activate() {
		// Create custom database table for logs.
		ContentCraft\Logger::create_table();

		// Set default options.
		$defaults = array(
			'provider'         => 'openai',
			'api_base_url'     => 'http://localhost:8000',
			'api_secret'       => '',
			'model_name'       => 'gpt-4o-mini',
			'default_tone'     => 'professional',
			'default_language' => 'en',
			'seo_options'      => array(
				'enable_schema'          => true,
				'suggest_internal_links' => true,
				'auto_alt_text'          => false,
			),
			'rate_limit'       => 60,
			'cache_ttl'        => 600,
			'brand_profile'    => array(
				'enabled' => false,
			),
		);

		if ( ! get_option( 'contentcraft_settings' ) ) {
			add_option( 'contentcraft_settings', $defaults, '', 'no' );
		}

		// Flush rewrite rules.
		flush_rewrite_rules();

		// Set activation flag for redirect to settings.
		set_transient( 'contentcraft_activation_redirect', true, 30 );
	}

	/**
	 * Plugin deactivation.
	 */
	public function deactivate() {
		// Flush rewrite rules.
		flush_rewrite_rules();

		// Clear scheduled cron jobs.
		wp_clear_scheduled_hook( 'contentcraft_scheduled_generation' );
	}
}

/**
 * Initialize the plugin.
 */
function contentcraft() {
	return ContentCraft_AI::instance();
}

// Start the plugin.
contentcraft();


