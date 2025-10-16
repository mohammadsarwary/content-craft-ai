<?php
/**
 * Settings page template.
 *
 * @package ContentCraft_AI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors( 'contentcraft_settings' ); ?>

	<div class="contentcraft-settings-container">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'contentcraft_settings_group' );
			do_settings_sections( 'contentcraft-settings' );
			submit_button( __( 'Save Settings', 'contentcraft-ai' ) );
			?>
		</form>

		<div class="contentcraft-settings-sidebar">
			<div class="postbox">
				<h2 class="hndle"><?php esc_html_e( 'Quick Start', 'contentcraft-ai' ); ?></h2>
				<div class="inside">
					<ol>
						<li><?php esc_html_e( 'Start your FastAPI backend server', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Enter the FastAPI URL above', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Set a shared API secret', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Click "Test Connection" to verify', 'contentcraft-ai' ); ?></li>
						<li><?php esc_html_e( 'Start generating content!', 'contentcraft-ai' ); ?></li>
					</ol>
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><?php esc_html_e( 'Documentation', 'contentcraft-ai' ); ?></h2>
				<div class="inside">
					<ul>
						<li><a href="https://github.com/contentcraft-ai/contentcraft-ai/blob/main/README.md" target="_blank"><?php esc_html_e( 'Getting Started', 'contentcraft-ai' ); ?></a></li>
						<li><a href="https://github.com/contentcraft-ai/contentcraft-ai/blob/main/docs/architecture.md" target="_blank"><?php esc_html_e( 'Architecture', 'contentcraft-ai' ); ?></a></li>
						<li><a href="https://github.com/contentcraft-ai/contentcraft-ai/blob/main/docs/api-contracts.md" target="_blank"><?php esc_html_e( 'API Contracts', 'contentcraft-ai' ); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><?php esc_html_e( 'Support', 'contentcraft-ai' ); ?></h2>
				<div class="inside">
					<p><?php esc_html_e( 'Need help? Report issues on GitHub:', 'contentcraft-ai' ); ?></p>
					<p>
						<a href="https://github.com/contentcraft-ai/contentcraft-ai/issues" target="_blank" class="button">
							<?php esc_html_e( 'Report Issue', 'contentcraft-ai' ); ?>
						</a>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.contentcraft-settings-container {
		display: flex;
		gap: 20px;
	}
	.contentcraft-settings-container > form {
		flex: 1;
		max-width: 800px;
	}
	.contentcraft-settings-sidebar {
		width: 300px;
	}
	.contentcraft-settings-sidebar .postbox {
		margin-bottom: 20px;
	}
	#connection-status {
		margin-left: 10px;
		font-weight: 600;
	}
	#connection-status.success {
		color: #46b450;
	}
	#connection-status.error {
		color: #dc3232;
	}
</style>


