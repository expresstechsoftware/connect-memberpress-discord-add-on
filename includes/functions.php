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
 * Log API call response
 *
 * @param INT          $user_id
 * @param STRING       $api_url
 * @param ARRAY        $api_args
 * @param ARRAY|OBJECT $api_response
 */
function ets_memberpress_discord_log_api_response( $user_id, $api_url = '', $api_args = array(), $api_response = '', $backtrace_arr = array() ) {
	
	$log_api_response = get_option( 'ets_memberpress_discord_log_api_response' );
	$uuid             = get_option( 'ets_memberpress_discord_uuid_file_name' );
	$log_file_name    = $uuid . ETS_Memberpress_Discord_Admin::$log_file_name;

	$log_string        = current_time( 'mysql' );
	$log_string  .= '==>USER::' . $user_id;
	$log_string  .= '==>URL::' . $api_url;

	unset($api_args['headers']['Authorization']);

	$log_string .= '==>ARGS::' . print_r( $api_args, true );

	$response_arr = json_decode( wp_remote_retrieve_body( $api_response ), true );

	$log_string .= '==>File::' . $backtrace_arr['file'] . '==>Line::' . $backtrace_arr['line'] . '==>Function::' . $backtrace_arr['function'] . '==>::' . print_r($response_arr,true);

	if ( $log_api_response == true ) {
		file_put_contents( WP_CONTENT_DIR . '/' . $log_file_name, $log_string . PHP_EOL, FILE_APPEND | LOCK_EX );
	}
}

/**
 * Add API error logs into log file
 *
 * @param array  $response_arr
 * @param array  $backtrace_arr
 * @param string $error_type
 * @return None
 */
function write_api_response_logs( $response_arr, $user_id, $backtrace_arr = array() ) {
	$error        = current_time( 'mysql' );
	$user_details = '';
	if ( $user_id ) {
		$user_details = '::User Id:' . $user_id;
	}
	$log_api_response = get_option( 'ets_memberpress_discord_log_api_response' );
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
	$mapped_role_ids                      = ets_memberpress_discord_get_mapped_roles( $ets_memberpress_discord_role_mapping, $membership['product_id'] );
	$MEMBER_USERNAME                      = $user_obj->user_login;
	$MEMBER_EMAIL                         = $user_obj->user_email;

	// Check if any of the mapped roles exist
	$has_role = false;
	if ( is_array( $all_roles ) ) {
		foreach ( $mapped_role_ids as $role_id ) {
			if ( array_key_exists( $role_id, $all_roles ) ) {
				$has_role = true;
				break;
			}
		}
	}

	if ( $has_role ) {
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
	//$delete_usermeta_sql = $wpdb->prepare( $usermeta_sql, $user_id );
	delete_user_meta( $user_id, '_ets_memberpress_discord_access_token' );
	//$wpdb->query( $delete_usermeta_sql );
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

/**
 * Check if pro version is active
 *
 * @return BOOL
 */
function ets_memberpress_discord_is_pro_active() {
	return defined( 'MEMBERPRESS_PRO_DISCORD_ADDON_VERSION' );
}

/**
 * Get role IDs for a membership level (handles both string and array formats)
 * This function provides backward compatibility for role mappings:
 * - Pro version with multiple roles: returns array ["role1", "role2"]
 * - Free version or single role: converts string to array ["role1"]
 *
 * @param array $role_mapping The role mapping array
 * @param int   $product_id The membership product ID
 * @return array Array of role IDs (always returns array, even for single role)
 */
function ets_memberpress_discord_get_mapped_roles( $role_mapping, $product_id ) {
	$role_key = 'level_id_' . $product_id;

	if ( ! isset( $role_mapping[ $role_key ] ) ) {
		return array();
	}

	$role_value = $role_mapping[ $role_key ];

	// Check if it's already an array (pro version with multiple roles)
	if ( is_array( $role_value ) ) {
		return $role_value;
	}

	// Single role (free version or pro with 1 role)
	return array( $role_value );
}
