<?php
/**
 * Dashboard page template.
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get statistics.
$logger = ContentCraft\Logger::instance();
$stats  = $logger->get_stats( 0, gmdate( 'Y-m-d', strtotime( '-30 days' ) ), gmdate( 'Y-m-d' ) );

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( ! contentcraft_is_configured() ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %s: Settings page URL */
					wp_kses_post( __( 'ContentCraft AI is not configured yet. Please <a href="%s">configure settings</a> to get started.', 'contentcraft-ai' ) ),
					esc_url( admin_url( 'admin.php?page=contentcraft-settings' ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="contentcraft-dashboard">
		<div class="contentcraft-stats-grid">
			<div class="contentcraft-stat-box">
				<div class="stat-icon dashicons dashicons-edit-large"></div>
				<div class="stat-content">
					<div class="stat-value"><?php echo esc_html( number_format( $stats['total_generations'] ) ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Total Generations', 'contentcraft-ai' ); ?></div>
				</div>
			</div>

			<div class="contentcraft-stat-box success">
				<div class="stat-icon dashicons dashicons-yes-alt"></div>
				<div class="stat-content">
					<div class="stat-value"><?php echo esc_html( number_format( $stats['successful'] ) ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Successful', 'contentcraft-ai' ); ?></div>
				</div>
			</div>

			<div class="contentcraft-stat-box error">
				<div class="stat-icon dashicons dashicons-warning"></div>
				<div class="stat-content">
					<div class="stat-value"><?php echo esc_html( number_format( $stats['failed'] ) ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Failed', 'contentcraft-ai' ); ?></div>
				</div>
			</div>

			<div class="contentcraft-stat-box">
				<div class="stat-icon dashicons dashicons-chart-line"></div>
				<div class="stat-content">
					<div class="stat-value"><?php echo esc_html( contentcraft_format_tokens( $stats['total_tokens'] ) ); ?></div>
					<div class="stat-label"><?php esc_html_e( 'Tokens Used', 'contentcraft-ai' ); ?></div>
				</div>
			</div>
		</div>

		<div class="contentcraft-dashboard-content">
			<div class="contentcraft-main-column">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Quick Actions', 'contentcraft-ai' ); ?></h2>
					<div class="inside">
						<div class="contentcraft-quick-actions">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=contentcraft-content-studio' ) ); ?>" class="contentcraft-action-button">
								<span class="dashicons dashicons-edit-large"></span>
								<span><?php esc_html_e( 'Content Studio', 'contentcraft-ai' ); ?></span>
								<p><?php esc_html_e( 'Generate blog posts and pages', 'contentcraft-ai' ); ?></p>
							</a>

							<?php if ( contentcraft_is_woocommerce_active() ) : ?>
								<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>" class="contentcraft-action-button">
									<span class="dashicons dashicons-products"></span>
									<span><?php esc_html_e( 'Product Writer', 'contentcraft-ai' ); ?></span>
									<p><?php esc_html_e( 'Generate product descriptions', 'contentcraft-ai' ); ?></p>
								</a>

								<a href="<?php echo esc_url( admin_url( 'admin.php?page=contentcraft-bulk' ) ); ?>" class="contentcraft-action-button">
									<span class="dashicons dashicons-list-view"></span>
									<span><?php esc_html_e( 'Bulk Generator', 'contentcraft-ai' ); ?></span>
									<p><?php esc_html_e( 'Process multiple products', 'contentcraft-ai' ); ?></p>
								</a>
							<?php endif; ?>

							<a href="<?php echo esc_url( admin_url( 'admin.php?page=contentcraft-brand' ) ); ?>" class="contentcraft-action-button">
								<span class="dashicons dashicons-admin-customizer"></span>
								<span><?php esc_html_e( 'Brand Voice', 'contentcraft-ai' ); ?></span>
								<p><?php esc_html_e( 'Train your brand style', 'contentcraft-ai' ); ?></p>
							</a>
						</div>
					</div>
				</div>

				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Recent Activity', 'contentcraft-ai' ); ?></h2>
					<div class="inside">
						<?php if ( ! empty( $stats['recent_generations'] ) ) : ?>
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Type', 'contentcraft-ai' ); ?></th>
										<th><?php esc_html_e( 'Status', 'contentcraft-ai' ); ?></th>
										<th><?php esc_html_e( 'Tokens', 'contentcraft-ai' ); ?></th>
										<th><?php esc_html_e( 'Time', 'contentcraft-ai' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $stats['recent_generations'] as $log ) : ?>
										<tr>
											<td><?php echo esc_html( ucfirst( $log['type'] ) ); ?></td>
											<td>
												<?php if ( 'success' === $log['status'] ) : ?>
													<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
												<?php else : ?>
													<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
												<?php endif; ?>
											</td>
											<td><?php echo esc_html( number_format( $log['tokens_used'] ) ); ?></td>
											<td><?php echo esc_html( human_time_diff( strtotime( $log['created_at'] ), current_time( 'timestamp' ) ) . ' ago' ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p><?php esc_html_e( 'No recent activity.', 'contentcraft-ai' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="contentcraft-sidebar-column">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Getting Started', 'contentcraft-ai' ); ?></h2>
					<div class="inside">
						<ol>
							<li><?php esc_html_e( 'Configure your AI provider in Settings', 'contentcraft-ai' ); ?></li>
							<li><?php esc_html_e( 'Train your Brand Voice (optional)', 'contentcraft-ai' ); ?></li>
							<li><?php esc_html_e( 'Generate your first content!', 'contentcraft-ai' ); ?></li>
						</ol>
					</div>
				</div>

				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'System Status', 'contentcraft-ai' ); ?></h2>
					<div class="inside">
						<?php
						$connection = ContentCraft\API_Client::instance()->test_connection();
						?>
						<p>
							<strong><?php esc_html_e( 'FastAPI Connection:', 'contentcraft-ai' ); ?></strong><br />
							<?php if ( $connection['connected'] ) : ?>
								<span style="color: #46b450;">✓ <?php esc_html_e( 'Connected', 'contentcraft-ai' ); ?></span>
							<?php else : ?>
								<span style="color: #dc3232;">✗ <?php esc_html_e( 'Disconnected', 'contentcraft-ai' ); ?></span>
							<?php endif; ?>
						</p>

						<?php if ( $connection['connected'] ) : ?>
							<p>
								<strong><?php esc_html_e( 'Provider:', 'contentcraft-ai' ); ?></strong> <?php echo esc_html( $connection['provider'] ?? 'unknown' ); ?><br />
								<strong><?php esc_html_e( 'Version:', 'contentcraft-ai' ); ?></strong> <?php echo esc_html( $connection['version'] ?? 'unknown' ); ?>
							</p>
						<?php endif; ?>

						<?php
						$brand_profile = contentcraft_get_option( 'brand_profile', array() );
						$brand_enabled = isset( $brand_profile['enabled'] ) && $brand_profile['enabled'];
						?>
						<p>
							<strong><?php esc_html_e( 'Brand Voice:', 'contentcraft-ai' ); ?></strong><br />
							<?php if ( $brand_enabled ) : ?>
								<span style="color: #46b450;">✓ <?php esc_html_e( 'Trained', 'contentcraft-ai' ); ?></span>
							<?php else : ?>
								<span style="color: #999;">○ <?php esc_html_e( 'Not trained', 'contentcraft-ai' ); ?></span>
							<?php endif; ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.contentcraft-stats-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin: 20px 0;
	}
	.contentcraft-stat-box {
		background: #fff;
		border: 1px solid #c3c4c7;
		border-radius: 4px;
		padding: 20px;
		display: flex;
		align-items: center;
		gap: 15px;
	}
	.contentcraft-stat-box.success {
		border-left: 4px solid #46b450;
	}
	.contentcraft-stat-box.error {
		border-left: 4px solid #dc3232;
	}
	.stat-icon {
		font-size: 32px;
		width: 32px;
		height: 32px;
		color: #2271b1;
	}
	.stat-value {
		font-size: 32px;
		font-weight: 700;
		line-height: 1;
	}
	.stat-label {
		font-size: 13px;
		color: #646970;
		margin-top: 5px;
	}
	.contentcraft-dashboard-content {
		display: flex;
		gap: 20px;
	}
	.contentcraft-main-column {
		flex: 1;
	}
	.contentcraft-sidebar-column {
		width: 300px;
	}
	.contentcraft-quick-actions {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
		gap: 15px;
	}
	.contentcraft-action-button {
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 20px;
		background: #f6f7f7;
		border: 1px solid #c3c4c7;
		border-radius: 4px;
		text-decoration: none;
		text-align: center;
		transition: all 0.2s;
	}
	.contentcraft-action-button:hover {
		background: #fff;
		border-color: #2271b1;
		transform: translateY(-2px);
	}
	.contentcraft-action-button .dashicons {
		font-size: 48px;
		width: 48px;
		height: 48px;
		color: #2271b1;
		margin-bottom: 10px;
	}
	.contentcraft-action-button span {
		font-size: 16px;
		font-weight: 600;
		color: #1d2327;
		margin-bottom: 5px;
	}
	.contentcraft-action-button p {
		font-size: 13px;
		color: #646970;
		margin: 0;
	}
</style>


