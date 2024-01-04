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
	 * Display MemberPress Discord API Logs
	 *
	 * This function retrieves and displays the logs from the MemberPress Discord API Logs table.
	 *
	 * @since 1.1.0
	 *
	 * @global wpdb $wpdb WordPress database object.
	 *
	 * @return string|int
	 */
	public static function ets_memberpress_discord_display_log_data() {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'ets_memberpress_discord_api_logs';
		$per_page     = 10;
		$current_page = max( 1, get_query_var( 'paged' ) );
		$sort_by      = isset( $_GET['sort_by'] ) ? sanitize_key( $_GET['sort_by'] ) : 'datetime';
		$sort_order   = isset( $_GET['sort_order'] ) ? strtoupper( sanitize_text_field( $_GET['sort_order'] ) ) : 'DESC';

		$api_endpoint      = isset( $_GET['api-endpoint'] ) ? sanitize_text_field( $_GET['api-endpoint'] ) : '';
		$api_response_code = isset( $_GET['api-response-code'] ) ? sanitize_text_field( $_GET['api-response-code'] ) : '';
		$error_message     = isset( $_GET['error-message'] ) ? sanitize_text_field( $_GET['error-message'] ) : '';
		$wp_user_id        = isset( $_GET['wp-user-id'] ) ? sanitize_text_field( $_GET['wp-user-id'] ) : '';
		$discord_user_id   = isset( $_GET['discord-user-id'] ) ? sanitize_text_field( $_GET['discord-user-id'] ) : '';
		$datetime          = isset( $_GET['datetime'] ) ? sanitize_text_field( $_GET['datetime'] ) : '';

		$where_clause = ' WHERE 1=1';

		if ( isset( $_GET['ets-log-search-form'] ) ) {
			if ( ! empty( $api_response_code ) ) {
				$where_clause .= $wpdb->prepare( ' AND api_response_code = %s', $api_response_code );
			}

			if ( ! empty( $error_message ) ) {
				$where_clause .= $wpdb->prepare( ' AND error_message LIKE %s', '%' . $wpdb->esc_like( $error_message ) . '%' );
			}

			if ( ! empty( $wp_user_id ) ) {
				$where_clause .= $wpdb->prepare( ' AND wp_user_id = %s', $wp_user_id );
			}

			if ( ! empty( $discord_user_id ) ) {
				$where_clause .= $wpdb->prepare( ' AND discord_user_id = %s', $discord_user_id );
			}

			if ( ! empty( $datetime ) ) {
				$where_clause .= $wpdb->prepare( ' AND datetime = %s', $datetime );
			}

			if ( ! empty( $api_endpoint ) ) {
				$where_clause .= $wpdb->prepare( ' AND api_endpoint LIKE %s', '%' . $wpdb->esc_like( $api_endpoint ) . '%' );
			}

			$logs = $wpdb->get_results(
				"SELECT * FROM $table_name" . $where_clause . ' ORDER BY datetime DESC LIMIT ' . ( $current_page - 1 ) * $per_page . ", $per_page"
			);
		} else {
			$logs = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY datetime DESC LIMIT " . ( $current_page - 1 ) * $per_page . ", $per_page" );
		}

		// $logs         = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY $sort_by $sort_order LIMIT " . ( $current_page - 1 ) * $per_page . ", $per_page" );

		if ( $logs ) {
			echo '<table class="log-table">';
			echo '<thead>';
			echo '<tr>';
			echo '<th><a href="?page=memberpress-discord&sort_by=id&sort_order=' . ( $sort_order === 'ASC' ? 'DESC' : 'ASC' ) . '#mepr_logs">ID</a></th>';
			echo '<th>API Endpoint</th>';
			echo '<th>API Endpoint Version</th>';
			echo '<th>Request Params</th>';
			echo '<th>API Response Header</th>';
			echo '<th>API Response Body</th>';
			echo '<th>API Response HTTP Code</th>';
			echo '<th>Error Detail Code</th>';
			echo '<th>Error Message</th>';
			echo '<th>WordPress User ID</th>';
			echo '<th>Discord User ID</th>';
			echo '<th><a href="?page=memberpress-discord&sort_by=datetime&sort_order=' . ( $sort_order === 'ASC' ? 'DESC' : 'ASC' ) . '#mepr_logs">Timestamp</a></th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			foreach ( $logs as $log ) {

				$unserialize_request_params = unserialize( $log->request_params );
				if ( is_array( $unserialize_request_params ) ) {
					$get_requets_params = self::ets_recursive_array_loop( $unserialize_request_params );
				}

				if ( ! empty( $log->api_response_header ) ) {
					$get_api_response_header = self::ets_extract_header_info( $log->api_response_header );
				} else {
					$get_api_response_header = '-';
				}
				echo '<pre>';
				var_dump( unserialize( $log->api_response_body ) );
				echo '</pre>';
				$get_api_response_body = '-';
				// if ( ! empty( $log->api_response_body ) ) {
				// $get_api_response_body = self::ets_extract_body_info( $log->api_response_body );
				// } else {
				// $get_api_response_body = '-';
				// }

				echo '<tr>';
				echo '<td>' . $log->id . '</td>';
				echo '<td>' . esc_html( $log->api_endpoint ) . '</td>';
				echo '<td>' . esc_html( $log->api_endpoint_version ) . '</td>';
				echo '<td>' . $get_requets_params . '</td>';

				echo '<td>' . $get_api_response_header . '</td>';

				echo '<td>' . $get_api_response_body . '</td>';

				echo '<td>' . esc_html( $log->api_response_http_code ) . '</td>';
				echo '<td>' . esc_html( $log->error_detail_code ) . '</td>';
				echo '<td>' . esc_html( $log->error_message ) . '</td>';
				echo '<td>' . esc_html( $log->wp_user_id ) . '</td>';
				echo '<td>' . esc_html( $log->discord_user_id ) . '</td>';
				echo '<td>' . esc_html( $log->datetime ) . '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table>';

			$total_logs  = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" );
			$total_pages = ceil( $total_logs / $per_page );

			echo '<div class="log-pagination">';
			echo paginate_links(
				array(
					'total'        => $total_pages,
					'current'      => $current_page,
					'format'       => '?paged=%#%',
					'add_args'     => array(
						'page'       => 'memberpress-discord',
						'sort_by'    => $sort_by,
						'sort_order' => $sort_order,
					),
					'add_fragment' => '#mepr_logs',
				)
			);
			echo '</div>';
		} else {
			return 0;
		}
	}

	/**
	 * Return the formatted data
	 *
	 * @param mix The data to serialize
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public static function ets_unserialize_and_format( $data ) {

		// _deprecated_function( __FUNCTION__, 'For test prupose . to be removed ' );
		$unserialized_data = unserialize( $data );
		$formatted_data    = json_encode( $unserialized_data, JSON_PRETTY_PRINT );

		return $formatted_data;
	}

	/**
	 * Recursively loop through a nested array and generate HTML list.
	 *
	 * @param array $array The nested array to loop through.
	 *
	 * @return string The generated HTML list.
	 */
	public static function ets_recursive_array_loop( $array ) {
		$html = '<ul>';

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {

				$html .= '<li><b>' . $key . ' :</b> ' . self::ets_recursive_array_loop( $value ) . '</li>';
			} else {
				if ( $key === 'Authorization' && strpos( $value, 'Bot' ) !== false ) {
					$html .= '<li><b>' . $key . ': </b> Bot XXXXXXXXXX</li>';
				} else {
					$html .= '<li><b>' . $key . ' : </b> ' . $value . '</li>';
				}
			}
		}
		$html .= '</ul>';

		return $html;
	}

	/**
	 * Extract relevant information from serialized header data.
	 *
	 * @param string $serialized_data Serialized header data.
	 * @return array Extracted information.
	 */
	public static function ets_extract_header_info( $serialized_data ) {

		$header_data = unserialize( $serialized_data );

		if ( $header_data === false ) {
			return 'Failed to unserialize header data';
		}

		$formatted_info = self::ets_recursive_array_loop( $header_data );
		// foreach ( $header_data as $key => $value ) {
		// $formatted_info .= $key . ': ' . $value . '<br>';
		// }

		return $formatted_info;
	}

	/**
	 * Extract relevant information from serialized body data.
	 *
	 * @param string $serialized_data Serialized body data.
	 * @return string Formatted information for display.
	 */
	public static function ets_extract_body_info( $serialized_data ) {

		$body_data = unserialize( $serialized_data );

		if ( $body_data === false ) {
			return 'Failed to unserialize body data';
		}

		// If the first unserialize was successful, attempt a second time
		$body_data = unserialize( $body_data );

		if ( $body_data === false ) {
			return 'Failed to unserialize body data on the second attempt';
		}

		$formatted_info = $formatted_info = self::ets_recursive_array_loop( $body_data );

		return $formatted_info;
	}

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

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'memberpress_discord_clear_log_table' ) {
			self::clear_log_tab();
			$message      = esc_html__( 'Log table cleared successfully.', 'connect-memberpress-discord-add-on' );
			$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_logs';
			wp_safe_redirect( $pre_location );
		}

	}


}
