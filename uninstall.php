<?php
/**
 * Fired when the plugin is uninstalled.
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Memberpress_Discord
 */

// If uninstall not called from WordPress, then exit.
if ( defined( 'WP_UNINSTALL_PLUGIN' )
		&& $_REQUEST['plugin'] == 'memberpress-discord/memberpress-discord.php'
		&& $_REQUEST['slug'] == 'connect-memberpress-discord-add-on'
	&& wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'updates' )
  ) {
	global $wpdb;
	  $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "usermeta WHERE `meta_key` LIKE '_ets_memberpress_discord%'" );
	  $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE `option_name` LIKE 'ets_memberpress_discord_%'" );
}
