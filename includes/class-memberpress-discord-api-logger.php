<?php

/**
 * Class ETS_Memberpress_Discord_Api_Logger
 *
 * Handles logging of API requests.
 *
 * @package    ETS_Memberpress_Discord
 * @subpackage ETS_Memberpress_Discord/includes
 * @author ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 *
 * @since      1.1.0
 */
class ETS_Memberpress_Discord_Api_Logger {

	/**
	 * Log API request details to the database.
	 *
	 * @param array $log_data {
	 *     The data to be logged.
	 *
	 *     @type string $api_endpoint         The API endpoint URL.
	 *     @type string $api_endpoint_version The API endpoint version.
	 *     @type string $request_params       Serialized request parameters.
	 *     @type string $api_response_header  API response headers.
	 *     @type string $api_response_body    API response body.
	 *     @type string $api_response_http_code API response HTTP code.
	 *     @type string $error_detail_code    Error detail code.
	 *     @type string $error_message        Error message.
	 *     @type int    $wp_user_id           WordPress user ID.
	 *     @type int    $discord_user_id      Discord user ID.
	 * }
	 *
	 * @since 1.1.0
	 */
	public static function log_api_request( $log_data ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'ets_memberpress_discord_api_logs';

		$wpdb->insert(
			$table_name,
			array(
				'api_endpoint'           => $log_data['api_endpoint'],
				'api_endpoint_version'   => $log_data['api_endpoint_version'],
				'request_params'         => $log_data['request_params'],
				'api_response_header'    => $log_data['api_response_header'],
				'api_response_body'      => $log_data['api_response_body'],
				'api_response_http_code' => $log_data['api_response_http_code'],
				'error_detail_code'      => $log_data['error_detail_code'],
				'error_message'          => $log_data['error_message'],
				'wp_user_id'             => $log_data['wp_user_id'],
				'discord_user_id'        => $log_data['discord_user_id'],
				'datetime'               => current_time( 'mysql' ),
			)
		);
	}

	/**
	 * Clear the logs in the MemberPress Discord API Log tab.
	 *
	 * This function truncates the logs table, removing all log entries.
	 *
	 * @since 1.1.0
	 */
	public static function clear_log_tab() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'ets_memberpress_discord_api_logs';

		$wpdb->query( "TRUNCATE TABLE $table_name" );
	}

	/**
	 * Clear MemberPress Discord API Logs.
	 *
	 * This function is used to clear all logs in the MemberPress Discord API Logs tab.
	 * Users with administrator privileges can trigger this action.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function ets_memberpress_discord_clear_log() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		self::clear_log_tab();
	}


}
