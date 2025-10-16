<?php
/**
 * Autoloader for ContentCraft AI plugin.
 *
 * PSR-4 autoloader implementation.
 *
 * @package ContentCraft_AI
 */

namespace ContentCraft;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Namespace prefix.
	 *
	 * @var string
	 */
	private static $prefix = 'ContentCraft\\';

	/**
	 * Base directory.
	 *
	 * @var string
	 */
	private static $base_dir;

	/**
	 * Register autoloader.
	 */
	public static function register() {
		self::$base_dir = CONTENTCRAFT_PLUGIN_DIR . 'includes/';

		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class The fully-qualified class name.
	 */
	public static function autoload( $class ) {
		// Check if the class uses the namespace prefix.
		$len = strlen( self::$prefix );
		if ( strncmp( self::$prefix, $class, $len ) !== 0 ) {
			return;
		}

		// Get the relative class name.
		$relative_class = substr( $class, $len );

		// Convert namespace to file path.
		// Replace namespace separators with directory separators.
		$relative_class = str_replace( '\\', '/', $relative_class );

		// Convert class name to WordPress file naming convention.
		// Example: Admin_Settings -> class-admin-settings.php
		$file_name = 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) . '.php';

		// Build the full file path.
		$file = self::$base_dir . $file_name;

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}


