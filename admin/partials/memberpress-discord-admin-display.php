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
	if( isset($_GET['save_settings_msg']) ){
		?>
			<div class="notice notice-success is-dismissible support-success-msg">
				<p><?php echo __( $_GET['save_settings_msg'], 'ets_memberpress_discord' ); ?></p>
			</div>
		<?php
	}
?>
<h1><?php echo __( 'Memberpress Discord Add On Settings', 'ets_memberpress_discord' ); ?></h1>
		<div id="outer" class="skltbs-theme-light" data-skeletabs='{ "startIndex": 1 }'>
			<ul class="skltbs-tab-group">
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="settings" ><?php echo __( 'Discord Settings', 'ets_memberpress_discord' ); ?><span class="initialtab spinner"></span></button>
				</li>
				<?php if ( ! empty( $ets_memberpress_discord_client_id ) && ! empty( $discord_client_secret ) && ! empty( $discord_bot_token ) && ! empty( $ets_memberpress_discord_redirect_url ) && ! empty( $ets_memberpress_discord_guild_id ) ) : ?>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="level-mapping" ><?php echo __( 'Role Mappings', 'ets_memberpress_discord' ); ?></button>
				</li>
				<?php endif; ?>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="advanced" data-toggle="tab" data-event="ets_advanced"><?php echo __( 'Advanced', 'ets_memberpress_discord' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="logs" data-toggle="tab" data-event="ets_logs"><?php echo __( 'Logs', 'ets_memberpress_discord' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="docs" data-toggle="tab" data-event="ets_docs"><?php echo __( 'Documentation', 'ets_memberpress_discord' ); ?>	
				</button>
				</li>
				<li class="skltbs-tab-item">
				<button class="skltbs-tab" data-identity="support" data-toggle="tab" data-event="ets_about_us"><?php echo __( 'Support', 'ets_memberpress_discord' ); ?>	
				</button>
				</li>
			</ul>
			<div class="skltbs-panel-group">
				<div class="skltbs-panel">
				<?php require_once plugin_dir_path( __FILE__ ) . 'pages/memberpress-discord-settings.php'; ?>
				</div>
				<?php if ( ! empty( $ets_memberpress_discord_client_id ) && ! empty( $discord_client_secret ) && ! empty( $discord_bot_token ) && ! empty( $ets_memberpress_discord_redirect_url ) && ! empty( $ets_memberpress_discord_guild_id ) ) : ?>
				<div class="skltbs-panel">
				<?php require_once plugin_dir_path( __FILE__ ) . 'pages/memberpress-discord-role-level-map.php'; ?>
				</div>
				<?php endif; ?>
				<div class="skltbs-panel">
				<?php require_once plugin_dir_path( __FILE__ ) . 'pages/memberpress-discord-advance.php'; ?>
				</div>
				<div class="skltbs-panel">
				<?php require_once plugin_dir_path( __FILE__ ) . 'pages/memberpress-discord-error-log.php'; ?>
				</div>
				<div class="skltbs-panel">
				<?php require_once plugin_dir_path( __FILE__ ) . 'pages/memberpress-discord-documentation.php'; ?>
				</div>
				<div class="skltbs-panel">
				<?php require_once plugin_dir_path( __FILE__ ) . 'pages/memberpress-discord-get-support.php'; ?>
				</div>
			</div>
		</div>
