<?php
/*
* common functions file.
*/


	/**
	 * This method parse url and append a query param to it.
	 *
	 * @param STRING $url
	 * @return STRING $url
	 */
function ets_memberpress_discord_get_memberpress_formated_discord_redirect_url( $url ) {
	$parsed = parse_url( $url, PHP_URL_QUERY );
	if ( $parsed === null ) {
		return $url .= '?via=mem-discord';
	} else {
		if ( stristr( $url, 'via=mem-discord' ) !== false ) {
			return $url;
		} else {
			return $url .= '&via=mem-discord';
		}
	}
}

/*
* Get current screen URL,
*
* @param NONE
* @return STRING $url
*/
function ets_memberpress_discord_get_current_screen_url() {
	$parts           = parse_url( home_url() );
		$current_uri = "{$parts['scheme']}://{$parts['host']}" . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' ) . add_query_arg( null, null );
		return $current_uri;
}

/**
 * @deprecated 1.1.0 Use ets_memberpress_discord_log_api_response_v2 instead.
 *
 * Log API call response. This function will be removed in future versions.
 *
 * @param INT          $user_id
 * @param STRING       $api_url
 * @param ARRAY        $api_args
 * @param ARRAY|OBJECT $api_response
 */
function ets_memberpress_discord_log_api_response( $user_id, $api_url = '', $api_args = array(), $api_response = '' ) {
	$log_api_response = get_option( 'ets_memberpress_discord_log_api_response' );
	if ( $log_api_response == true ) {
		$log_string  = '==>' . $api_url;
		$log_string .= '-::-' . serialize( $api_args );
		$log_string .= '-::-' . serialize( $api_response );
		write_api_response_logs( $log_string, $user_id );
	}
}

/**
 * Log API call response
 *
 * @param int          $user_id
 * @param string       $api_url
 * @param array        $api_args
 * @param array|object $api_response
 */
function ets_memberpress_discord_log_api_response_v2( $user_id, $api_url = '', $api_args = array(), $api_response = '' ) {
	$log_api_response = get_option( 'ets_memberpress_discord_log_api_response_v2' );

	if ( $log_api_response == true ) {
		$log_data = array(
			'api_endpoint'           => $api_url,
			'api_endpoint_version'   => '',
			'request_params'         => serialize( $api_args ),
			'api_response_header'    => '',
			'api_response_body'      => serialize( $api_response ),
			'api_response_http_code' => '',
			'error_detail_code'      => '',
			'error_message'          => '',
			'wp_user_id'             => $user_id,
			'discord_user_id'        => '',
		);

		write_api_response_logs_v2( $log_data, $user_id );
	}
}


/**
 * @deprecated 1.1.0 Use write_api_response_logs_v2() instead.
 *
 * Add API error logs into log file. This function will be removed in future versions.
 *
 * @param array $response_arr
 * @param int   $user_id
 * @param array $backtrace_arr
 * @return None
 */
function write_api_response_logs( $response_arr, $user_id, $backtrace_arr = array() ) {

	_deprecated_function( __FUNCTION__, '1.1.0', 'write_api_response_logs_v2()' );

	$error        = current_time( 'mysql' );
	$user_details = '';
	if ( $user_id ) {
		$user_details = '::User Id:' . $user_id;
	}
	$log_api_response = get_option( 'ets_memberpress_discord_log_api_response_v2' );
	$uuid             = get_option( 'ets_memberpress_discord_uuid_file_name' );
	$log_file_name    = $uuid . ETS_Memberpress_Discord_Admin::$log_file_name;

	if ( is_array( $response_arr ) && array_key_exists( 'code', $response_arr ) ) {
		$error .= '==>File:' . $backtrace_arr['file'] . $user_details . '::Line:' . $backtrace_arr['line'] . '::Function:' . $backtrace_arr['function'] . '::' . $response_arr['code'] . ':' . $response_arr['message'];
		file_put_contents( WP_CONTENT_DIR . '/' . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
	} elseif ( is_array( $response_arr ) && array_key_exists( 'error', $response_arr ) ) {
		$error .= '==>File:' . $backtrace_arr['file'] . $user_details . '::Line:' . $backtrace_arr['line'] . '::Function:' . $backtrace_arr['function'] . '::' . $response_arr['error'];
		file_put_contents( WP_CONTENT_DIR . '/' . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
	} elseif ( $log_api_response == true ) {
		$error .= json_encode( $response_arr ) . '::' . $user_id;
		file_put_contents( WP_CONTENT_DIR . '/' . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
	}

}

/**
 * Add API error logs into the database
 *
 * @param array $response_arr
 * @param int   $user_id
 * @param array $backtrace_arr
 * @return None
 */
function write_api_response_logs_v2( $response_arr, $user_id, $backtrace_arr = array() ) {

	$api_logger = new ETS_Memberpress_Discord_Api_Logger();

	if ( is_array( $response_arr ) && array_key_exists( 'code', $response_arr ) ) {
		update_option( 'get_response_arr_1_' . time(), $response_arr );

		$api_endpoint           = $response_arr['api_endpoint'] ?? '';
		$api_endpoint_version   = ( ! empty( $response_arr['api_endpoint_version'] ) ) ? $response_arr['api_endpoint_version'] : ets_memberpress_discord_extractEndpointVersion( $api_endpoint );
		$request_params         = $response_arr['request_params'] ?? '';
		$api_response_header    = $response_arr['api_response_header'] ?? '';
		$api_response_body      = $response_arr['api_response_body'] ?? '';
		$api_response_http_code = $response_arr['api_response_http_code'] ?? '';
		$error_detail_code      = $response_arr['code'] ?? '';
		$error_message          = $response_arr['message'] ?? '';
		$discord_user_id        = $response_arr['discord_user_id'] ?? '';

			$api_logger->log_api_request(
				array(
					'api_endpoint'           => $api_endpoint,
					'api_endpoint_version'   => $api_endpoint_version,
					'request_params'         => $request_params,
					'api_response_header'    => $api_response_header,
					'api_response_body'      => $api_response_body,
					'api_response_http_code' => $api_response_http_code,
					'error_detail_code'      => $error_detail_code,
					'error_message'          => $error_message,
					'wp_user_id'             => $user_id,
					'discord_user_id'        => $discord_user_id,
				)
			);

	} elseif ( is_array( $response_arr ) && array_key_exists( 'error', $response_arr ) ) {
		update_option( 'get_response_arr_2_' . time(), $response_arr );

			$api_logger->log_api_request(
				array(
					'api_endpoint'           => '',
					'api_endpoint_version'   => '',
					'request_params'         => '',
					'api_response_header'    => '',
					'api_response_body'      => '',
					'api_response_http_code' => '',
					'error_detail_code'      => '',
					'error_message'          => $response_arr['error'],
					'wp_user_id'             => $user_id,
					'discord_user_id'        => '',
				)
			);

	} elseif ( get_option( 'ets_memberpress_discord_log_api_response_v2' ) == true ) {
		// update_option( 'get_response_arr_3_b_' . time(), $response_arr );
		if ( is_array( $response_arr ) ) {

			$api_response_body = unserialize( $response_arr['api_response_body'] );
			$message_body = json_decode( $api_response_body['body'] );
			if ( is_object( $message_body ) ) {
				$error_detail_code = property_exists( $message_body, 'code' ) ? $message_body->code : null;
				$error_message     = property_exists( $message_body, 'message' ) ? $message_body->message : null;

			} elseif ( $api_response_body['response'] ) {

				$error_detail_code = $api_response_body['response']['code'];
				$error_message     = $api_response_body['response']['message'];

			} else {

				$error_detail_code = null;
				$error_message     = null;
			}

			$api_endpoint           = $response_arr['api_endpoint'] ?? '';
			$api_endpoint_version   = ( ! empty( $response_arr['api_endpoint_version'] ) ) ? $response_arr['api_endpoint_version'] : ets_memberpress_discord_extractEndpointVersion( $api_endpoint );
			$request_params         = $response_arr['request_params'] ?? '';
			$api_response_header    = $response_arr['api_response_header'];
			$api_response_http_code = $response_arr['api_response_http_code'] ?? '';
			$discord_user_id        = $response_arr['discord_user_id'] ?? '';

				$api_logger->log_api_request(
					array(
						'api_endpoint'           => $api_endpoint,
						'api_endpoint_version'   => $api_endpoint_version,
						'request_params'         => $request_params,
						'api_response_header'    => $api_response_header,
						'api_response_body'      => $response_arr['api_response_body'],
						'api_response_http_code' => $api_response_http_code,
						'error_detail_code'      => $error_detail_code,
						'error_message'          => $error_message,
						'wp_user_id'             => $user_id,
						'discord_user_id'        => $discord_user_id,
					)
				);

		}
	}
}




/**
 * Function to extract API endpoint version from the full endpoint URL
 *
 * @param string $endpoint
 *
 * @return string
 */
function ets_memberpress_discord_extractEndpointVersion( $endpoint ) {

	$version_pattern = '/\/v(\d+)\//';
	preg_match( $version_pattern, $endpoint, $matches );
	if ( isset( $matches[1] ) ) {
		return $matches[1];
	} else {
		return '';
	}
}
/**
 * Check API call response and detect conditions which can cause of action failure and retry should be attemped.
 *
 * @param ARRAY|OBJECT $api_response
 * @param BOOLEAN
 */
function ets_memberpress_discord_check_api_errors( $api_response ) {
	// check if response code is a WordPress error.
	if ( is_wp_error( $api_response ) ) {
		return true;
	}

	// First Check if response contain codes which should not get re-try.
	$body = json_decode( wp_remote_retrieve_body( $api_response ), true );
	if ( isset( $body['code'] ) && in_array( $body['code'], ETS_MEMBERPRESS_DISCORD_DONOT_RETRY_THESE_API_CODES ) ) {
		return false;
	}

	$response_code = strval( $api_response['response']['code'] );
	if ( isset( $api_response['response']['code'] ) && in_array( $response_code, ETS_MEMBERPRESS_DISCORD_DONOT_RETRY_HTTP_CODES ) ) {
		return false;
	}

	// check if response code is in the range of HTTP error.
	if ( ( 400 <= absint( $response_code ) ) && ( absint( $response_code ) <= 599 ) ) {
		return true;
	}
}

/**
 * Get Action data from table `actionscheduler_actions`
 *
 * @param INT $action_id
 */
function ets_memberpress_discord_as_get_action_data( $action_id ) {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.hook, aa.status, aa.args, aa.extended_args, ag.slug AS as_group FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id=ag.group_id WHERE `action_id`=%d AND ag.slug=%s', $action_id, ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

	if ( ! empty( $result ) ) {
		return $result[0];
	} else {
		return false;
	}
}

/**
 * Get the highest available last attempt schedule time
 */

function ets_memberpress_discord_get_highest_last_attempt_timestamp() {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.last_attempt_gmt FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s ORDER BY aa.last_attempt_gmt DESC limit 1', ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

	if ( ! empty( $result ) ) {
		return strtotime( $result['0']['last_attempt_gmt'] );
	} else {
		return false;
	}
}

/**
 * Get randon integer between a predefined range.
 *
 * @param INT $add_upon
 */
function ets_memberpress_discord_get_random_timestamp( $add_upon = '' ) {
	if ( $add_upon != '' && $add_upon !== false ) {
		return $add_upon + random_int( 5, 15 );
	} else {
		return strtotime( 'now' ) + random_int( 5, 15 );
	}
}


/**
 * Get pending jobs for group ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME
 */
function ets_memberpress_discord_get_all_pending_actions() {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.* FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s AND aa.status="pending" ', ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

	if ( ! empty( $result ) ) {
		return $result['0'];
	} else {
		return false;
	}
}

/**
 * Get how many times a hook is failed in a particular day.
 *
 * @param STRING $hook
 */
function ets_memberpress_discord_count_of_hooks_failures( $hook ) {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT count(last_attempt_gmt) as hook_failed_count FROM ' . $wpdb->prefix . 'actionscheduler_actions WHERE `hook`=%s AND status="failed" AND DATE(last_attempt_gmt) = %s', $hook, date( 'Y-m-d' ) ), ARRAY_A );
	if ( ! empty( $result ) ) {
		return $result['0']['hook_failed_count'];
	} else {
		return false;
	}
}

/**
 * To check settings values saved or not
 *
 * @param NONE
 * @return BOOL $status
 */
function ets_memberpress_discord_check_saved_settings_status() {
	$ets_memberpress_discord_client_id     = get_option( 'ets_memberpress_discord_client_id' );
	$ets_memberpress_discord_client_secret = get_option( 'ets_memberpress_discord_client_secret' );
	$ets_memberpress_discord_bot_token     = get_option( 'ets_memberpress_discord_bot_token' );
	$ets_memberpress_discord_redirect_url  = get_option( 'ets_memberpress_discord_redirect_url' );
	$ets_memberpress_discord_server_id     = get_option( 'ets_memberpress_discord_server_id' );

	if ( $ets_memberpress_discord_client_id && $ets_memberpress_discord_client_secret && $ets_memberpress_discord_bot_token && $ets_memberpress_discord_redirect_url && $ets_memberpress_discord_server_id ) {
			$status = true;
	} else {
			$status = false;
	}

		return $status;
}

/**
 * Get formatted message to send in DM
 *
 * @param INT $user_id
 * * @param ARRAY $membership
 * Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME], [MEMBERSHIP_ENDDATE], [MEMBERSHIP_STARTDATE]</small>
 */
function ets_memberpress_discord_get_formatted_dm( $user_id, $membership, $message ) {
	global $wpdb;
	$user_obj                             = get_user_by( 'id', $user_id );
	$ets_memberpress_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
	$all_roles                            = json_decode( get_option( 'ets_memberpress_discord_all_roles' ), true );
	$mapped_role_id                       = $ets_memberpress_discord_role_mapping[ 'level_id_' . $membership['product_id'] ];
	$MEMBER_USERNAME                      = $user_obj->user_login;
	$MEMBER_EMAIL                         = $user_obj->user_email;
	if ( is_array( $all_roles ) && array_key_exists( $mapped_role_id, $all_roles ) ) {
		$MEMBERSHIP_LEVEL = get_the_title( $membership['product_id'] );
	} else {
		$MEMBERSHIP_LEVEL = '';
	}

	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );

	if ( ! empty( $membership ) && isset( $membership['created_at'] ) && '' !== $membership['created_at'] ) {
		$MEMBERSHIP_STARTDATE = date( 'F jS, Y', strtotime( $membership['created_at'] ) );

	} else {
		$MEMBERSHIP_STARTDATE = '';
	}
	if ( ! empty( $membership ) && isset( $membership['expires_at'] ) && '0000-00-00 00:00:00' !== $membership['expires_at'] ) {
		$MEMBERSHIP_ENDDATE = date( 'F jS, Y', strtotime( $membership['expires_at'] ) );
	} elseif ( null !== $membership && '0000-00-00 00:00:00' === $membership['expires_at'] ) {
		$MEMBERSHIP_ENDDATE = 'Never';
	} else {
		$MEMBERSHIP_ENDDATE = '';
	}

	$find    = array(
		'[MEMBER_USERNAME]',
		'[MEMBER_EMAIL]',
		'[MEMBERSHIP_LEVEL]',
		'[SITE_URL]',
		'[BLOG_NAME]',
		'[MEMBERSHIP_ENDDATE]',
		'[MEMBERSHIP_STARTDATE]',
	);
	$replace = array(
		$MEMBER_USERNAME,
		$MEMBER_EMAIL,
		$MEMBERSHIP_LEVEL,
		$SITE_URL,
		$BLOG_NAME,
		$MEMBERSHIP_ENDDATE,
		$MEMBERSHIP_STARTDATE,
	);

	return str_replace( $find, $replace, $message );
}

/**
 * Get memberpress current level id
 *
 * @param INT $user_id
 * @return INT|NULL $active_memberships
 */
function ets_memberpress_discord_get_active_memberships( $user_id ) {
	$memberpress_user   = new MeprUser( $user_id );
	$active_memberships = $memberpress_user->active_product_subscriptions( 'transactions' );
	if ( $active_memberships ) {
		return $active_memberships;
	} else {
		return null;
	}
}

/**
 * Search on array by key and value
 *
 * @param ARRAY   $array
 * @param STRING  $key
 * @param VARCHAR $value
 * @return INT|NULL $active_memberships
 */
function array_search_by_key_and_value( $array, $key, $value ) {
	$results = array();

	if ( is_array( $array ) ) {
		if ( isset( $array[ $key ] ) && $array[ $key ] == $value ) {
			$results[] = $array;
		}

		foreach ( $array as $subarray ) {
			$results = array_merge( $results, array_search_by_key_and_value( $subarray, $key, $value ) );
		}
	}

	return array_key_exists( $key, $results );
}

/**
 * Get the bot name using API call
 *
 * @return NONE
 */
function ets_memberpress_discord_update_bot_name_option() {
	$guild_id          = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
	$discord_bot_token = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
	if ( $guild_id && $discord_bot_token ) {
		$discod_current_user_api = ETS_MEMBERPRESS_DISCORD_API_URL . 'users/@me';
		$app_args                = array(
			'method'  => 'GET',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
		);

		$app_response = wp_remote_get( $discod_current_user_api, $app_args );
		$response_arr = json_decode( wp_remote_retrieve_body( $app_response ), true );
		if ( is_array( $response_arr ) && array_key_exists( 'username', $response_arr ) ) {
			update_option( 'ets_memberpress_discord_connected_bot_name', $response_arr ['username'] );
		} else {
			delete_option( 'ets_memberpress_discord_connected_bot_name' );
		}
	}
}

function ets_memberpress_discord_remove_usermeta( $user_id ) {
	global $wpdb;
	$usermeta_table      = $wpdb->prefix . 'usermeta';
	$usermeta_sql        = 'DELETE FROM ' . $usermeta_table . " WHERE `user_id` = %d AND  `meta_key` LIKE '_ets_memberpress_discord%'; ";
	$delete_usermeta_sql = $wpdb->prepare( $usermeta_sql, $user_id );
	$wpdb->query( $delete_usermeta_sql );
}

function ets_memberpress_discord_get_roles_color_name( $all_roles, $mapped_role_id, $role_color, $ets_memberpress_connecttodiscord_btn ) {

	$role                                  = '<span> <i style="background-color:#' . dechex( $role_color ) . '"></i>' . $all_roles[ $mapped_role_id ] . '</span>';
	$ets_memberpress_connecttodiscord_btn .= ets_memberpress_discord_allowed_html( $role );
	return $ets_memberpress_connecttodiscord_btn;
}

function ets_memberpress_discord_allowed_html( $html_message ) {
	$allowed_html = array(
		'span' => array(),
		'i'    => array(
			'style' => array(),
		),
		'img'  => array(
			'src' => array(),
		),
	);

	return wp_kses( $html_message, $allowed_html );
}

function ets_memberpress_discord_get_user_avatar( $discord_user_id, $user_avatar, $ets_memberpress_connecttodiscord_btn ) {
	if ( $user_avatar ) {
		$avatar_url                            = '<img src="https://cdn.discordapp.com/avatars/' . $discord_user_id . '/' . $user_avatar . '.png" />';
		$ets_memberpress_connecttodiscord_btn .= ets_memberpress_discord_allowed_html( $avatar_url );
	}
	return $ets_memberpress_connecttodiscord_btn;
}

/**
 * Send DM message Rich Embed .
 *
 * @param string $message The message to send.
 */
function ets_memberpress_discord_get_rich_embed_message( $message ) {

	$blog_logo_full      = is_array( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) ) ? esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' )[0] ) : '';
	$blog_logo_thumbnail = is_array( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'thumbnail' ) ) ? esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'thumbnail' )[0] ) : '';

	$SITE_URL         = get_bloginfo( 'url' );
	$BLOG_NAME        = get_bloginfo( 'name' );
	$BLOG_DESCRIPTION = get_bloginfo( 'description' );

	$timestamp     = date( 'c', strtotime( 'now' ) );
	$convert_lines = preg_split( '/\[LINEBREAK\]/', $message );
	$fields        = array();
	if ( is_array( $convert_lines ) ) {
		for ( $i = 0; $i < count( $convert_lines ); $i++ ) {
			array_push(
				$fields,
				array(
					'name'   => '.',
					'value'  => $convert_lines[ $i ],
					'inline' => false,
				)
			);
		}
	}

	$rich_embed_message = json_encode(
		array(
			'content'    => '',
			'username'   => $BLOG_NAME,
			'avatar_url' => $blog_logo_thumbnail,
			'tts'        => false,
			'embeds'     => array(
				array(
					'title'       => '',
					'type'        => 'rich',
					'description' => $BLOG_DESCRIPTION,
					'url'         => '',
					'timestamp'   => $timestamp,
					'color'       => hexdec( '3366ff' ),
					'footer'      => array(
						'text'     => $BLOG_NAME,
						'icon_url' => $blog_logo_thumbnail,
					),
					'image'       => array(
						'url' => $blog_logo_full,
					),
					'thumbnail'   => array(
						'url' => $blog_logo_thumbnail,
					),
					'author'      => array(
						'name' => $BLOG_NAME,
						'url'  => $SITE_URL,
					),
					'fields'      => $fields,

				),
			),

		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);

	return $rich_embed_message;
}
