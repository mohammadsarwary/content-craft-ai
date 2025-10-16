<?php
/**
 * Brand Voice Training page template.
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand_profile = contentcraft_get_option( 'brand_profile', array() );
$is_trained    = isset( $brand_profile['enabled'] ) && $brand_profile['enabled'];

?>
<div class="wrap contentcraft-brand-voice">
	<h1 class="contentcraft-page-title">
		<span class="dashicons dashicons-admin-customizer"></span>
		<?php esc_html_e( 'Brand Voice Training', 'contentcraft-ai' ); ?>
	</h1>
	
	<p class="contentcraft-subtitle">
		<?php esc_html_e( 'Train AI to match your unique writing style and brand voice', 'contentcraft-ai' ); ?>
	</p>

	<div class="contentcraft-brand-container">
		<!-- Training Panel -->
		<div class="contentcraft-brand-main">
			<?php if ( $is_trained ) : ?>
				<!-- Current Profile Card -->
				<div class="contentcraft-card contentcraft-profile-card">
					<div class="contentcraft-card-header">
						<h2>
							<span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span>
							<?php esc_html_e( 'Brand Voice Profile', 'contentcraft-ai' ); ?>
						</h2>
						<span class="contentcraft-badge contentcraft-badge-success">
							<?php esc_html_e( 'Active', 'contentcraft-ai' ); ?>
						</span>
					</div>
					<div class="contentcraft-card-body">
						<div class="contentcraft-profile-grid">
							<?php if ( ! empty( $brand_profile['tone'] ) ) : ?>
								<div class="profile-item">
									<span class="profile-label"><?php esc_html_e( 'Tone:', 'contentcraft-ai' ); ?></span>
									<span class="profile-value"><?php echo esc_html( $brand_profile['tone'] ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $brand_profile['sentence_length'] ) ) : ?>
								<div class="profile-item">
									<span class="profile-label"><?php esc_html_e( 'Sentence Length:', 'contentcraft-ai' ); ?></span>
									<span class="profile-value"><?php echo esc_html( $brand_profile['sentence_length'] ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $brand_profile['vocabulary_level'] ) ) : ?>
								<div class="profile-item">
									<span class="profile-label"><?php esc_html_e( 'Vocabulary:', 'contentcraft-ai' ); ?></span>
									<span class="profile-value"><?php echo esc_html( $brand_profile['vocabulary_level'] ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $brand_profile['writing_style'] ) ) : ?>
								<div class="profile-item">
									<span class="profile-label"><?php esc_html_e( 'Writing Style:', 'contentcraft-ai' ); ?></span>
									<span class="profile-value"><?php echo esc_html( $brand_profile['writing_style'] ); ?></span>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $brand_profile['common_phrases'] ) && is_array( $brand_profile['common_phrases'] ) ) : ?>
							<div class="profile-section">
								<h4><?php esc_html_e( 'Common Phrases:', 'contentcraft-ai' ); ?></h4>
								<div class="contentcraft-tags">
									<?php foreach ( array_slice( $brand_profile['common_phrases'], 0, 8 ) as $phrase ) : ?>
										<span class="contentcraft-tag"><?php echo esc_html( $phrase ); ?></span>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $brand_profile['trained_at'] ) ) : ?>
							<p class="profile-meta">
								<?php
								printf(
									/* translators: %s: Training date */
									esc_html__( 'Trained on: %s', 'contentcraft-ai' ),
									esc_html( date_i18n( get_option( 'date_format' ), strtotime( $brand_profile['trained_at'] ) ) )
								);
								?>
							</p>
						<?php endif; ?>

						<div class="profile-actions">
							<button type="button" class="contentcraft-btn contentcraft-btn-secondary" id="disable-profile-btn">
								<span class="dashicons dashicons-hidden"></span>
								<?php esc_html_e( 'Disable Profile', 'contentcraft-ai' ); ?>
							</button>
							<button type="button" class="contentcraft-btn contentcraft-btn-primary" id="retrain-btn">
								<span class="dashicons dashicons-update"></span>
								<?php esc_html_e( 'Retrain Voice', 'contentcraft-ai' ); ?>
							</button>
						</div>
					</div>
				</div>
			<?php else : ?>
				<!-- Training Form -->
				<div class="contentcraft-card">
					<div class="contentcraft-card-header">
						<h2><?php esc_html_e( 'Train Brand Voice', 'contentcraft-ai' ); ?></h2>
					</div>
					<div class="contentcraft-card-body">
						<div class="contentcraft-info-box">
							<span class="dashicons dashicons-info"></span>
							<div>
								<h4><?php esc_html_e( 'How it works', 'contentcraft-ai' ); ?></h4>
								<p><?php esc_html_e( 'Our AI will analyze your existing content to learn your unique writing style, tone, and vocabulary. This ensures all generated content matches your brand voice.', 'contentcraft-ai' ); ?></p>
							</div>
						</div>

						<form id="contentcraft-brand-form">
							<div class="contentcraft-form-group">
								<label for="num-samples" class="contentcraft-label">
									<?php esc_html_e( 'Number of Posts to Analyze', 'contentcraft-ai' ); ?>
								</label>
								<select id="num-samples" class="contentcraft-select">
									<option value="10">10 posts</option>
									<option value="15">15 posts</option>
									<option value="20" selected>20 posts (recommended)</option>
									<option value="30">30 posts</option>
									<option value="50">50 posts</option>
								</select>
								<p class="contentcraft-hint">
									<?php esc_html_e( 'More samples = better accuracy (but slower training)', 'contentcraft-ai' ); ?>
								</p>
							</div>

							<div class="contentcraft-form-group">
								<label class="contentcraft-label">
									<?php esc_html_e( 'Content Types', 'contentcraft-ai' ); ?>
								</label>
								<div class="contentcraft-checkbox-group">
									<label class="contentcraft-checkbox">
										<input type="checkbox" name="post_types[]" value="post" checked />
										<span><?php esc_html_e( 'Blog Posts', 'contentcraft-ai' ); ?></span>
									</label>
									<label class="contentcraft-checkbox">
										<input type="checkbox" name="post_types[]" value="page" />
										<span><?php esc_html_e( 'Pages', 'contentcraft-ai' ); ?></span>
									</label>
								</div>
							</div>

							<div class="contentcraft-form-actions">
								<button type="submit" class="contentcraft-btn contentcraft-btn-primary contentcraft-btn-lg" id="train-btn">
									<span class="dashicons dashicons-update"></span>
									<span class="btn-text"><?php esc_html_e( 'Start Training', 'contentcraft-ai' ); ?></span>
								</button>
							</div>
						</form>
					</div>
				</div>
			<?php endif; ?>

			<!-- Training Progress -->
			<div id="training-progress" class="contentcraft-card" style="display: none;">
				<div class="contentcraft-card-body">
					<div class="contentcraft-loading-state">
						<div class="contentcraft-loading-spinner">
							<div class="spinner"></div>
						</div>
						<h3><?php esc_html_e( 'Training your brand voice...', 'contentcraft-ai' ); ?></h3>
						<p><?php esc_html_e( 'Analyzing your content. This may take up to a minute.', 'contentcraft-ai' ); ?></p>
						<div class="contentcraft-progress-bar">
							<div class="contentcraft-progress-fill"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Info Sidebar -->
		<div class="contentcraft-brand-sidebar">
			<div class="contentcraft-card">
				<div class="contentcraft-card-header">
					<h3>
						<span class="dashicons dashicons-lightbulb"></span>
						<?php esc_html_e( 'Why Brand Voice?', 'contentcraft-ai' ); ?>
					</h3>
				</div>
				<div class="contentcraft-card-body">
					<ul class="contentcraft-features-list">
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<div>
								<strong><?php esc_html_e( 'Consistency', 'contentcraft-ai' ); ?></strong>
								<p><?php esc_html_e( 'All content matches your brand style', 'contentcraft-ai' ); ?></p>
							</div>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<div>
								<strong><?php esc_html_e( 'Authenticity', 'contentcraft-ai' ); ?></strong>
								<p><?php esc_html_e( 'AI writes like you do', 'contentcraft-ai' ); ?></p>
							</div>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<div>
								<strong><?php esc_html_e( 'Time-Saving', 'contentcraft-ai' ); ?></strong>
								<p><?php esc_html_e( 'Less editing required', 'contentcraft-ai' ); ?></p>
							</div>
						</li>
						<li>
							<span class="dashicons dashicons-yes-alt"></span>
							<div>
								<strong><?php esc_html_e( 'Better Results', 'contentcraft-ai' ); ?></strong>
								<p><?php esc_html_e( 'Content that resonates with your audience', 'contentcraft-ai' ); ?></p>
							</div>
						</li>
					</ul>
				</div>
			</div>

			<div class="contentcraft-card">
				<div class="contentcraft-card-header">
					<h3>
						<span class="dashicons dashicons-editor-help"></span>
						<?php esc_html_e( 'Tips', 'contentcraft-ai' ); ?>
					</h3>
				</div>
				<div class="contentcraft-card-body">
					<ul class="contentcraft-tips-list">
						<li><?php esc_html_e( 'Train with your best content', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Use 15-30 posts for optimal results', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Retrain every 3-6 months as your style evolves', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Keep the profile active for all generations', 'contentcraft-ai' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.contentcraft-brand-container {
	display: grid;
	grid-template-columns: 1fr 350px;
	gap: 24px;
	margin-top: 20px;
}

@media (max-width: 1200px) {
	.contentcraft-brand-container {
		grid-template-columns: 1fr;
	}
}

.contentcraft-profile-card .contentcraft-card-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.contentcraft-badge {
	padding: 4px 12px;
	font-size: 11px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	border-radius: 12px;
}

.contentcraft-badge-success {
	background: #d4edda;
	color: #00a32a;
}

.contentcraft-profile-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 16px;
	margin-bottom: 24px;
}

.profile-item {
	padding: 12px;
	background: #f6f7f7;
	border-radius: 6px;
}

.profile-label {
	display: block;
	font-size: 11px;
	font-weight: 600;
	color: #646970;
	text-transform: uppercase;
	margin-bottom: 4px;
	letter-spacing: 0.5px;
}

.profile-value {
	display: block;
	font-size: 14px;
	color: #1d2327;
	font-weight: 500;
}

.profile-section {
	margin-top: 24px;
	padding-top: 24px;
	border-top: 1px solid #c3c4c7;
}

.profile-section h4 {
	font-size: 13px;
	font-weight: 600;
	color: #646970;
	text-transform: uppercase;
	margin-bottom: 12px;
}

.contentcraft-tags {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
}

.contentcraft-tag {
	padding: 6px 12px;
	font-size: 12px;
	background: #e7f5fe;
	color: #135e96;
	border-radius: 16px;
	font-weight: 500;
}

.profile-meta {
	font-size: 12px;
	color: #646970;
	margin-top: 20px;
	padding-top: 20px;
	border-top: 1px solid #c3c4c7;
}

.profile-actions {
	display: flex;
	gap: 12px;
	margin-top: 20px;
}

.contentcraft-info-box {
	display: flex;
	gap: 16px;
	padding: 16px;
	background: #e7f5fe;
	border-left: 4px solid #2271b1;
	border-radius: 6px;
	margin-bottom: 24px;
}

.contentcraft-info-box .dashicons {
	font-size: 24px;
	width: 24px;
	height: 24px;
	color: #2271b1;
	flex-shrink: 0;
}

.contentcraft-info-box h4 {
	margin: 0 0 8px 0;
	font-size: 14px;
	color: #1d2327;
}

.contentcraft-info-box p {
	margin: 0;
	font-size: 13px;
	color: #1d2327;
	line-height: 1.6;
}

.contentcraft-features-list {
	list-style: none;
	padding: 0;
	margin: 0;
}

.contentcraft-features-list li {
	display: flex;
	gap: 12px;
	padding: 16px 0;
	border-bottom: 1px solid #f0f0f1;
}

.contentcraft-features-list li:last-child {
	border-bottom: none;
}

.contentcraft-features-list .dashicons {
	color: #00a32a;
	font-size: 20px;
	width: 20px;
	height: 20px;
	flex-shrink: 0;
	margin-top: 2px;
}

.contentcraft-features-list strong {
	display: block;
	font-size: 13px;
	color: #1d2327;
	margin-bottom: 4px;
}

.contentcraft-features-list p {
	margin: 0;
	font-size: 12px;
	color: #646970;
	line-height: 1.5;
}
</style>

