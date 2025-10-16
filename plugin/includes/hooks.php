<?php
/**
 * Action and filter hooks registration.
 *
 * Centralized place for all plugin hooks.
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Content generation filters.
 *
 * Allow developers to modify content before/after generation.
 */

/**
 * Filter content generation parameters before API call.
 *
 * @param array $params Generation parameters.
 */
$params = apply_filters( 'contentcraft_before_generate_content', $params );

/**
 * Filter generated content before saving.
 *
 * @param array $content Generated content data.
 * @param array $params  Original parameters.
 */
$content = apply_filters( 'contentcraft_generated_content', $content, $params );

/**
 * Filter API request arguments.
 *
 * @param array  $args     Request arguments for wp_remote_post().
 * @param string $endpoint API endpoint.
 */
$args = apply_filters( 'contentcraft_api_request_args', $args, $endpoint );

/**
 * Filter API response before processing.
 *
 * @param array  $response API response data.
 * @param string $endpoint API endpoint.
 */
$response = apply_filters( 'contentcraft_api_response', $response, $endpoint );

/**
 * Filter brand profile before applying to prompt.
 *
 * @param array $profile Brand profile data.
 */
$profile = apply_filters( 'contentcraft_brand_profile', $profile );

/**
 * Filter prompt instructions.
 *
 * @param string $instructions Prompt instructions.
 * @param string $type         Content type (content, product, seo, etc).
 */
$instructions = apply_filters( 'contentcraft_prompt_instructions', $instructions, $type );

/**
 * Filter SEO meta before saving.
 *
 * @param array $meta    SEO meta data.
 * @param int   $post_id Post ID.
 */
$meta = apply_filters( 'contentcraft_seo_meta', $meta, $post_id );

/**
 * Filter internal link suggestions.
 *
 * @param array  $links   Internal link suggestions.
 * @param string $content Post content.
 */
$links = apply_filters( 'contentcraft_internal_links', $links, $content );

/**
 * Filter schema JSON-LD before output.
 *
 * @param string $schema  Schema JSON-LD.
 * @param int    $post_id Post ID.
 */
$schema = apply_filters( 'contentcraft_schema_json_ld', $schema, $post_id );

/**
 * Actions.
 *
 * Allow developers to hook into plugin events.
 */

/**
 * Fires before API request is made.
 *
 * @param string $endpoint API endpoint.
 * @param array  $body     Request body.
 */
do_action( 'contentcraft_before_api_request', $endpoint, $body );

/**
 * Fires after successful API request.
 *
 * @param string $endpoint API endpoint.
 * @param array  $response API response.
 */
do_action( 'contentcraft_after_api_request', $endpoint, $response );

/**
 * Fires after content is generated.
 *
 * @param int   $post_id Post ID.
 * @param array $content Generated content data.
 */
do_action( 'contentcraft_content_generated', $post_id, $content );

/**
 * Fires after product content is generated.
 *
 * @param int   $product_id Product ID.
 * @param array $content    Generated content data.
 */
do_action( 'contentcraft_product_content_generated', $product_id, $content );

/**
 * Fires when content generation fails.
 *
 * @param WP_Error $error  Error object.
 * @param array    $params Generation parameters.
 */
do_action( 'contentcraft_generation_failed', $error, $params );

/**
 * Fires after brand voice is trained.
 *
 * @param array $profile Brand profile data.
 */
do_action( 'contentcraft_brand_trained', $profile );

/**
 * Fires after alt-text is generated.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $alt_text      Generated alt-text.
 */
do_action( 'contentcraft_alt_text_generated', $attachment_id, $alt_text );

/**
 * Fires after bulk generation completes.
 *
 * @param array $results Bulk generation results.
 */
do_action( 'contentcraft_bulk_generation_complete', $results );

/**
 * Fires when settings are saved.
 *
 * @param array $settings New settings.
 */
do_action( 'contentcraft_settings_saved', $settings );

/**
 * WooCommerce specific hooks.
 */
if ( contentcraft_is_woocommerce_active() ) {

	/**
	 * Add meta box to product editor.
	 */
	add_action( 'add_meta_boxes', array( ContentCraft\WooCommerce_Integration::instance(), 'add_meta_box' ) );

	/**
	 * Save product meta.
	 */
	add_action( 'woocommerce_process_product_meta', array( ContentCraft\WooCommerce_Integration::instance(), 'save_meta' ) );
}

/**
 * Admin hooks.
 */
if ( is_admin() ) {

	/**
	 * Redirect to settings page on activation.
	 */
	add_action( 'admin_init', 'contentcraft_activation_redirect' );

	/**
	 * Add settings link to plugin row.
	 */
	add_filter( 'plugin_action_links_' . CONTENTCRAFT_PLUGIN_BASENAME, 'contentcraft_add_settings_link' );
}

/**
 * Redirect to settings page on activation.
 */
function contentcraft_activation_redirect() {
	if ( get_transient( 'contentcraft_activation_redirect' ) ) {
		delete_transient( 'contentcraft_activation_redirect' );

		if ( ! isset( $_GET['activate-multi'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=contentcraft-settings' ) );
			exit;
		}
	}
}

/**
 * Add settings link to plugin row.
 *
 * @param array $links Existing links.
 * @return array Modified links.
 */
function contentcraft_add_settings_link( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		admin_url( 'admin.php?page=contentcraft-settings' ),
		__( 'Settings', 'contentcraft-ai' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}


