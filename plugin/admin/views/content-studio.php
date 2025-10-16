<?php
/**
 * Content Studio page template.
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap contentcraft-content-studio">
	<h1 class="contentcraft-page-title">
		<span class="dashicons dashicons-edit-large"></span>
		<?php esc_html_e( 'Content Studio', 'contentcraft-ai' ); ?>
	</h1>
	
	<p class="contentcraft-subtitle">
		<?php esc_html_e( 'Generate SEO-optimized blog posts and pages with AI', 'contentcraft-ai' ); ?>
	</p>

	<?php if ( ! contentcraft_is_configured() ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %s: Settings page URL */
					wp_kses_post( __( 'Please <a href="%s">configure ContentCraft AI</a> first.', 'contentcraft-ai' ) ),
					esc_url( admin_url( 'admin.php?page=contentcraft-settings' ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="contentcraft-studio-container">
		<!-- Generator Form -->
		<div class="contentcraft-studio-form-panel">
			<div class="contentcraft-card">
				<div class="contentcraft-card-header">
					<h2><?php esc_html_e( 'Content Configuration', 'contentcraft-ai' ); ?></h2>
				</div>
				<div class="contentcraft-card-body">
					<form id="contentcraft-content-form">
						<!-- Topic -->
						<div class="contentcraft-form-group">
							<label for="content-topic" class="contentcraft-label">
								<?php esc_html_e( 'Topic', 'contentcraft-ai' ); ?>
								<span class="required">*</span>
							</label>
							<input 
								type="text" 
								id="content-topic" 
								class="contentcraft-input contentcraft-input-lg" 
								placeholder="<?php esc_attr_e( 'e.g., Best WordPress SEO Plugins 2024', 'contentcraft-ai' ); ?>"
								required
							/>
							<p class="contentcraft-hint">
								<?php esc_html_e( 'Enter the main topic for your content', 'contentcraft-ai' ); ?>
							</p>
						</div>

						<!-- Keywords -->
						<div class="contentcraft-form-group">
							<label for="content-keywords" class="contentcraft-label">
								<?php esc_html_e( 'Keywords', 'contentcraft-ai' ); ?>
							</label>
							<input 
								type="text" 
								id="content-keywords" 
								class="contentcraft-input" 
								placeholder="<?php esc_attr_e( 'wordpress, seo, plugins (comma-separated)', 'contentcraft-ai' ); ?>"
							/>
							<p class="contentcraft-hint">
								<?php esc_html_e( 'Target keywords for SEO optimization', 'contentcraft-ai' ); ?>
							</p>
						</div>

						<!-- Audience -->
						<div class="contentcraft-form-group">
							<label for="content-audience" class="contentcraft-label">
								<?php esc_html_e( 'Target Audience', 'contentcraft-ai' ); ?>
							</label>
							<input 
								type="text" 
								id="content-audience" 
								class="contentcraft-input" 
								placeholder="<?php esc_attr_e( 'e.g., WordPress beginners, developers', 'contentcraft-ai' ); ?>"
							/>
						</div>

						<div class="contentcraft-form-row">
							<!-- Tone -->
							<div class="contentcraft-form-group">
								<label for="content-tone" class="contentcraft-label">
									<?php esc_html_e( 'Tone', 'contentcraft-ai' ); ?>
								</label>
								<select id="content-tone" class="contentcraft-select">
									<?php foreach ( contentcraft_get_tones() as $key => $label ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( contentcraft_get_option( 'default_tone' ), $key ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Length -->
							<div class="contentcraft-form-group">
								<label for="content-length" class="contentcraft-label">
									<?php esc_html_e( 'Length', 'contentcraft-ai' ); ?>
								</label>
								<select id="content-length" class="contentcraft-select">
									<?php foreach ( contentcraft_get_lengths() as $key => $label ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="contentcraft-form-row">
							<!-- Language -->
							<div class="contentcraft-form-group">
								<label for="content-language" class="contentcraft-label">
									<?php esc_html_e( 'Language', 'contentcraft-ai' ); ?>
								</label>
								<select id="content-language" class="contentcraft-select">
									<?php foreach ( contentcraft_get_languages() as $key => $label ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( contentcraft_get_option( 'default_language' ), $key ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Sections -->
							<div class="contentcraft-form-group">
								<label class="contentcraft-label">
									<?php esc_html_e( 'Generate Sections', 'contentcraft-ai' ); ?>
								</label>
								<div class="contentcraft-checkbox-group">
									<label class="contentcraft-checkbox">
										<input type="checkbox" name="sections[]" value="title" checked />
										<span><?php esc_html_e( 'Title', 'contentcraft-ai' ); ?></span>
									</label>
									<label class="contentcraft-checkbox">
										<input type="checkbox" name="sections[]" value="excerpt" checked />
										<span><?php esc_html_e( 'Excerpt', 'contentcraft-ai' ); ?></span>
									</label>
									<label class="contentcraft-checkbox">
										<input type="checkbox" name="sections[]" value="body" checked />
										<span><?php esc_html_e( 'Body', 'contentcraft-ai' ); ?></span>
									</label>
									<label class="contentcraft-checkbox">
										<input type="checkbox" name="sections[]" value="meta" checked />
										<span><?php esc_html_e( 'SEO Meta', 'contentcraft-ai' ); ?></span>
									</label>
								</div>
							</div>
						</div>

						<!-- Generate Button -->
						<div class="contentcraft-form-actions">
							<button type="submit" class="contentcraft-btn contentcraft-btn-primary contentcraft-btn-lg" id="generate-content-btn">
								<span class="dashicons dashicons-edit"></span>
								<span class="btn-text"><?php esc_html_e( 'Generate Content', 'contentcraft-ai' ); ?></span>
							</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Tips Card -->
			<div class="contentcraft-card contentcraft-tips-card">
				<div class="contentcraft-card-header">
					<h3>
						<span class="dashicons dashicons-lightbulb"></span>
						<?php esc_html_e( 'Pro Tips', 'contentcraft-ai' ); ?>
					</h3>
				</div>
				<div class="contentcraft-card-body">
					<ul class="contentcraft-tips-list">
						<li><?php esc_html_e( 'Be specific with your topic for better results', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Include 3-5 relevant keywords for SEO', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Define your target audience clearly', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Review and edit generated content before publishing', 'contentcraft-ai' ); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<!-- Result Panel -->
		<div class="contentcraft-studio-result-panel">
			<!-- Loading State -->
			<div id="content-loading" class="contentcraft-loading-state" style="display: none;">
				<div class="contentcraft-loading-spinner">
					<div class="spinner"></div>
				</div>
				<h3><?php esc_html_e( 'Generating your content...', 'contentcraft-ai' ); ?></h3>
				<p><?php esc_html_e( 'This may take a few seconds. Please wait.', 'contentcraft-ai' ); ?></p>
				<div class="contentcraft-progress-bar">
					<div class="contentcraft-progress-fill"></div>
				</div>
			</div>

			<!-- Empty State -->
			<div id="content-empty" class="contentcraft-empty-state">
				<div class="contentcraft-empty-icon">
					<span class="dashicons dashicons-edit-large"></span>
				</div>
				<h3><?php esc_html_e( 'Ready to Create Amazing Content?', 'contentcraft-ai' ); ?></h3>
				<p><?php esc_html_e( 'Fill in the form and click "Generate Content" to get started.', 'contentcraft-ai' ); ?></p>
			</div>

			<!-- Result Display -->
			<div id="content-result" class="contentcraft-result-display" style="display: none;">
				<div class="contentcraft-card">
					<div class="contentcraft-card-header contentcraft-result-header">
						<h2><?php esc_html_e( 'Generated Content', 'contentcraft-ai' ); ?></h2>
						<div class="contentcraft-result-actions">
							<button type="button" class="contentcraft-btn contentcraft-btn-secondary" id="regenerate-btn">
								<span class="dashicons dashicons-update"></span>
								<?php esc_html_e( 'Regenerate', 'contentcraft-ai' ); ?>
							</button>
						</div>
					</div>
					<div class="contentcraft-card-body">
						<!-- Tabs -->
						<div class="contentcraft-tabs">
							<button class="contentcraft-tab active" data-tab="preview">
								<span class="dashicons dashicons-visibility"></span>
								<?php esc_html_e( 'Preview', 'contentcraft-ai' ); ?>
							</button>
							<button class="contentcraft-tab" data-tab="html">
								<span class="dashicons dashicons-editor-code"></span>
								<?php esc_html_e( 'HTML', 'contentcraft-ai' ); ?>
							</button>
							<button class="contentcraft-tab" data-tab="seo">
								<span class="dashicons dashicons-chart-line"></span>
								<?php esc_html_e( 'SEO Data', 'contentcraft-ai' ); ?>
							</button>
						</div>

						<!-- Tab Content -->
						<div class="contentcraft-tab-content">
							<!-- Preview Tab -->
							<div class="contentcraft-tab-pane active" id="tab-preview">
								<div id="content-preview" class="contentcraft-content-preview"></div>
							</div>

							<!-- HTML Tab -->
							<div class="contentcraft-tab-pane" id="tab-html">
								<textarea id="content-html" class="contentcraft-code-editor" readonly></textarea>
								<button type="button" class="contentcraft-btn contentcraft-btn-secondary contentcraft-btn-sm" id="copy-html-btn">
									<span class="dashicons dashicons-clipboard"></span>
									<?php esc_html_e( 'Copy HTML', 'contentcraft-ai' ); ?>
								</button>
							</div>

							<!-- SEO Tab -->
							<div class="contentcraft-tab-pane" id="tab-seo">
								<div id="content-seo" class="contentcraft-seo-data">
									<div class="contentcraft-seo-item">
										<label><?php esc_html_e( 'SEO Title:', 'contentcraft-ai' ); ?></label>
										<div id="seo-title" class="contentcraft-seo-value"></div>
									</div>
									<div class="contentcraft-seo-item">
										<label><?php esc_html_e( 'Meta Description:', 'contentcraft-ai' ); ?></label>
										<div id="seo-desc" class="contentcraft-seo-value"></div>
									</div>
									<div class="contentcraft-seo-item">
										<label><?php esc_html_e( 'Slug:', 'contentcraft-ai' ); ?></label>
										<div id="seo-slug" class="contentcraft-seo-value"></div>
									</div>
									<div class="contentcraft-seo-item">
										<label><?php esc_html_e( 'Headings:', 'contentcraft-ai' ); ?></label>
										<div id="seo-headings" class="contentcraft-seo-value"></div>
									</div>
								</div>
							</div>
						</div>

						<!-- Actions -->
						<div class="contentcraft-result-footer">
							<button type="button" class="contentcraft-btn contentcraft-btn-primary" id="insert-editor-btn">
								<span class="dashicons dashicons-plus-alt"></span>
								<?php esc_html_e( 'Insert to Editor', 'contentcraft-ai' ); ?>
							</button>
							<button type="button" class="contentcraft-btn contentcraft-btn-success" id="create-draft-btn">
								<span class="dashicons dashicons-saved"></span>
								<?php esc_html_e( 'Save as Draft', 'contentcraft-ai' ); ?>
							</button>
						</div>
					</div>
				</div>

				<!-- Stats -->
				<div class="contentcraft-generation-stats">
					<div class="stat-item">
						<span class="dashicons dashicons-clock"></span>
						<span id="stat-time">--</span>
					</div>
					<div class="stat-item">
						<span class="dashicons dashicons-chart-bar"></span>
						<span id="stat-tokens">--</span>
					</div>
					<div class="stat-item">
						<span class="dashicons dashicons-text"></span>
						<span id="stat-words">--</span>
					</div>
				</div>
			</div>

			<!-- Error State -->
			<div id="content-error" class="contentcraft-error-state" style="display: none;">
				<div class="contentcraft-error-icon">
					<span class="dashicons dashicons-warning"></span>
				</div>
				<h3><?php esc_html_e( 'Oops! Something went wrong', 'contentcraft-ai' ); ?></h3>
				<p id="error-message"></p>
				<button type="button" class="contentcraft-btn contentcraft-btn-primary" id="retry-btn">
					<span class="dashicons dashicons-update"></span>
					<?php esc_html_e( 'Try Again', 'contentcraft-ai' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

