<?php
/**
 * Helper functions for ContentCraft AI.
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin option.
 *
 * @param string $key     Option key.
 * @param mixed  $default Default value.
 * @return mixed Option value.
 */
function contentcraft_get_option( $key, $default = null ) {
	$settings = get_option( 'contentcraft_settings', array() );
	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Update plugin option.
 *
 * @param string $key   Option key.
 * @param mixed  $value Option value.
 * @return bool True on success.
 */
function contentcraft_update_option( $key, $value ) {
	$settings         = get_option( 'contentcraft_settings', array() );
	$settings[ $key ] = $value;
	return update_option( 'contentcraft_settings', $settings );
}

/**
 * Sanitize text input.
 *
 * @param string $text Text to sanitize.
 * @return string Sanitized text.
 */
function contentcraft_sanitize_text( $text ) {
	return sanitize_text_field( $text );
}

/**
 * Sanitize array recursively.
 *
 * @param array $array Array to sanitize.
 * @return array Sanitized array.
 */
function contentcraft_sanitize_array( $array ) {
	if ( ! is_array( $array ) ) {
		return array();
	}

	$sanitized = array();

	foreach ( $array as $key => $value ) {
		$key = sanitize_key( $key );

		if ( is_array( $value ) ) {
			$sanitized[ $key ] = contentcraft_sanitize_array( $value );
		} else {
			$sanitized[ $key ] = sanitize_text_field( $value );
		}
	}

	return $sanitized;
}

/**
 * Check if WooCommerce is active.
 *
 * @return bool True if WooCommerce is active.
 */
function contentcraft_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Check if current user can generate content.
 *
 * @return bool True if user has permission.
 */
function contentcraft_user_can_generate() {
	return current_user_can( 'edit_posts' );
}

/**
 * Check if current user can manage settings.
 *
 * @return bool True if user has permission.
 */
function contentcraft_user_can_manage_settings() {
	return current_user_can( 'manage_options' );
}

/**
 * Format token count for display.
 *
 * @param int $tokens Token count.
 * @return string Formatted token count.
 */
function contentcraft_format_tokens( $tokens ) {
	if ( $tokens >= 1000000 ) {
		return number_format( $tokens / 1000000, 2 ) . 'M';
	} elseif ( $tokens >= 1000 ) {
		return number_format( $tokens / 1000, 1 ) . 'K';
	}
	return number_format( $tokens );
}

/**
 * Get available AI providers.
 *
 * @return array Provider options.
 */
function contentcraft_get_providers() {
	return apply_filters(
		'contentcraft_providers',
		array(
			'openai'     => __( 'OpenAI (GPT-4, GPT-3.5)', 'contentcraft-ai' ),
			'anthropic'  => __( 'Anthropic (Claude)', 'contentcraft-ai' ),
			'ollama'     => __( 'Ollama (Local)', 'contentcraft-ai' ),
			'custom'     => __( 'Custom API', 'contentcraft-ai' ),
		)
	);
}

/**
 * Get available content tones.
 *
 * @return array Tone options.
 */
function contentcraft_get_tones() {
	return apply_filters(
		'contentcraft_tones',
		array(
			'professional'    => __( 'Professional', 'contentcraft-ai' ),
			'casual'          => __( 'Casual', 'contentcraft-ai' ),
			'friendly'        => __( 'Friendly', 'contentcraft-ai' ),
			'formal'          => __( 'Formal', 'contentcraft-ai' ),
			'conversational'  => __( 'Conversational', 'contentcraft-ai' ),
			'authoritative'   => __( 'Authoritative', 'contentcraft-ai' ),
			'enthusiastic'    => __( 'Enthusiastic', 'contentcraft-ai' ),
			'informative'     => __( 'Informative', 'contentcraft-ai' ),
			'persuasive'      => __( 'Persuasive', 'contentcraft-ai' ),
		)
	);
}

/**
 * Get available languages.
 *
 * @return array Language options.
 */
function contentcraft_get_languages() {
	return apply_filters(
		'contentcraft_languages',
		array(
			'en' => __( 'English', 'contentcraft-ai' ),
			'fa' => __( 'فارسی (Persian)', 'contentcraft-ai' ),
			'ar' => __( 'العربية (Arabic)', 'contentcraft-ai' ),
			'de' => __( 'Deutsch (German)', 'contentcraft-ai' ),
			'es' => __( 'Español (Spanish)', 'contentcraft-ai' ),
			'fr' => __( 'Français (French)', 'contentcraft-ai' ),
			'it' => __( 'Italiano (Italian)', 'contentcraft-ai' ),
			'ja' => __( '日本語 (Japanese)', 'contentcraft-ai' ),
			'ko' => __( '한국어 (Korean)', 'contentcraft-ai' ),
			'zh' => __( '中文 (Chinese)', 'contentcraft-ai' ),
		)
	);
}

/**
 * Get content length options.
 *
 * @return array Length options.
 */
function contentcraft_get_lengths() {
	return apply_filters(
		'contentcraft_lengths',
		array(
			'short'  => __( 'Short (300-500 words)', 'contentcraft-ai' ),
			'medium' => __( 'Medium (800-1200 words)', 'contentcraft-ai' ),
			'long'   => __( 'Long (1500-2500 words)', 'contentcraft-ai' ),
		)
	);
}

/**
 * Mask sensitive string (for displaying API keys).
 *
 * @param string $string String to mask.
 * @param int    $visible_chars Number of visible characters at start.
 * @return string Masked string.
 */
function contentcraft_mask_string( $string, $visible_chars = 4 ) {
	if ( empty( $string ) || strlen( $string ) <= $visible_chars ) {
		return $string;
	}

	$visible = substr( $string, 0, $visible_chars );
	$masked  = str_repeat( '•', strlen( $string ) - $visible_chars );

	return $visible . $masked;
}

/**
 * Check if plugin is properly configured.
 *
 * @return bool True if configured.
 */
function contentcraft_is_configured() {
	$api_url    = contentcraft_get_option( 'api_base_url' );
	$api_secret = contentcraft_get_option( 'api_secret' );

	return ! empty( $api_url ) && ! empty( $api_secret );
}

/**
 * Get error message by code.
 *
 * @param string $code Error code.
 * @return string Error message.
 */
function contentcraft_get_error_message( $code ) {
	$messages = array(
		'INVALID_REQUEST'        => __( 'Invalid request parameters.', 'contentcraft-ai' ),
		'UNAUTHORIZED'           => __( 'Authentication failed. Check your API secret.', 'contentcraft-ai' ),
		'RATE_LIMIT_EXCEEDED'    => __( 'Rate limit exceeded. Please try again later.', 'contentcraft-ai' ),
		'GENERATION_FAILED'      => __( 'Content generation failed. Please try again.', 'contentcraft-ai' ),
		'PROVIDER_ERROR'         => __( 'AI provider error. Check provider status.', 'contentcraft-ai' ),
		'TIMEOUT'                => __( 'Request timeout. Try with shorter content.', 'contentcraft-ai' ),
		'INSUFFICIENT_TOKENS'    => __( 'Insufficient tokens in your account.', 'contentcraft-ai' ),
		'CONNECTION_FAILED'      => __( 'Cannot connect to FastAPI backend.', 'contentcraft-ai' ),
	);

	return isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'An unknown error occurred.', 'contentcraft-ai' );
}

/**
 * Log debug message if WP_DEBUG is enabled.
 *
 * @param string $message Debug message.
 * @param mixed  $data    Additional data to log.
 */
function contentcraft_debug_log( $message, $data = null ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$log_message = '[ContentCraft AI] ' . $message;

		if ( ! is_null( $data ) ) {
			$log_message .= ' | Data: ' . print_r( $data, true );
		}

		error_log( $log_message );
	}
}


