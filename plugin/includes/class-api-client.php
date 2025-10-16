<?php
/**
 * API Client for communicating with FastAPI backend.
 *
 * @package ContentCraft_AI
 */

namespace ContentCraft;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API Client class.
 */
class API_Client {

	/**
	 * Single instance.
	 *
	 * @var API_Client
	 */
	private static $instance = null;

	/**
	 * FastAPI base URL.
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * API secret token.
	 *
	 * @var string
	 */
	private $api_secret;

	/**
	 * Request timeout in seconds.
	 *
	 * @var int
	 */
	private $timeout = 60;

	/**
	 * Get instance.
	 *
	 * @return API_Client
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
		$this->base_url   = contentcraft_get_option( 'api_base_url', 'http://localhost:8000' );
		$this->api_secret = contentcraft_get_option( 'api_secret', '' );
	}

	/**
	 * Make POST request to FastAPI.
	 *
	 * @param string $endpoint API endpoint (e.g., '/api/content/generate').
	 * @param array  $body     Request body.
	 * @param int    $timeout  Timeout in seconds (optional).
	 * @return array|WP_Error Response data or WP_Error on failure.
	 */
	public function post( $endpoint, $body, $timeout = null ) {
		$start_time = microtime( true );

		// Build full URL.
		$url = trailingslashit( $this->base_url ) . ltrim( $endpoint, '/' );

		// Check if API is configured.
		if ( empty( $this->api_secret ) ) {
			contentcraft_debug_log( 'API secret not configured' );
			return new \WP_Error(
				'not_configured',
				__( 'ContentCraft AI is not configured. Please set API credentials in settings.', 'contentcraft-ai' )
			);
		}

		// Prepare request arguments.
		$args = array(
			'timeout' => $timeout ? $timeout : $this->timeout,
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_secret,
				'Content-Type'  => 'application/json',
				'User-Agent'    => 'ContentCraft-AI/' . CONTENTCRAFT_VERSION,
			),
			'body'    => wp_json_encode( $body ),
		);

		// Allow filtering of request args.
		$args = apply_filters( 'contentcraft_api_request_args', $args, $endpoint );

		// Fire before request action.
		do_action( 'contentcraft_before_api_request', $endpoint, $body );

		contentcraft_debug_log( "API Request to: $url", $body );

		// Make request.
		$response = wp_remote_post( $url, $args );

		// Calculate latency.
		$latency_ms = (int) ( ( microtime( true ) - $start_time ) * 1000 );

		// Check for WP_Error.
		if ( is_wp_error( $response ) ) {
			contentcraft_debug_log( 'API Request failed', $response->get_error_message() );

			// Log failure.
			Logger::instance()->log(
				$this->get_log_type_from_endpoint( $endpoint ),
				null,
				0,
				$latency_ms,
				'failed',
				$response->get_error_message()
			);

			do_action( 'contentcraft_generation_failed', $response, $body );

			return $response;
		}

		// Get response code and body.
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		contentcraft_debug_log( "API Response Code: $response_code", $response_body );

		// Parse JSON.
		$data = json_decode( $response_body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$error = new \WP_Error(
				'json_parse_error',
				__( 'Failed to parse API response.', 'contentcraft-ai' )
			);

			Logger::instance()->log(
				$this->get_log_type_from_endpoint( $endpoint ),
				null,
				0,
				$latency_ms,
				'failed',
				'JSON parse error'
			);

			return $error;
		}

		// Check response code.
		if ( $response_code !== 200 ) {
			$error_code    = isset( $data['error']['code'] ) ? $data['error']['code'] : 'UNKNOWN_ERROR';
			$error_message = isset( $data['error']['message'] ) ? $data['error']['message'] : __( 'API request failed.', 'contentcraft-ai' );

			$error = new \WP_Error( $error_code, $error_message );

			Logger::instance()->log(
				$this->get_log_type_from_endpoint( $endpoint ),
				null,
				0,
				$latency_ms,
				'failed',
				$error_message
			);

			do_action( 'contentcraft_generation_failed', $error, $body );

			return $error;
		}

		// Check success field.
		if ( ! isset( $data['success'] ) || ! $data['success'] ) {
			$error_message = isset( $data['error']['message'] ) ? $data['error']['message'] : __( 'Generation failed.', 'contentcraft-ai' );

			$error = new \WP_Error( 'generation_failed', $error_message );

			Logger::instance()->log(
				$this->get_log_type_from_endpoint( $endpoint ),
				null,
				0,
				$latency_ms,
				'failed',
				$error_message
			);

			do_action( 'contentcraft_generation_failed', $error, $body );

			return $error;
		}

		// Extract tokens and log success.
		$tokens_used = isset( $data['metadata']['tokens_used'] ) ? (int) $data['metadata']['tokens_used'] : 0;

		Logger::instance()->log(
			$this->get_log_type_from_endpoint( $endpoint ),
			null,
			$tokens_used,
			$latency_ms,
			'success',
			null
		);

		// Allow filtering of response.
		$data = apply_filters( 'contentcraft_api_response', $data, $endpoint );

		// Fire after request action.
		do_action( 'contentcraft_after_api_request', $endpoint, $data );

		return $data;
	}

	/**
	 * Get request to FastAPI.
	 *
	 * @param string $endpoint API endpoint.
	 * @return array|WP_Error Response data or WP_Error on failure.
	 */
	public function get( $endpoint ) {
		$url = trailingslashit( $this->base_url ) . ltrim( $endpoint, '/' );

		$args = array(
			'timeout' => 10,
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_secret,
				'User-Agent'    => 'ContentCraft-AI/' . CONTENTCRAFT_VERSION,
			),
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		$data = json_decode( $response_body, true );

		if ( $response_code !== 200 || json_last_error() !== JSON_ERROR_NONE ) {
			return new \WP_Error( 'api_error', __( 'API request failed.', 'contentcraft-ai' ) );
		}

		return $data;
	}

	/**
	 * Test connection to FastAPI.
	 *
	 * @return array|WP_Error Connection status.
	 */
	public function test_connection() {
		$response = $this->get( '/api/health' );

		if ( is_wp_error( $response ) ) {
			return array(
				'connected' => false,
				'message'   => $response->get_error_message(),
			);
		}

		return array(
			'connected' => true,
			'message'   => __( 'Successfully connected to FastAPI backend.', 'contentcraft-ai' ),
			'version'   => isset( $response['version'] ) ? $response['version'] : 'unknown',
			'provider'  => isset( $response['provider'] ) ? $response['provider'] : 'unknown',
		);
	}

	/**
	 * Get cache key for request.
	 *
	 * @param string $endpoint API endpoint.
	 * @param array  $body     Request body.
	 * @return string Cache key.
	 */
	private function get_cache_key( $endpoint, $body ) {
		return 'contentcraft_cache_' . md5( $endpoint . wp_json_encode( $body ) );
	}

	/**
	 * Get log type from endpoint.
	 *
	 * @param string $endpoint API endpoint.
	 * @return string Log type.
	 */
	private function get_log_type_from_endpoint( $endpoint ) {
		if ( strpos( $endpoint, '/content' ) !== false ) {
			return 'content';
		} elseif ( strpos( $endpoint, '/product' ) !== false ) {
			return 'product';
		} elseif ( strpos( $endpoint, '/image' ) !== false ) {
			return 'image';
		} elseif ( strpos( $endpoint, '/seo' ) !== false ) {
			return 'seo';
		} elseif ( strpos( $endpoint, '/brand' ) !== false ) {
			return 'brand';
		}

		return 'other';
	}

	/**
	 * Set base URL.
	 *
	 * @param string $url Base URL.
	 */
	public function set_base_url( $url ) {
		$this->base_url = $url;
	}

	/**
	 * Set API secret.
	 *
	 * @param string $secret API secret.
	 */
	public function set_api_secret( $secret ) {
		$this->api_secret = $secret;
	}

	/**
	 * Set timeout.
	 *
	 * @param int $timeout Timeout in seconds.
	 */
	public function set_timeout( $timeout ) {
		$this->timeout = $timeout;
	}
}


