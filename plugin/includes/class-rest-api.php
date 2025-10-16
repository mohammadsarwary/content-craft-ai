<?php
/**
 * REST API class for ContentCraft AI.
 *
 * Registers WordPress REST API endpoints.
 *
 * @package ContentCraft_AI
 */

namespace ContentCraft;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST_API class.
 */
class REST_API {

	/**
	 * Single instance.
	 *
	 * @var REST_API
	 */
	private static $instance = null;

	/**
	 * API namespace.
	 *
	 * @var string
	 */
	private $namespace = 'contentcraft/v1';

	/**
	 * Get instance.
	 *
	 * @return REST_API
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
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
		// Content generation.
		register_rest_route(
			$this->namespace,
			'/content/generate',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'generate_content' ),
				'permission_callback' => array( $this, 'check_edit_posts_permission' ),
				'args'                => $this->get_content_args(),
			)
		);

		// Product generation.
		register_rest_route(
			$this->namespace,
			'/product/generate',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'generate_product' ),
				'permission_callback' => array( $this, 'check_edit_products_permission' ),
				'args'                => $this->get_product_args(),
			)
		);

		// Image analysis.
		register_rest_route(
			$this->namespace,
			'/image/analyze',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'analyze_image' ),
				'permission_callback' => array( $this, 'check_upload_permission' ),
			)
		);

		// SEO optimization.
		register_rest_route(
			$this->namespace,
			'/seo/optimize',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'optimize_seo' ),
				'permission_callback' => array( $this, 'check_edit_posts_permission' ),
			)
		);

		// Brand training.
		register_rest_route(
			$this->namespace,
			'/brand/train',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'train_brand' ),
				'permission_callback' => array( $this, 'check_manage_options_permission' ),
			)
		);

		// Bulk processing.
		register_rest_route(
			$this->namespace,
			'/bulk/process',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'bulk_process' ),
				'permission_callback' => array( $this, 'check_edit_products_permission' ),
			)
		);

		// Test connection.
		register_rest_route(
			$this->namespace,
			'/settings/test',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'test_connection' ),
				'permission_callback' => array( $this, 'check_manage_options_permission' ),
			)
		);
	}

	/**
	 * Generate content callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|WP_Error Response.
	 */
	public function generate_content( $request ) {
		$params = array(
			'topic'       => $request->get_param( 'topic' ),
			'keywords'    => $request->get_param( 'keywords' ),
			'tone'        => $request->get_param( 'tone' ) ?: contentcraft_get_option( 'default_tone' ),
			'length'      => $request->get_param( 'length' ) ?: 'medium',
			'language'    => $request->get_param( 'language' ) ?: contentcraft_get_option( 'default_language' ),
			'audience'    => $request->get_param( 'audience' ),
			'sections'    => $request->get_param( 'sections' ) ?: array( 'title', 'excerpt', 'body', 'meta' ),
		);

		// Get brand profile if enabled.
		$brand_profile = contentcraft_get_option( 'brand_profile', array() );
		if ( isset( $brand_profile['enabled'] ) && $brand_profile['enabled'] ) {
			$params['brand_profile'] = $brand_profile;
		}

		// Call FastAPI.
		$response = API_Client::instance()->post( '/api/content/generate', $params );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => $response->get_error_message(),
				),
				500
			);
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Generate product callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|WP_Error Response.
	 */
	public function generate_product( $request ) {
		$params = array(
			'product_id' => $request->get_param( 'product_id' ),
			'name'       => $request->get_param( 'name' ),
			'category'   => $request->get_param( 'category' ),
			'attributes' => $request->get_param( 'attributes' ) ?: array(),
			'features'   => $request->get_param( 'features' ) ?: array(),
			'usp'        => $request->get_param( 'usp' ) ?: array(),
			'price'      => $request->get_param( 'price' ),
			'keywords'   => $request->get_param( 'keywords' ) ?: array(),
			'tone'       => $request->get_param( 'tone' ) ?: contentcraft_get_option( 'default_tone' ),
			'language'   => $request->get_param( 'language' ) ?: contentcraft_get_option( 'default_language' ),
		);

		// Get brand profile.
		$brand_profile = contentcraft_get_option( 'brand_profile', array() );
		if ( isset( $brand_profile['enabled'] ) && $brand_profile['enabled'] ) {
			$params['brand_profile'] = $brand_profile;
		}

		// Call FastAPI.
		$response = API_Client::instance()->post( '/api/product/generate', $params );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => $response->get_error_message(),
				),
				500
			);
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Analyze image callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response Response.
	 */
	public function analyze_image( $request ) {
		$params = array(
			'image_url'     => $request->get_param( 'image_url' ),
			'attachment_id' => $request->get_param( 'attachment_id' ),
			'language'      => $request->get_param( 'language' ) ?: contentcraft_get_option( 'default_language' ),
			'context'       => $request->get_param( 'context' ) ?: 'product',
		);

		$response = API_Client::instance()->post( '/api/image/analyze', $params );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => $response->get_error_message(),
				),
				500
			);
		}

		// Update attachment alt-text if attachment_id provided.
		if ( ! empty( $params['attachment_id'] ) && isset( $response['data']['alt_text'] ) ) {
			update_post_meta( $params['attachment_id'], '_wp_attachment_image_alt', sanitize_text_field( $response['data']['alt_text'] ) );
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Optimize SEO callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response Response.
	 */
	public function optimize_seo( $request ) {
		$params = array(
			'content_html'   => $request->get_param( 'content_html' ),
			'current_title'  => $request->get_param( 'current_title' ),
			'keywords'       => $request->get_param( 'keywords' ) ?: array(),
			'language'       => $request->get_param( 'language' ) ?: contentcraft_get_option( 'default_language' ),
			'post_type'      => $request->get_param( 'post_type' ) ?: 'post',
		);

		$response = API_Client::instance()->post( '/api/seo/optimize', $params );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => $response->get_error_message(),
				),
				500
			);
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Train brand voice callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response Response.
	 */
	public function train_brand( $request ) {
		$num_samples = $request->get_param( 'num_samples' ) ?: 20;
		$post_types  = $request->get_param( 'post_types' ) ?: array( 'post', 'page' );

		// Get recent posts for training.
		$posts = get_posts(
			array(
				'post_type'      => $post_types,
				'posts_per_page' => $num_samples,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$samples = array();
		foreach ( $posts as $post ) {
			$samples[] = array(
				'title'   => $post->post_title,
				'excerpt' => $post->post_excerpt,
				'body'    => wp_strip_all_tags( $post->post_content ),
			);
		}

		$params = array(
			'samples'  => $samples,
			'language' => contentcraft_get_option( 'default_language' ),
		);

		$response = API_Client::instance()->post( '/api/brand/train', $params, 90 );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'error'   => $response->get_error_message(),
				),
				500
			);
		}

		// Save brand profile.
		if ( isset( $response['data']['brand_profile'] ) ) {
			$brand_profile = $response['data']['brand_profile'];
			$brand_profile['enabled'] = true;
			$brand_profile['trained_at'] = current_time( 'mysql' );
			$brand_profile['prompt_template'] = $response['data']['prompt_template'];

			contentcraft_update_option( 'brand_profile', $brand_profile );

			do_action( 'contentcraft_brand_trained', $brand_profile );
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Bulk process callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response Response.
	 */
	public function bulk_process( $request ) {
		// TODO: Implement bulk processing logic
		// This will process products in batches
		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Bulk processing started.', 'contentcraft-ai' ),
			),
			200
		);
	}

	/**
	 * Test connection callback.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response Response.
	 */
	public function test_connection( $request ) {
		$result = API_Client::instance()->test_connection();

		if ( $result['connected'] ) {
			return new \WP_REST_Response( $result, 200 );
		} else {
			return new \WP_REST_Response( $result, 500 );
		}
	}

	/**
	 * Permission callback: check if user can edit posts.
	 *
	 * @return bool
	 */
	public function check_edit_posts_permission() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Permission callback: check if user can edit products.
	 *
	 * @return bool
	 */
	public function check_edit_products_permission() {
		if ( ! contentcraft_is_woocommerce_active() ) {
			return current_user_can( 'edit_posts' );
		}
		return current_user_can( 'edit_products' );
	}

	/**
	 * Permission callback: check if user can upload files.
	 *
	 * @return bool
	 */
	public function check_upload_permission() {
		return current_user_can( 'upload_files' );
	}

	/**
	 * Permission callback: check if user can manage options.
	 *
	 * @return bool
	 */
	public function check_manage_options_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get content generation args schema.
	 *
	 * @return array
	 */
	private function get_content_args() {
		return array(
			'topic'    => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'keywords' => array(
				'type'    => 'array',
				'default' => array(),
			),
			'tone'     => array(
				'type'    => 'string',
				'default' => contentcraft_get_option( 'default_tone' ),
			),
			'length'   => array(
				'type'    => 'string',
				'default' => 'medium',
			),
			'language' => array(
				'type'    => 'string',
				'default' => contentcraft_get_option( 'default_language' ),
			),
		);
	}

	/**
	 * Get product generation args schema.
	 *
	 * @return array
	 */
	private function get_product_args() {
		return array(
			'name' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}


