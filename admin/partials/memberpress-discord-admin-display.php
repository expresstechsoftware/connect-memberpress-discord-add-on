<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/admin/partials
 */
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
if ( isset( $_GET['save_settings_msg'] ) ) {
	?>
	<div class="notice notice-success is-dismissible support-success-msg">
		<p><?php echo esc_html( $_GET['save_settings_msg'] ); ?></p>
	</div>
	<?php
}
?>
<h1><?php echo __( 'Memberpress Discord Add On Settings', 'memberpress-discord-add-on' ); ?></h1>
		<div id="outer" class="skltbs-theme-light" data-skeletabs='{ "startIndex": 1 }'>
			<ul class="skltbs-tab-group">
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="mepr_settings" ><?php echo __( 'Application details', 'memberpress-discord-add-on' ); ?><span class="initialtab spinner"></span></button>
				</li>
				<?php if ( ets_memberpress_discord_check_saved_settings_status() ) : ?>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="level-mapping" ><?php echo __( 'Role Mappings', 'memberpress-discord-add-on' ); ?></button>
				</li>
				<?php endif; ?>	
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="advanced" data-toggle="tab" data-event="ets_advanced"><?php echo __( 'Advanced', 'memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="appearance" data-toggle="tab" data-event="ets_appearance"><?php echo __( 'Appearance', 'memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="logs" data-toggle="tab" data-event="ets_logs"><?php echo __( 'Logs', 'memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="docs" data-toggle="tab" data-event="ets_docs"><?php echo __( 'Documentation', 'memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="support" data-toggle="tab" data-event="ets_about_us"><?php echo __( 'Support', 'memberpress-discord-add-on' ); ?>	
				</button>
				</li>
			</ul>
			<div class="skltbs-panel-group">
				<div id='mepr_general_settings' class="skltbs-panel">
				<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-settings.php'; ?>
				</div>
				<?php if ( ets_memberpress_discord_check_saved_settings_status() ) : ?>
				<div id='mepr_role_mapping' class="skltbs-panel">
					<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-role-level-map.php'; ?>
				</div>
				<?php endif; ?>
				<div id='mepr_advance' class="skltbs-panel">
				<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-advance.php'; ?>
				</div>
				<div id='mepr_appearance' class="skltbs-panel">
				<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-appearance.php'; ?>
				</div>
				<div id='mepr_logs' class="skltbs-panel">
				<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-error-log.php'; ?>
				</div>
				<div id='mepr_docs' class="skltbs-panel">
				<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-documentation.php'; ?>
				</div>
				<div id='mepr_support' class="skltbs-panel">
				<?php require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-get-support.php'; ?>
				</div>
			</div>
		</div>
