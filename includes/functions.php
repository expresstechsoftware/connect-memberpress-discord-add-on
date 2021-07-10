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
function get_memberpress_formated_discord_redirect_url( $url ) {
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

		$logs = new memberpress_Discord_Logs();
		$logs->write_api_response_logs( $log_string, $user_id );
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
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.hook, aa.status, aa.args, ag.slug AS as_group FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id=ag.group_id WHERE `action_id`=%d AND ag.slug=%s', $action_id, ETS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

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
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.last_attempt_gmt FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s ORDER BY aa.last_attempt_gmt DESC limit 1', ETS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

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
 * Get pending jobs for group ETS_DISCORD_AS_GROUP_NAME
 */
function ets_memberpress_discord_get_all_pending_actions() {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.* FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s AND aa.status="pending" ', ETS_DISCORD_AS_GROUP_NAME ), ARRAY_A );

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
function ets_memberpress_discord_get_current_level_id( $user_id ) {
	$membership_level = memberpress_getMembershipLevelForUser( $user_id );
	if ( $membership_level ) {
		$curr_level_id = sanitize_text_field( trim( $membership_level->ID ) );
		return $curr_level_id;
	} else {
		return null;
	}
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

