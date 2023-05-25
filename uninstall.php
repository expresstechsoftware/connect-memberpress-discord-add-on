<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    ETS_Memberpress_Discord
 */

// If uninstall not called from WordPress, then exit.
if ( defined( 'WP_UNINSTALL_PLUGIN' )
		&& $_REQUEST['plugin'] == 'expresstechsoftwares-memberpress-discord-add-on/memberpress-discord.php'
		&& $_REQUEST['slug'] == 'expresstechsoftwares-memberpress-discord-add-on'
	&& wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'updates' )
  ) {
	$ets_memberpress_discord_data_erases = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_data_erases' ) ) );
	if ( $ets_memberpress_discord_data_erases == true ) {
		global $wpdb;
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "usermeta WHERE `meta_key` LIKE '_ets_memberpress_discord%'" );
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE `option_name` LIKE 'ets_memberpress_discord_%'" );
	}
}
