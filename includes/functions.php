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
		return $url .= '?via=discord';
	} else {
		if ( stristr( $url, 'via=discord' ) !== false ) {
			return $url;
		} else {
			return $url .= '&via=discord';
		}
	}
}

/**
 * Log API call response
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
 * Add API error logs into log file
 *
 * @param array  $response_arr
 * @param array  $backtrace_arr
 * @param string $error_type
 * @return None
 */
	function write_api_response_logs( $response_arr, $user_id, $backtrace_arr = array() ) {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		
		$error        = current_time( 'mysql' );
		$user_details = '';
		if ( $user_id ) {
			$user_details = '::User Id:' . $user_id;
		}
		$log_api_response = get_option( 'ets_memberpress_discord_log_api_response' );
		$log_file_name    = Memberpress_Discord_Admin::$log_file_name;

		if ( is_array( $response_arr ) && array_key_exists( 'code', $response_arr ) ) {
			$error .= '==>File:' . $backtrace_arr['file'] . $user_details . '::Line:' . $backtrace_arr['line'] . '::Function:' . $backtrace_arr['function'] . '::' . $response_arr['code'] . ':' . $response_arr['message'];
			file_put_contents( MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
		} elseif ( is_array( $response_arr ) && array_key_exists( 'error', $response_arr ) ) {
			$error .= '==>File:' . $backtrace_arr['file'] . $user_details . '::Line:' . $backtrace_arr['line'] . '::Function:' . $backtrace_arr['function'] . '::' . $response_arr['error'];
			file_put_contents( MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
		} elseif ( $log_api_response == true ) {
			$error .= json_encode( $response_arr ) . '::' . $user_id;
			file_put_contents( MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
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
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.hook, aa.status, aa.args, ag.slug AS as_group FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id=ag.group_id WHERE `action_id`=%d AND ag.slug=%s', $action_id, MEMBERPRESS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

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
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.last_attempt_gmt FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s ORDER BY aa.last_attempt_gmt DESC limit 1', MEMBERPRESS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

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
 * Get pending jobs for group MEMBERPRESS_DISCORD_AS_GROUP_NAME
 */
function ets_memberpress_discord_get_all_pending_actions() {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.* FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s AND aa.status="pending" ', MEMBERPRESS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

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
 * Get memberpress current level id
 *
 * @param INT $user_id
 * @return INT|NULL $curr_level_id
 */
function ets_memberpress_discord_get_current_level_id( $user ) {
	$active_prodcuts = $user->active_product_subscriptions('ids');
	if ( $active_prodcuts ) {
		$curr_level_id = sanitize_text_field( trim( $active_prodcuts[0] ) );
		return $curr_level_id;
	} else {
		return null;
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
		$ets_memberpress_discord_guild_id      = get_option( 'ets_memberpress_discord_guild_id' );

		if ( $ets_memberpress_discord_client_id && $ets_memberpress_discord_client_secret && $ets_memberpress_discord_bot_token && $ets_memberpress_discord_redirect_url && $ets_memberpress_discord_guild_id ) {
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
 * Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME], [MEMBERSHIP_ENDDATE], [MEMBERSHIP_STARTDATE]</small>
 */
function ets_memberpress_discord_get_formatted_dm( $user_id, $level_id, $message ) {
	global $wpdb;
	$user_obj         = get_user_by( 'id', $user_id );
	$level            = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->memberpress_membership_levels WHERE id = %d LIMIT 1", $level_id ) );
	$membership_level = memberpress_getMembershipLevelForUser( $user_id );

	$MEMBER_USERNAME = $user_obj->user_login;
	$MEMBER_EMAIL    = $user_obj->user_email;
	if ( $membership_level !== false ) {
		$MEMBERSHIP_LEVEL = $membership_level->name;
	} elseif ( $level !== null ) {
		$MEMBERSHIP_LEVEL = $level->name;
	} else {
		$MEMBERSHIP_LEVEL = '';
	}

	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );

	if ( $membership_level !== false && isset( $membership_level->startdate ) && $membership_level->startdate != '' ) {
		$MEMBERSHIP_STARTDATE = date( 'F jS, Y', $membership_level->startdate );

	} else {
		$MEMBERSHIP_STARTDATE = '';
	}
	if ( $membership_level !== false && isset( $membership_level->enddate ) && $membership_level->enddate != '' ) {
		$MEMBERSHIP_ENDDATE = date( 'F jS, Y', $membership_level->enddate );
	} elseif ( $level !== null && $level->expiration_period == '' ) {
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

