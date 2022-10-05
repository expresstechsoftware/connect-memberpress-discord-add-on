<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    ETS_Memberpress_Discord
 * @subpackage ETS_Memberpress_Discord/public
 */

class ETS_Memberpress_Discord_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The admin class instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $admin_cls_instance;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of the plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_admin ) {

		$this->plugin_name        = $plugin_name;
		$this->version            = $version;
		$this->admin_cls_instance = $plugin_admin;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$min_css = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_style( $this->plugin_name . 'public_css', plugin_dir_url( __FILE__ ) . 'css/memberpress-discord-public' . $min_css . '.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$min_js = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_script( $this->plugin_name . 'public_js', plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-public' . $min_js . '.js', array( 'jquery' ), $this->version, false );
		$script_params = array(
			'admin_ajax'                           => admin_url( 'admin-ajax.php' ),
			'permissions_const'                    => ETS_MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
			'ets_memberpress_discord_public_nonce' => wp_create_nonce( 'ets-memberpress-discord-public-ajax-nonce' ),
		);

		wp_localize_script( $this->plugin_name . 'public_js', 'etsMemberpresspublicParams', $script_params );

	}

	/**
	 * Add discord connection buttons.
	 *
	 * @since    1.0.0
	 */
	public function ets_memberpress_discord_add_connect_button() {

		wp_enqueue_style( $this->plugin_name . 'public_css' );
		wp_enqueue_script( $this->plugin_name . 'public_js' );
		$user_id                                      = sanitize_text_field( trim( get_current_user_id() ) );
		$access_token                                 = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$allow_none_member                            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_allow_none_member' ) ) );
		$default_role                                 = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
		$ets_memberpress_discord_role_mapping         = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$all_roles                                    = json_decode( get_option( 'ets_memberpress_discord_all_roles' ), true );
		$roles_color                                  = unserialize( get_option( 'ets_memberpress_discord_roles_color' ) );
		$active_memberships                           = ets_memberpress_discord_get_active_memberships( $user_id );
		$mapped_role_ids                              = array();
		$ets_memberpress_discord_loggedin_button_text = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_loggedin_btn_text' ) ) );
		$ets_memberpress_discord_btn_color            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_btn_color' ) ) );
		$ets_memberpress_discord_disconnect_btn_text  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_disconnect_btn_text' ) ) );
		$ets_memberpress_discord_btn_disconnect_color = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_btn_disconnect_color' ) ) );
		if ( $active_memberships && is_array( $all_roles ) ) {
			foreach ( $active_memberships as $active_membership ) {
				if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $active_membership->product_id, $ets_memberpress_discord_role_mapping ) ) {
					$mapped_role_id = $ets_memberpress_discord_role_mapping[ 'level_id_' . $active_membership->product_id ];
					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						array_push( $mapped_role_ids, $mapped_role_id );
					}
				}
			}
		}
		$default_role_name = '';
		if ( 'none' !== $default_role && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = $all_roles[ $default_role ];
		}
		$ets_memberpress_connecttodiscord_btn = '';
		if ( ets_memberpress_discord_check_saved_settings_status() ) {
			if ( $access_token ) {
				$disconnect_btn_bg_color               = 'style="background-color:' . esc_attr( $ets_memberpress_discord_btn_disconnect_color ) . '"';
				$discord_user_name                     = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_username', true ) ) );
				$discord_user_id                       = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
				$discord_user_avatar                   = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_avatar', true ) ) );
				$ets_memberpress_connecttodiscord_btn .= '<div><label class="ets-connection-lbl">' . esc_html__( 'Discord connection', 'connect-memberpress-discord-add-on' ) . '</label>';
				$ets_memberpress_connecttodiscord_btn .= '<a href="#" class="ets-btn btn-disconnect" ' . $disconnect_btn_bg_color . ' data-user-id="' . esc_attr( $user_id ) . '">' . esc_html__( $ets_memberpress_discord_disconnect_btn_text, 'connect-memberpress-discord-add-on' ) . ETS_Memberpress_Discord::get_discord_logo_white() . '</a>';
				$ets_memberpress_connecttodiscord_btn .= '<span class="ets-spinner"></span>';
				if ( $mapped_role_ids || $default_role_name ) {
					$ets_memberpress_connecttodiscord_btn .= '<p class="ets_assigned_role">';
					$ets_memberpress_connecttodiscord_btn .= esc_html__( 'Following Roles was assigned to you in Discord: ', 'connect-memberpress-discord-add-on' );
					foreach ( $mapped_role_ids as $mapped_role_id ) {
						$ets_memberpress_connecttodiscord_btn = ets_memberpress_discord_get_roles_color_name( $all_roles, $mapped_role_id, $roles_color[ $mapped_role_id ], $ets_memberpress_connecttodiscord_btn );
					}
					if ( $default_role_name ) {
						$ets_memberpress_connecttodiscord_btn = ets_memberpress_discord_get_roles_color_name( $all_roles, $default_role, $roles_color[ $default_role ], $ets_memberpress_connecttodiscord_btn );// esc_html( $default_role_name );
					}
					$ets_memberpress_connecttodiscord_btn .= '</p><p class="ets_assigned_role">';
					$ets_memberpress_connecttodiscord_btn .= esc_html__( 'Connected account: ' . $discord_user_name, 'connect-memberpress-discord-add-on' );
					$ets_memberpress_connecttodiscord_btn  = ets_memberpress_discord_get_user_avatar( $discord_user_id, $discord_user_avatar, $ets_memberpress_connecttodiscord_btn );
					$ets_memberpress_connecttodiscord_btn .= '</p></div>';
				}
			} elseif ( current_user_can( 'memberpress_authorized' ) && $mapped_role_ids || $allow_none_member == 'yes' ) {
				$connect_btn_bg_color                  = 'style="background-color:' . esc_attr( $ets_memberpress_discord_btn_color ) . '"';
				$ets_memberpress_connecttodiscord_btn .= '<div><label class="ets-connection-lbl">' . esc_html__( 'Discord connection', 'connect-memberpress-discord-add-on' ) . '</label>';
				$ets_memberpress_connecttodiscord_btn .= '<a href="?action=memberpress-discord-login" class="btn-connect ets-btn" ' . $connect_btn_bg_color . ' >' . esc_html__( $ets_memberpress_discord_loggedin_button_text, 'connect-memberpress-discord-add-on' ) . ETS_Memberpress_Discord::get_discord_logo_white() . '</a>';
				if ( $mapped_role_ids || $default_role_name ) {
					$ets_memberpress_connecttodiscord_btn .= '<p class="ets_assigned_role">';
					$ets_memberpress_connecttodiscord_btn .= esc_html__( 'Following Roles will be assigned to you in Discord: ', 'connect-memberpress-discord-add-on' );
					foreach ( $mapped_role_ids as $mapped_role_id ) {
						$ets_memberpress_connecttodiscord_btn = ets_memberpress_discord_get_roles_color_name( $all_roles, $mapped_role_id, $roles_color[ $mapped_role_id ], $ets_memberpress_connecttodiscord_btn );
					}
					if ( $default_role_name ) {
						$ets_memberpress_connecttodiscord_btn = ets_memberpress_discord_get_roles_color_name( $all_roles, $default_role, $roles_color[ $default_role ], $ets_memberpress_connecttodiscord_btn );// esc_html( $default_role_name );
					}
					$ets_memberpress_connecttodiscord_btn .= '</p></div>';
				}
			}
		}
		return $ets_memberpress_connecttodiscord_btn;
	}

	/**
	 * Show status of Memberpress connection with Discord user
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_memberpress_show_discord_button() {
		$show = apply_filters( 'ets_memberpress_show_connect_button_on_profile', true );
		if ( $show ) {
			echo do_shortcode( '[mepr_discord_button]' );
		}
	}

	/**
	 * For authorization process call discord API
	 *
	 * @param NONE
	 * @return OBJECT REST API response
	 */
	public function ets_memberpress_discord_discord_api_callback() {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			if ( isset( $_GET['code'] ) && isset( $_GET['via'] ) && $_GET['via'] == 'mem-discord' ) {
				$membership_private_obj = ets_memberpress_discord_get_active_memberships( $user_id );
				$active_memberships     = array();
				if ( ! empty( $membership_private_obj ) ) {
					foreach ( $membership_private_obj as $memberships ) {
						$membership_arr = array(
							'product_id' => $memberships->product_id,
							'txn_number' => $memberships->trans_num,
							'created_at' => $memberships->created_at,
							'expires_at' => $memberships->expires_at,
						);
						array_push( $active_memberships, $membership_arr );
					}
				}
				$code     = sanitize_text_field( trim( $_GET['code'] ) );
				$response = $this->ets_memberpress_create_discord_auth_token( $code, $user_id, $active_memberships );

				if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
					$res_body              = json_decode( wp_remote_retrieve_body( $response ), true );
					$discord_exist_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
					if ( is_array( $res_body ) ) {
						if ( array_key_exists( 'access_token', $res_body ) ) {
							$access_token = sanitize_text_field( trim( $res_body['access_token'] ) );
							$user_body    = $this->get_discord_current_user( $access_token );

							$this->memberpress_catch_discord_auth_callback( $res_body, $user_body, $user_id );

							if ( is_array( $user_body ) && array_key_exists( 'id', $user_body ) ) {
								$_ets_memberpress_discord_user_id = sanitize_text_field( trim( $user_body['id'] ) );
								if ( $discord_exist_user_id === $_ets_memberpress_discord_user_id ) {
									foreach ( $active_memberships as $active_membership ) {
										$_ets_memberpress_discord_role_id = get_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_membership['txn_number'], true );
										if ( ! empty( $_ets_memberpress_discord_role_id ) && $_ets_memberpress_discord_role_id['role_id'] != 'none' ) {
											$this->admin_cls_instance->memberpress_delete_discord_role( $user_id, $_ets_memberpress_discord_role_id['role_id'] );
										}
									}
								}
								update_user_meta( $user_id, '_ets_memberpress_discord_user_id', $_ets_memberpress_discord_user_id );
								$this->ets_memberpress_discord_add_member_in_guild( $_ets_memberpress_discord_user_id, $user_id, $access_token, $active_memberships );
							}
						}
					}
				}
			}
		} else {
			if ( isset( $_GET['code'] ) && isset( $_GET['via'] ) && $_GET['via'] == 'mem-discord' ) {
				$code     = sanitize_text_field( trim( $_GET['code'] ) );
				$response = $this->ets_memberpress_create_discord_auth_token( $code, 'none_wp_user', '' );
				if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
					$res_body = json_decode( wp_remote_retrieve_body( $response ), true );
					if ( is_array( $res_body ) ) {
						if ( array_key_exists( 'access_token', $res_body ) ) {
							$access_token       = sanitize_text_field( trim( $res_body['access_token'] ) );
							$user_body          = $this->get_discord_current_user( $access_token );
							$discord_user_email = $user_body['email'];
							$password           = wp_generate_password( 12, true, false );
							if ( email_exists( $discord_user_email ) ) {
								$current_user = get_user_by( 'email', $discord_user_email );
								$user_id      = $current_user->ID;
							} else {
								$user_id = wp_create_user( $discord_user_email, $password, $discord_user_email );
								wp_new_user_notification( $user_id, null, $password );
							}
							$this->memberpress_catch_discord_auth_callback( $res_body, $user_body, $user_id );

							wp_set_auth_cookie( $user_id, false, '', '' );
							$discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
							$this->ets_memberpress_discord_add_member_in_guild( $discord_user_id, $user_id, $access_token, '' );
							if ( isset( $_COOKIE['ets_memberpress_discord_page'] ) ) {
								wp_safe_redirect( urldecode_deep( $_COOKIE['ets_memberpress_discord_page'] ) );
								exit();
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Add new member into discord guild
	 *
	 * @param INT    $_ets_memberpress_discord_user_id
	 * @param INT    $user_id
	 * @param STRING $access_token
	 * @param ARRAY  $active_memberships
	 * @return NONE
	 */
	public function ets_memberpress_discord_add_member_in_guild( $_ets_memberpress_discord_user_id, $user_id, $access_token, $active_memberships ) {
		$allow_none_member = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_allow_none_member' ) ) );
		if ( ! empty( $active_memberships ) || 'yes' === $allow_none_member ) {
			// It is possible that we may exhaust API rate limit while adding members to guild, so handling off the job to queue.
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_add_member_to_guild', array( $_ets_memberpress_discord_user_id, $user_id, $access_token, $active_memberships ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}

	/**
	 * Method to add new members to discord guild.
	 *
	 * @param INT    $_ets_memberpress_discord_user_id
	 * @param INT    $user_id
	 * @param STRING $access_token
	 * @param ARRAY  $active_memberships
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_handler_add_member_to_guild( $_ets_memberpress_discord_user_id, $user_id, $access_token, $active_memberships ) {
		// Since we using a queue to delay the API call, there may be a condition when a member is delete from DB. so put a check.
		if ( get_userdata( $user_id ) === false ) {
			return;
		}
		$guild_id                                = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
		$discord_bot_token                       = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$default_role                            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
		$ets_memberpress_discord_role_mapping    = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$discord_role                            = '';
		$ets_memberpress_discord_send_welcome_dm = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_welcome_dm' ) ) );
		$discord_roles                           = array();
		if ( is_array( $active_memberships ) ) {
			foreach ( $active_memberships as $active_membership ) {
				if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $active_membership['product_id'], $ets_memberpress_discord_role_mapping ) ) {
						$discord_role = sanitize_text_field( trim( $ets_memberpress_discord_role_mapping[ 'level_id_' . $active_membership['product_id'] ] ) );
						array_push( $discord_roles, $discord_role );
				}
			}
		}

		$guilds_memeber_api_url = ETS_MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id;
		$guild_args             = array(
			'method'  => 'PUT',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
			'body'    => wp_json_encode(
				array(
					'access_token' => $access_token,
				)
			),
		);
		$guild_response         = wp_remote_post( $guilds_memeber_api_url, $guild_args );
		ets_memberpress_discord_log_api_response( $user_id, $guilds_memeber_api_url, $guild_args, $guild_response );
		if ( ets_memberpress_discord_check_api_errors( $guild_response ) ) {

			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );
			write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			// this should be catch by Action schedule failed action.
			throw new Exception( 'Failed in function ets_as_handler_add_member_to_guild' );
		}
		if ( is_array( $discord_roles ) ) {
			foreach ( $discord_roles as $key => $discord_role ) {
				$assigned_role = array(
					'role_id'    => $discord_role,
					'product_id' => $active_memberships[ $key ]['product_id'],
				);
				update_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_memberships[ $key ]['txn_number'], $assigned_role );
				if ( $discord_role && $discord_role != 'none' && isset( $user_id ) ) {
					$this->put_discord_role_api( $user_id, $discord_role );
				}
			}
		}

		if ( $default_role && 'none' !== $default_role && isset( $user_id ) ) {
			$this->put_discord_role_api( $user_id, $default_role );
			update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
		}
		if ( empty( get_user_meta( $user_id, '_ets_memberpress_discord_join_date', true ) ) ) {
			update_user_meta( $user_id, '_ets_memberpress_discord_join_date', current_time( 'Y-m-d H:i:s' ) );
		}

		// Send welcome message.
		if ( is_array( $active_memberships ) && true == $ets_memberpress_discord_send_welcome_dm ) {
			foreach ( $active_memberships as $active_membership ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_welcome_dm', array( $user_id, $active_membership, 'welcome' ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
			}
		}
	}

	/**
	 * API call to change discord user role
	 *
	 * @param INT  $user_id
	 * @param INT  $role_id
	 * @param BOOL $is_schedule
	 * @return object API response
	 */
	public function put_discord_role_api( $user_id, $role_id, $is_schedule = true ) {
		if ( $is_schedule ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_schedule_member_put_role', array( $user_id, $role_id, $is_schedule ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		} else {
			$this->ets_memberpress_discord_as_handler_put_memberrole( $user_id, $role_id, $is_schedule );
		}
	}

	/**
	 * Action Schedule handler for mmeber change role discord.
	 *
	 * @param INT  $user_id
	 * @param INT  $role_id
	 * @param BOOL $is_schedule
	 * @return object API response
	 */
	public function ets_memberpress_discord_as_handler_put_memberrole( $user_id, $role_id, $is_schedule ) {
		$access_token                     = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$guild_id                         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
		$_ets_memberpress_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$discord_bot_token                = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$discord_change_role_api_url      = ETS_MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id . '/roles/' . $role_id;

		if ( $access_token && $_ets_memberpress_discord_user_id ) {
			$param = array(
				'method'  => 'PUT',
				'headers' => array(
					'Content-Type'   => 'application/json',
					'Authorization'  => 'Bot ' . $discord_bot_token,
					'Content-Length' => 0,
				),
			);

			$response = wp_remote_get( $discord_change_role_api_url, $param );

			ets_memberpress_discord_log_api_response( $user_id, $discord_change_role_api_url, $param, $response );
			if ( ets_memberpress_discord_check_api_errors( $response ) ) {
				$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				if ( $is_schedule ) {
					// this exception should be catch by action scheduler.
					throw new Exception( 'Failed in function ets_memberpress_discord_as_handler_put_memberrole' );
				}
			}
		}
	}

	/**
	 * Get Discord user details from API
	 *
	 * @param STRING $access_token
	 * @return OBJECT REST API response
	 */
	public function get_discord_current_user( $access_token ) {
		// if ( ! is_user_logged_in() ) {
		// wp_send_json_error( 'Unauthorized user', 401 );
		// exit();
		// }
		$user_id = get_current_user_id();

		$discord_cuser_api_url = ETS_MEMBERPRESS_DISCORD_API_URL . 'users/@me';
		$param                 = array(
			'headers' => array(
				'Content-Type'  => 'application/x-www-form-urlencoded',
				'Authorization' => 'Bearer ' . $access_token,
			),
		);
		$user_response         = wp_remote_get( $discord_cuser_api_url, $param );
		ets_memberpress_discord_log_api_response( $user_id, $discord_cuser_api_url, $param, $user_response );

		$response_arr = json_decode( wp_remote_retrieve_body( $user_response ), true );
		write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
		$user_body = json_decode( wp_remote_retrieve_body( $user_response ), true );
		return $user_body;

	}

	/**
	 * Create authentication token for discord API
	 *
	 * @param STRING $code
	 * @param INT    $user_id
	 * @param ARRAY  $active_memberships
	 * @return OBJECT API response
	 */
	public function ets_memberpress_create_discord_auth_token( $code, $user_id, $active_memberships ) {
		$discord_token_api_url = ETS_MEMBERPRESS_DISCORD_API_URL . 'oauth2/token';
		if ( ! is_user_logged_in() ) {
			if ( ! empty( $code ) && $user_id == 'none_wp_user' && empty( $active_memberships ) ) {
				$args     = array(
					'method'  => 'POST',
					'headers' => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
					'body'    => array(
						'client_id'     => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
						'client_secret' => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_secret' ) ) ),
						'grant_type'    => 'authorization_code',
						'code'          => $code,
						'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_redirect_url' ) ) ),
					),
				);
				$response = wp_remote_post( $discord_token_api_url, $args );
				ets_memberpress_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
				if ( ets_memberpress_discord_check_api_errors( $response ) ) {
					$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
					write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				}
				return $response;
			} else {
				wp_send_json_error( 'Unauthorized user', 401 );
				exit();
			}
		}

		// stop users who having the direct URL of discord Oauth.
		// We must check IF NONE members is set to NO and user having no active membership.
		$allow_none_member = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_allow_none_member' ) ) );
		if ( empty( $active_memberships ) && 'no' === $allow_none_member ) {
			return;
		}
		$response          = '';
		$refresh_token     = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_refresh_token', true ) ) );
		$token_expiry_time = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_expires_in', true ) ) );
		if ( $refresh_token ) {
			$date              = new DateTime();
			$current_timestamp = $date->getTimestamp();

			if ( $current_timestamp > $token_expiry_time ) {
				$args     = array(
					'method'  => 'POST',
					'headers' => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
					'body'    => array(
						'client_id'     => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
						'client_secret' => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_secret' ) ) ),
						'grant_type'    => 'refresh_token',
						'refresh_token' => $refresh_token,
						'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_redirect_url' ) ) ),
						'scope'         => ETS_MEMBERPRESS_DISCORD_OAUTH_SCOPES,
					),
				);
				$response = wp_remote_post( $discord_token_api_url, $args );
				ets_memberpress_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
				if ( ets_memberpress_discord_check_api_errors( $response ) ) {
					$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
					write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				}
			}
		} else {
			$args     = array(
				'method'  => 'POST',
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'client_id'     => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
					'client_secret' => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_secret' ) ) ),
					'grant_type'    => 'authorization_code',
					'code'          => $code,
					'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_redirect_url' ) ) ),
					'scope'         => ETS_MEMBERPRESS_DISCORD_OAUTH_SCOPES,
				),
			);
			$response = wp_remote_post( $discord_token_api_url, $args );

			ets_memberpress_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
			if ( ets_memberpress_discord_check_api_errors( $response ) ) {
				$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			}
		}

		return $response;
	}

	/**
	 * Disconnect user from discord
	 *
	 * @param NONE
	 * @return OBJECT JSON response
	 */
	public function ets_memberpress_disconnect_from_discord() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		// Check for nonce security.
		if ( isset( $_POST['ets_memberpress_discord_public_nonce'] ) && ! wp_verify_nonce( $_POST['ets_memberpress_discord_public_nonce'], 'ets-memberpress-discord-public-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$user_id                              = sanitize_text_field( trim( $_POST['user_id'] ) );
		$memberpress_member_kick_out          = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_member_kick_out' ) ) );
		$ets_memberpress_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$previous_default_role                = get_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
		if ( $user_id ) {
			if ( $memberpress_member_kick_out == true ) {
				$this->memberpress_delete_member_from_guild( $user_id, false );
			} else {
				// check for roles assigned, and delete them
				  $active_memberships = ets_memberpress_discord_get_active_memberships( $user_id );
				if ( is_array( $active_memberships ) && count( $active_memberships ) != 0 ) {
					foreach ( $active_memberships as $active_membership ) {
						if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $active_membership->product_id, $ets_memberpress_discord_role_mapping ) ) {
							  $mapped_role_id = sanitize_text_field( trim( $ets_memberpress_discord_role_mapping[ 'level_id_' . $active_membership->product_id ] ) );
							if ( $mapped_role_id ) {
								$this->admin_cls_instance->memberpress_delete_discord_role( $user_id, $mapped_role_id, false );
							}
						}
					}
				}

				// check for default role and delete it.
				if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
					$this->admin_cls_instance->memberpress_delete_discord_role( $user_id, $previous_default_role, false );
				}
			}
			// delete all user_meta keys
			ets_memberpress_discord_remove_usermeta( $user_id );

		}
		$event_res = array(
			'status'  => 1,
			'message' => 'Successfully disconnected',
		);
		echo wp_json_encode( $event_res );
		die();
	}

	/**
	 * Schedule delete existing user from guild
	 *
	 * @param INT  $user_id
	 * @param BOOL $is_schedule
	 */
	public function memberpress_delete_member_from_guild( $user_id, $is_schedule = true ) {
		if ( $is_schedule && isset( $user_id ) ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_schedule_delete_member', array( $user_id, $is_schedule ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		} else {
			if ( isset( $user_id ) ) {
				$this->ets_memberpress_discord_as_handler_delete_member_from_guild( $user_id, $is_schedule );
			}
		}
	}

	/**
	 * AS Handling member delete from huild
	 *
	 * @param INT  $user_id
	 * @param BOOL $is_schedule
	 * @return OBJECT API response
	 */
	public function ets_memberpress_discord_as_handler_delete_member_from_guild( $user_id, $is_schedule ) {
		$guild_id                         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
		$discord_bot_token                = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$_ets_memberpress_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$active_memberships               = ets_memberpress_discord_get_active_memberships( $user_id );
		$guilds_delete_memeber_api_url    = ETS_MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id;
		$guild_args                       = array(
			'method'  => 'DELETE',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
		);
		$guild_response                   = wp_remote_post( $guilds_delete_memeber_api_url, $guild_args );
		ets_memberpress_discord_log_api_response( $user_id, $guilds_delete_memeber_api_url, $guild_args, $guild_response );
		if ( ets_memberpress_discord_check_api_errors( $guild_response ) ) {
			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );
			write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			if ( $is_schedule ) {
				// this exception should be catch by action scheduler.
				throw new Exception( 'Failed in function ets_memberpress_discord_as_handler_delete_member_from_guild' );
			}
		}

		/*Delete all usermeta related to discord connection*/
		delete_user_meta( $user_id, '_ets_memberpress_discord_user_id' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_access_token' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_refresh_token' );
		if ( is_array( $active_memberships ) ) {
			foreach ( $active_memberships as $active_membership ) {
				delete_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_membership->trans_num );
			}
		}
		delete_user_meta( $user_id, '_ets_memberpress_discord_default_role_id' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_username' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_expires_in' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_avatar' );
	}

	/**
	 * Method for allow user to login with discord account.
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_memberpress_discord_login_with_discord_button( $membership_id ) {
		wp_enqueue_style( $this->plugin_name . 'public_css' );
		if ( ! is_user_logged_in() ) {
			$default_role                         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
			$ets_memberpress_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
			$all_roles                            = json_decode( get_option( 'ets_memberpress_discord_all_roles' ), true );
			$member_discord_login                 = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_login_with_discord' ) ) );
			$btn_color                            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_btn_color' ) ) );
			$btn_text                             = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_loggedout_btn_text' ) ) );

			if ( $member_discord_login ) {
				echo wp_kses( '<style>.memberpress-btn-connect{background-color: ' . esc_attr( $btn_color ) . ';}</style>', array( 'style' => array() ) );
				$curr_level_id     = $membership_id;
				$mapped_role_name  = '';
				$default_role_name = '';
				if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
					$default_role_name = $all_roles[ $default_role ];
				}
				if ( $curr_level_id && is_array( $all_roles ) ) {
					if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $curr_level_id, $ets_memberpress_discord_role_mapping ) ) {
						$mapped_role_id = $ets_memberpress_discord_role_mapping[ 'level_id_' . $curr_level_id ];
						if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
							$mapped_role_name = $all_roles[ $mapped_role_id ];
						}
					}
				}
				$current_url = get_site_url() . '?action=memberpress-discord-login&fromcheckout=1&url=' . ets_memberpress_discord_get_current_screen_url();
				echo wp_kses(
					'<a href="' . sanitize_url( $current_url ) . '" class="memberpress-btn-connect ets-btn" >' . esc_html( $btn_text ) . ETS_Memberpress_Discord::get_discord_logo_white() . '</a>',
					array(
						'a'   => array(
							'href'  => array( $current_url ),
							'class' => array( 'memberpress-btn-connect', 'ets-btn' ),
						),
						'img' => array(
							'src' => array(),
						),
					)
				);
				$memberpress_connecttodiscord_btn = '';
				if ( $mapped_role_name ) {
					$memberpress_connecttodiscord_btn .= '<p class="ets_assigned_role">' . esc_html__( 'Following Roles will be assigned to you in Discord: ', 'connect-memberpress-discord-add-on' );
					$memberpress_connecttodiscord_btn .= esc_html( $mapped_role_name );
					if ( $default_role_name ) {
						$memberpress_connecttodiscord_btn .= ', ' . esc_html( $default_role_name );
					}
					$memberpress_connecttodiscord_btn .= '</p>';

					echo wp_kses( $memberpress_connecttodiscord_btn, array( 'p' => array( 'class' => array( 'ets_assigned_role' ) ) ) );
				}
			}
		}
	}

	/*
	* Get action from $_GET['action']
	*/
	public function ets_memberpress_discord_act_on_url_action() {
		// when discord-login initiated
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'memberpress-discord-login' ) {
			$params                    = array(
				'client_id'     => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
				'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_redirect_url' ) ) ),
				'response_type' => 'code',
				'scope'         => 'identify email connections guilds guilds.join',
			);
			$discord_authorise_api_url = ETS_MEMBERPRESS_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );
			// cache the url param for 1 minute
			if ( isset( $_GET['url'] ) ) {
				setcookie( 'ets_memberpress_discord_page', sanitize_url( $_GET['url'] ), time() + 60, '/' );
			}
			wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
			exit;
		}
	}

	/*
	* Method to catch the discord auth response and process it.
	*
	* @param ARRAY $res_body
	*/
	private function memberpress_catch_discord_auth_callback( $res_body, $user_body, $user_id ) {

		$discord_exist_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$access_token          = sanitize_text_field( trim( $res_body['access_token'] ) );
		update_user_meta( $user_id, '_ets_memberpress_discord_access_token', $access_token );
		if ( array_key_exists( 'refresh_token', $res_body ) ) {
			$refresh_token = sanitize_text_field( trim( $res_body['refresh_token'] ) );
			update_user_meta( $user_id, '_ets_memberpress_discord_refresh_token', $refresh_token );
		}
		if ( array_key_exists( 'expires_in', $res_body ) ) {
			$expires_in = $res_body['expires_in'];
			$date       = new DateTime();
			$date->add( DateInterval::createFromDateString( $expires_in . ' seconds' ) );
			$token_expiry_time = $date->getTimestamp();
			update_user_meta( $user_id, '_ets_memberpress_discord_expires_in', $token_expiry_time );
		}

		if ( is_array( $user_body ) && array_key_exists( 'discriminator', $user_body ) ) {
			$discord_user_number           = $user_body['discriminator'];
			$discord_user_name             = $user_body['username'];
			$discord_user_name_with_number = $discord_user_name . '#' . $discord_user_number;
			$discord_user_avatar           = $user_body['avatar'];
			update_user_meta( $user_id, '_ets_memberpress_discord_username', $discord_user_name_with_number );
			update_user_meta( $user_id, '_ets_memberpress_discord_avatar', $discord_user_avatar );
		}
		if ( is_array( $user_body ) && array_key_exists( 'id', $user_body ) ) {
			$_ets_memberpress_discord_user_id = sanitize_text_field( trim( $user_body['id'] ) );
			if ( $discord_exist_user_id == $_ets_memberpress_discord_user_id ) {
				$_ets_memberpress_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_role_id', true ) ) );
				if ( ! empty( $_ets_memberpress_discord_role_id ) && $_ets_memberpress_discord_role_id != 'none' ) {
					$this->admin_cls_instance->memberpress_delete_discord_role( $user_id, $_ets_memberpress_discord_role_id );
				}
			}
			update_user_meta( $user_id, '_ets_memberpress_discord_user_id', $_ets_memberpress_discord_user_id );
		}

	}
	public function ets_memberpress_discord_listen_to_mepr_events( $event ) {
		$obj = $event->get_data();

		if ( ! ( $obj instanceof MeprTransaction ) && ! ( $obj instanceof MeprSubscription ) ) {
				return; // nothing here to do if we're not dealing with a txn or sub
		}

		$member       = $obj->user();
		$access_token = sanitize_text_field( trim( get_user_meta( $member->ID, '_ets_memberpress_discord_access_token', true ) ) );

		if ( $access_token ) {

			$ets_memberpress_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );

			if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $obj->product_id, $ets_memberpress_discord_role_mapping ) ) {
				$mapped_role_id = $ets_memberpress_discord_role_mapping[ 'level_id_' . $obj->product_id ];

				if ( $member->is_active_on_membership( $obj ) ) {

					$this->put_discord_role_api( $member->ID, $mapped_role_id );
				} else {
					$this->admin_cls_instance->memberpress_delete_discord_role( $member->ID, $mapped_role_id );

				}
			}
		}
	}

	/**
	 * Add 'data' protocol to display discord icon .
	 *
	 * @param array $protocols Array of allowed protocols.
	 */
	public function ets_memberpress_discord_allow_data_protocol( $protocols ) {
		$protocols[] = 'data';

		return $protocols;
	}

	/**
	 *  Filter call back to show or hide the Connect Discord button on profile page.
	 *
	 * @param bool $show By default True.
	 */
	public function ets_memberpress_show_connect_button_on_profile( $show = true ) {

		return $show;
	}
}
