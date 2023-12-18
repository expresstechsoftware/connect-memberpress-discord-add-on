<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    ETS_Memberpress_Discord
 * @subpackage ETS_Memberpress_Discord/includes
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class ETS_Memberpress_Discord_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;
		$wpdb->hide_errors();

		update_option( 'ets_memberpress_discord_btn_color', '#77a02e' );
		update_option( 'ets_memberpress_discord_btn_disconnect_color', '#ff0000' );
		update_option( 'ets_memberpress_discord_loggedout_btn_text', 'Login with Discord' );
		update_option( 'ets_memberpress_discord_loggedin_btn_text', 'Connect with Discord' );
		update_option( 'ets_memberpress_discord_disconnect_btn_text', 'Disconnect Discord' );
		update_option( 'ets_memberpress_discord_member_facing_text', 'Following Roles will be assigned to you in Discord' );
		update_option( 'ets_memberpress_discord_payment_failed', true );
		update_option( 'ets_memberpress_discord_log_api_response', false );
		update_option( 'ets_memberpress_discord_retry_failed_api', true );
		update_option( 'ets_memberpress_discord_job_queue_concurrency', 1 );
		update_option( 'ets_memberpress_discord_job_queue_batch_size', 7 );
		update_option( 'ets_memberpress_discord_allow_none_member', 'yes' );
		update_option( 'ets_memberpress_discord_retry_api_count', '5' );
		update_option( 'ets_memberpress_discord_send_welcome_dm', true );
		update_option( 'ets_memberpress_discord_welcome_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Welcome, Your membership [MEMBERSHIP_LEVEL] is starting from [MEMBERSHIP_STARTDATE] at [SITE_URL] the last date of your membership is [MEMBERSHIP_ENDDATE] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_memberpress_discord_send_expiration_warning_dm', true );
		update_option( 'ets_memberpress_discord_expiration_warning_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] is expiring at [MEMBERSHIP_ENDDATE] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_memberpress_discord_send_membership_expired_dm', true );
		update_option( 'ets_memberpress_discord_expiration_expired_message', 'Hi [MEMBER_USERNAME] ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] is expired at [MEMBERSHIP_ENDDATE] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );
		update_option( 'ets_memberpress_discord_send_membership_cancel_dm', true );
		update_option( 'ets_memberpress_discord_cancel_message', 'Hi [MEMBER_USERNAME], ([MEMBER_EMAIL]), Your membership [MEMBERSHIP_LEVEL] at [BLOG_NAME] is cancelled, Regards, [SITE_URL]' );
		update_option( 'ets_memberpress_discord_uuid_file_name', wp_generate_uuid4() );
		update_option( 'ets_memberpress_discord_data_erases', false );
		update_option( 'ets_memberpress_discord_embed_messaging_feature', false );

		$table_name      = $wpdb->prefix . 'ets_memberpress_discord_api_logs';
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
			id INT AUTO_INCREMENT PRIMARY KEY,
			api_endpoint VARCHAR(255),
			api_endpoint_version VARCHAR(10),
			request_params LONGTEXT,
			api_response_header LONGTEXT,
			api_response_body LONGTEXT,
			api_response_http_code VARCHAR(10),
			error_detail_code VARCHAR(100),
			error_message TEXT,
			wp_user_id INT,
			discord_user_id BIGINT,
			datetime DATETIME DEFAULT CURRENT_TIMESTAMP
		) $charset_collate;";

		dbDelta( $sql );
	}

}
