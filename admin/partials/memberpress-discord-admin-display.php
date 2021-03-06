<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    ETS_Memberpress_Discord
 * @subpackage ETS_Memberpress_Discord/admin/partials
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
$log_api_res                                  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_log_api_response' ) ) );
if (  $log_api_res ) {
	?>
	<div class="notice notice-success support-success-msg">
		<p><?php echo __( 'MemberPress - Discord logging is currently enabled. Since logs may contain sensitive information, please ensure that you only leave it enabled for as long as it is needed for troubleshooting. If you currently have a support ticket open, please do not disable logging until the Support Team has reviewed your logs.', 'connect-memberpress-discord-add-on' ); ?></p>
	</div>
	<?php
}

?>
<h1><?php echo __( 'Memberpress Discord Add On Settings', 'connect-memberpress-discord-add-on' ); ?></h1>
		<div id="outer" class="skltbs-theme-light" data-skeletabs='{ "startIndex": 1 }'>
			<ul class="skltbs-tab-group">
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="mepr_settings" ><?php echo __( 'Application details', 'connect-memberpress-discord-add-on' ); ?><span class="initialtab spinner"></span></button>
				</li>
				<?php if ( ets_memberpress_discord_check_saved_settings_status() ) : ?>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="level-mapping" ><?php echo __( 'Role Mappings', 'connect-memberpress-discord-add-on' ); ?></button>
				</li>
				<?php endif; ?>	
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="advanced" data-toggle="tab" data-event="ets_advanced"><?php echo __( 'Advanced', 'connect-memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="appearance" data-toggle="tab" data-event="ets_appearance"><?php echo __( 'Appearance', 'connect-memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="logs" data-toggle="tab" data-event="ets_logs"><?php echo __( 'Logs', 'connect-memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="docs" data-toggle="tab" data-event="ets_docs"><?php echo __( 'Documentation', 'connect-memberpress-discord-add-on' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="support" data-toggle="tab" data-event="ets_about_us"><?php echo __( 'Support', 'connect-memberpress-discord-add-on' ); ?>	
				</button>
				</li>
			</ul>
			<div class="skltbs-panel-group">
				<div id='mepr_general_settings' class="skltbs-panel">
				<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-settings.php'; ?>
				</div>
				<?php if ( ets_memberpress_discord_check_saved_settings_status() ) : ?>
				<div id='mepr_role_mapping' class="skltbs-panel">
					<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-role-level-map.php'; ?>
				</div>
				<?php endif; ?>
				<div id='mepr_advance' class="skltbs-panel">
				<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-advance.php'; ?>
				</div>
				<div id='mepr_appearance' class="skltbs-panel">
				<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-appearance.php'; ?>
				</div>
				<div id='mepr_logs' class="skltbs-panel">
				<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-error-log.php'; ?>
				</div>
				<div id='mepr_docs' class="skltbs-panel">
				<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-documentation.php'; ?>
				</div>
				<div id='mepr_support' class="skltbs-panel">
				<?php require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/pages/memberpress-discord-get-support.php'; ?>
				</div>
			</div>
		</div>
