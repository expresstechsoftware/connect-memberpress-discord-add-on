<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/includes
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Memberpress_Discord_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

	}

	/**
	 * Set default settings on activation
	*/
	public static function memberpress_set_default_setting_values() {
		update_option( 'ets_memberpress_discord_payment_failed', true );
		update_option( 'ets_memberpress_discord_log_api_response', false );
		update_option( 'ets_memberpress_retry_failed_api', true );
		update_option( 'ets_memberpress_discord_job_queue_concurrency', 1 );
		update_option( 'ets_memberpress_discord_job_queue_batch_size', 7 );
		update_option( 'ets_memberpress_allow_none_member', 'yes' );
		update_option( 'ets_memberpress_retry_api_count', '5' );
		update_option( 'ets_memberpress_discord_send_welcome_dm', true );
		update_option( 'ets_memberpress_discord_welcome_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Welcome, Your membership [MEMBERSHIP_LEVEL] is starting from [MEMBERSHIP_STARTDATE] at [SITE_URL] the last date of your membership is [MEMBERSHIP_STARTDATE] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_memberpress_discord_send_expiration_warning_dm', true );
		update_option( 'ets_memberpress_discord_expiration_warning_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] is expiring at [MEMBERSHIP_ENDDATE] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_memberpress_discord_send_membership_expired_dm', true );
		update_option( 'ets_memberpress_discord_expiration_expired_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] is expired at [MEMBERSHIP_ENDDATE] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_memberpress_discord_send_membership_cancel_dm', true );
		update_option( 'ets_memberpress_discord_cancel_message', 'Hi [MEMBER_USERNAME], ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] at [BLOG_NAME] is cancelled, Regards, [SITE_URL]' );
	}	

}
