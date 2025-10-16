<?php
/**
 * WooCommerce Integration class.
 *
 * @package ContentCraft_AI
 */

namespace ContentCraft;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce_Integration class.
 */
class WooCommerce_Integration {

	/**
	 * Single instance.
	 *
	 * @var WooCommerce_Integration
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return WooCommerce_Integration
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
		// Add meta box to product editor.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Save product meta.
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_meta' ) );

		// Enqueue scripts on product edit page.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add meta box to product editor.
	 */
	public function add_meta_box() {
		add_meta_box(
			'contentcraft_product_metabox',
			__( 'ContentCraft AI - Product Writer', 'contentcraft-ai' ),
			array( $this, 'render_meta_box' ),
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Render meta box content.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'contentcraft_product_meta', 'contentcraft_product_nonce' );

		$product = wc_get_product( $post->ID );

		if ( ! $product ) {
			echo '<p>' . esc_html__( 'Product not found.', 'contentcraft-ai' ) . '</p>';
			return;
		}

		?>
		<div id="contentcraft-product-writer">
			<p class="description">
				<?php esc_html_e( 'Generate AI-powered product descriptions, features, and FAQs.', 'contentcraft-ai' ); ?>
			</p>

			<div class="contentcraft-field">
				<label for="contentcraft_keywords">
					<?php esc_html_e( 'Keywords (comma-separated)', 'contentcraft-ai' ); ?>
				</label>
				<input type="text" id="contentcraft_keywords" class="widefat" placeholder="<?php esc_attr_e( 'e.g., organic, eco-friendly, premium', 'contentcraft-ai' ); ?>" />
			</div>

			<div class="contentcraft-field">
				<label for="contentcraft_tone">
					<?php esc_html_e( 'Tone', 'contentcraft-ai' ); ?>
				</label>
				<select id="contentcraft_tone" class="widefat">
					<?php foreach ( contentcraft_get_tones() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>">
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="contentcraft-field">
				<label for="contentcraft_language">
					<?php esc_html_e( 'Language', 'contentcraft-ai' ); ?>
				</label>
				<select id="contentcraft_language" class="widefat">
					<?php foreach ( contentcraft_get_languages() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( contentcraft_get_option( 'default_language' ), $key ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="contentcraft-field">
				<label><?php esc_html_e( 'Generate Sections', 'contentcraft-ai' ); ?></label>
				<label>
					<input type="checkbox" name="sections[]" value="short_desc" checked /> <?php esc_html_e( 'Short Description', 'contentcraft-ai' ); ?>
				</label><br />
				<label>
					<input type="checkbox" name="sections[]" value="long_desc" checked /> <?php esc_html_e( 'Long Description', 'contentcraft-ai' ); ?>
				</label><br />
				<label>
					<input type="checkbox" name="sections[]" value="bullets" checked /> <?php esc_html_e( 'Bullet Features', 'contentcraft-ai' ); ?>
				</label><br />
				<label>
					<input type="checkbox" name="sections[]" value="faqs" checked /> <?php esc_html_e( 'FAQs', 'contentcraft-ai' ); ?>
				</label><br />
				<label>
					<input type="checkbox" name="sections[]" value="meta" checked /> <?php esc_html_e( 'SEO Meta', 'contentcraft-ai' ); ?>
				</label><br />
				<label>
					<input type="checkbox" name="sections[]" value="tags" checked /> <?php esc_html_e( 'Product Tags', 'contentcraft-ai' ); ?>
				</label>
			</div>

			<div class="contentcraft-field">
				<button type="button" id="contentcraft-generate-product" class="button button-primary button-large widefat">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Generate Product Content', 'contentcraft-ai' ); ?>
				</button>
			</div>

			<div id="contentcraft-product-result" style="display: none; margin-top: 15px;">
				<h4><?php esc_html_e( 'Generated Content', 'contentcraft-ai' ); ?></h4>
				<div id="contentcraft-product-output"></div>
				<button type="button" id="contentcraft-apply-product" class="button button-primary widefat">
					<?php esc_html_e( 'Apply to Product', 'contentcraft-ai' ); ?>
				</button>
			</div>

			<div id="contentcraft-product-loading" style="display: none; text-align: center; padding: 20px;">
				<span class="spinner is-active" style="float: none;"></span>
				<p><?php esc_html_e( 'Generating content...', 'contentcraft-ai' ); ?></p>
			</div>

			<div id="contentcraft-product-error" style="display: none;" class="notice notice-error">
				<p></p>
			</div>
		</div>

		<style>
			.contentcraft-field {
				margin-bottom: 15px;
			}
			.contentcraft-field label {
				display: block;
				font-weight: 600;
				margin-bottom: 5px;
			}
		</style>
		<?php
	}

	/**
	 * Save product meta.
	 *
	 * @param int $product_id Product ID.
	 */
	public function save_meta( $product_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['contentcraft_product_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['contentcraft_product_nonce'] ) ), 'contentcraft_product_meta' ) ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_product', $product_id ) ) {
			return;
		}

		// Save any custom meta if needed.
		// Currently, generation happens via AJAX, so no need to save here.
	}

	/**
	 * Enqueue scripts for product editor.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		// Only on product edit page.
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'product' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_script(
			'contentcraft-product',
			CONTENTCRAFT_PLUGIN_URL . 'admin/assets/js/product.js',
			array( 'jquery', 'wp-api' ),
			CONTENTCRAFT_VERSION,
			true
		);

		wp_localize_script(
			'contentcraft-product',
			'contentcraftProduct',
			array(
				'restUrl' => rest_url( 'contentcraft/v1' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'strings' => array(
					'generating' => __( 'Generating content...', 'contentcraft-ai' ),
					'success'    => __( 'Content generated successfully!', 'contentcraft-ai' ),
					'error'      => __( 'An error occurred. Please try again.', 'contentcraft-ai' ),
					'applied'    => __( 'Content applied to product!', 'contentcraft-ai' ),
				),
			)
		);
	}
}


