<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/public
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Memberpress_Discord_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of the plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Memberpress_Discord_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Memberpress_Discord_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . 'public_css', plugin_dir_url( __FILE__ ) . 'css/memberpress-discord-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Memberpress_Discord_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Memberpress_Discord_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . 'public_js', plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-public.js', array( 'jquery' ), $this->version, false );
		$script_params = array(
			'admin_ajax'                           => admin_url( 'admin-ajax.php' ),
			'permissions_const'                    => MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
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
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$user_id                              = sanitize_text_field( trim( get_current_user_id() ) );
		$access_token                         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$allow_none_member                    = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
		$default_role                         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
		$ets_memberpress_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$all_roles                            = json_decode( get_option( 'ets_memberpress_discord_all_roles' ), true );
		$active_memberships                   = $this->ets_memberpress_discord_get_active_memberships( $user_id );
		$mapped_role_names                    = array();
		if ( $active_memberships && is_array( $all_roles ) ) {
			foreach ( $active_memberships as $active_membership ) {
				if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $active_membership->product_id, $ets_memberpress_discord_role_mapping ) ) {
					$mapped_role_id = $ets_memberpress_discord_role_mapping[ 'level_id_' . $active_membership->product_id ];
					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						array_push( $mapped_role_names, $all_roles[ $mapped_role_id ] );
					}
				}
			}
		}
		$default_role_name = '';
		if ( 'none' !== $default_role && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = $all_roles[ $default_role ];
		}
		if ( ets_memberpress_discord_check_saved_settings_status() ) {
			if ( $access_token ) {
				?>
				<label class="ets-connection-lbl"><?php echo __( 'Discord connection', 'ets_memberpress_discord' ); ?></label>
				<a href="#" class="ets-btn btn-disconnect" id="disconnect-discord" data-user-id="<?php echo esc_attr( $user_id ); ?>"><?php echo __( 'Disconnect From Discord ', 'ets_memberpress_discord' ); ?><i class='fab fa-discord'></i></a>
				<span class="ets-spinner"></span>
				<?php
			} elseif ( current_user_can( 'memberpress_authorized' ) && $mapped_role_names || $allow_none_member == 'yes' ) {
				?>
				<label class="ets-connection-lbl"><?php echo __( 'Discord connection', 'ets_memberpress_discord' ); ?></label>
				<a href="?action=memberpress-discord-login" class="btn-connect ets-btn" ><?php echo __( 'Connect To Discord', 'ets_memberpress_discord' ); ?> <i class='fab fa-discord'></i></a>
				<?php if ( $mapped_role_names ) { ?>
					<p class="ets_assigned_role">
					<?php
					echo __( 'Following Roles will be assigned to you in Discord: ', 'ets_memberpress_discord' );
					foreach ( $mapped_role_names as $mapped_role_name ) {
						echo esc_html( $mapped_role_name ) . ', ';
					}
					if ( $default_role_name ) {
						echo esc_html( $default_role_name );
					}
					?>
					</p>
				<?php } ?>
				<?php
			}
		}
	}

	/**
	 * Get memberpress current level id
	 *
	 * @param INT $user_id
	 * @return INT|NULL $active_memberships
	 */
	public function ets_memberpress_discord_get_active_memberships( $user_id ) {
		$memberpress_user   = new MeprUser( $user_id );
		$active_memberships = $memberpress_user->active_product_subscriptions( 'transactions' );
		if ( $active_memberships ) {
			return $active_memberships;
		} else {
			return null;
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
			if ( isset( $_GET['action'] ) && 'memberpress-discord-login' === $_GET['action'] ) {
				$params                    = array(
					'client_id'     => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
					'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_redirect_url' ) ) ),
					'response_type' => 'code',
					'scope'         => 'identify email connections guilds guilds.join messages.read',
				);
				$discord_authorise_api_url = MEMBERPRESS_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );

				wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
				exit;
			}

			if ( isset( $_GET['action'] ) && 'discord-connectToBot' === $_GET['action'] ) {
				$params                    = array(
					'client_id'   => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
					'permissions' => MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
					'scope'       => 'bot',
					'guild_id'    => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) ),
				);
				$discord_authorise_api_url = MEMBERPRESS_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );

				wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
				exit;
			}
			if ( isset( $_GET['code'] ) && isset( $_GET['via'] ) ) {
				$membership_private_obj = $this->ets_memberpress_discord_get_active_memberships( $user_id );
				$active_memberships     = array();
				if ( ! empty( $membership_private_obj ) ) {
					foreach ( $membership_private_obj as $memberships ) {
						$membership_arr = array(
							'product_id' => $memberships->product_id,
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
							update_user_meta( $user_id, '_ets_memberpress_discord_access_token', $access_token );
							if ( array_key_exists( 'refresh_token', $res_body ) ) {
								$refresh_token = sanitize_text_field( trim( $res_body['refresh_token'] ) );
								update_user_meta( $user_id, '_ets_memberpress_discord_refresh_token', $refresh_token );
							}
							if ( array_key_exists( 'expires_in', $res_body ) ) {
								$expires_in = $res_body['expires_in'];
								$date       = new DateTime();
								$date->add( DateInterval::createFromDateString( '' . $expires_in . ' seconds' ) );
								$token_expiry_time = $date->getTimestamp();
								update_user_meta( $user_id, '_ets_memberpress_discord_expires_in', $token_expiry_time );
							}
							$user_body = $this->get_discord_current_user( $access_token );

							if ( is_array( $user_body ) && array_key_exists( 'discriminator', $user_body ) ) {
								$discord_user_number           = $user_body['discriminator'];
								$discord_user_name             = $user_body['username'];
								$discord_user_name_with_number = $discord_user_name . '#' . $discord_user_number;
								update_user_meta( $user_id, '_ets_memberpress_discord_username', $discord_user_name_with_number );
							}
							if ( is_array( $user_body ) && array_key_exists( 'id', $user_body ) ) {
								$_ets_memberpress_discord_user_id = sanitize_text_field( trim( $user_body['id'] ) );
								if ( $discord_exist_user_id === $_ets_memberpress_discord_user_id ) {
									foreach ( $active_memberships as $active_membership ) {
										$_ets_memberpress_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_membership->product_id, true ) ) );
										if ( ! empty( $_ets_memberpress_discord_role_id ) && $_ets_memberpress_discord_role_id != 'none' ) {
											$this->memberpress_delete_discord_role( $user_id, $_ets_memberpress_discord_role_id );
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
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$allow_none_member = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
		if ( ! empty( $active_memberships ) || 'yes' === $allow_none_member ) {
			// It is possible that we may exhaust API rate limit while adding members to guild, so handling off the job to queue.
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_add_member_to_guild', array( $_ets_memberpress_discord_user_id, $user_id, $access_token, $active_memberships ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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

		$guilds_memeber_api_url = MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id;
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
		foreach ( $discord_roles as $key => $discord_role ) {
			update_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_memberships[ $key ]['product_id'], $discord_role );
			if ( $discord_role && $discord_role != 'none' && isset( $user_id ) ) {
				$this->put_discord_role_api( $user_id, $discord_role );
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
		if ( true == $ets_memberpress_discord_send_welcome_dm ) {
			foreach ( $active_memberships as $active_membership ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $active_membership, 'welcome' ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_schedule_member_put_role', array( $user_id, $role_id, $is_schedule ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
		$discord_change_role_api_url      = MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id . '/roles/' . $role_id;

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
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$user_id = get_current_user_id();

		$discord_cuser_api_url = MEMBERPRESS_DISCORD_API_URL . 'users/@me';
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
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}

		// stop users who having the direct URL of discord Oauth.
		// We must check IF NONE members is set to NO and user having no active membership.
		$allow_none_member = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
		if ( empty( $active_memberships ) && 'no' === $allow_none_member ) {
			return;
		}
		$response              = '';
		$refresh_token         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_refresh_token', true ) ) );
		$token_expiry_time     = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_expires_in', true ) ) );
		$discord_token_api_url = MEMBERPRESS_DISCORD_API_URL . 'oauth2/token';
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
						'scope'         => MEMBERPRESS_DISCORD_OAUTH_SCOPES,
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
					'scope'         => MEMBERPRESS_DISCORD_OAUTH_SCOPES,
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
		// Check for nonce security
		if ( isset( $_POST['ets_memberpress_discord_public_nonce'] ) && ! wp_verify_nonce( $_POST['ets_memberpress_discord_public_nonce'], 'ets-memberpress-discord-public-ajax-nonce' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
		}
		$user_id = sanitize_text_field( trim( $_POST['user_id'] ) );
		if ( $user_id ) {
			$this->memberpress_delete_member_from_guild( $user_id, false );
			delete_user_meta( $user_id, '_ets_memberpress_discord_access_token' );
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
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_schedule_delete_member', array( $user_id, $is_schedule ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
		$active_memberships               = $this->ets_memberpress_discord_get_active_memberships( $user_id );
		$guilds_delete_memeber_api_url    = MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id;
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
				delete_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_membership->product_id );
			}
		}
		delete_user_meta( $user_id, '_ets_memberpress_discord_default_role_id' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_username' );
		delete_user_meta( $user_id, '_ets_memberpress_discord_expires_in' );
	}

	/**
	 * Discord DM a member using bot.
	 *
	 * @param INT    $user_id
	 * @param ARRAY  $active_memberships
	 * @param STRING $type (warning|expired)
	 */
	public function ets_memberpress_discord_handler_send_dm( $user_id, $active_membership, $type = 'warning' ) {
		$discord_user_id                                    = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$discord_bot_token                                  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$ets_memberpress_discord_expiration_warning_message = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_expiration_warning_message' ) ) );
		$ets_memberpress_discord_expiration_expired_message = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_expiration_expired_message' ) ) );
		$ets_memberpress_discord_welcome_message            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_welcome_message' ) ) );
		$ets_memberpress_discord_cancel_message             = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_cancel_message' ) ) );
		// Check if DM channel is already created for the user.
		$user_dm = get_user_meta( $user_id, '_ets_memberpress_discord_dm_channel', true );

		if ( ! isset( $user_dm['id'] ) || false === $user_dm || empty( $user_dm ) ) {
			$this->ets_memberpress_discord_create_member_dm_channel( $user_id );
			$user_dm       = get_user_meta( $user_id, '_ets_memberpress_discord_dm_channel', true );
			$dm_channel_id = $user_dm['id'];
		} else {
			$dm_channel_id = $user_dm['id'];
		}

		if ( 'warning' === $type ) {
			update_user_meta( $user_id, '_ets_memberpress_discord_expitration_warning_dm_for_' . $active_membership['product_id'], true );
			$message = ets_memberpress_discord_get_formatted_dm( $user_id, $active_membership, $ets_memberpress_discord_expiration_warning_message );
		}
		if ( 'expired' === $type ) {
			update_user_meta( $user_id, '_ets_memberpress_discord_expired_dm_for_' . $active_membership['product_id'], true );
			$message = ets_memberpress_discord_get_formatted_dm( $user_id, $active_membership, $ets_memberpress_discord_expiration_expired_message );
		}
		if ( 'welcome' === $type ) {
			update_user_meta( $user_id, '_ets_memberpress_discord_welcome_dm_for_' . $active_membership['product_id'], true );
			$message = ets_memberpress_discord_get_formatted_dm( $user_id, $active_membership, $ets_memberpress_discord_welcome_message );
		}
		if ( 'cancel' === $type ) {
			update_user_meta( $user_id, '_ets_memberpress_discord_cancel_dm_for_' . $active_membership['product_id'], true );
			$message = ets_memberpress_discord_get_formatted_dm( $user_id, $active_membership, $ets_memberpress_discord_cancel_message );
		}

		$creat_dm_url = MEMBERPRESS_DISCORD_API_URL . '/channels/' . $dm_channel_id . '/messages';
		$dm_args      = array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
			'body'    => wp_json_encode(
				array(
					'content' => sanitize_text_field( trim( wp_unslash( $message ) ) ),
				)
			),
		);
		$dm_response  = wp_remote_post( $creat_dm_url, $dm_args );
		ets_memberpress_discord_log_api_response( $user_id, $creat_dm_url, $dm_args, $dm_response );
		$dm_response_body = json_decode( wp_remote_retrieve_body( $dm_response ), true );
		if ( ets_memberpress_discord_check_api_errors( $dm_response ) ) {
			write_api_response_logs( $dm_response_body, $user_id, debug_backtrace()[0] );
			// this should be catch by Action schedule failed action.
			throw new Exception( 'Failed in function ets_memberpress_discord_send_dm' );
		}
	}

	/**
	 * Create DM channel for a give user_id
	 *
	 * @param INT $user_id
	 * @return MIXED
	 */
	public function ets_memberpress_discord_create_member_dm_channel( $user_id ) {
		$discord_user_id       = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$discord_bot_token     = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$create_channel_dm_url = MEMBERPRESS_DISCORD_API_URL . '/users/@me/channels';
		$dm_channel_args       = array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
			'body'    => wp_json_encode(
				array(
					'recipient_id' => $discord_user_id,
				)
			),
		);

		$created_dm_response = wp_remote_post( $create_channel_dm_url, $dm_channel_args );
		ets_memberpress_discord_log_api_response( $user_id, $create_channel_dm_url, $dm_channel_args, $created_dm_response );
		$response_arr = json_decode( wp_remote_retrieve_body( $created_dm_response ), true );

		if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
			// check if there is error in create dm response.
			if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
				write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				if ( ets_memberpress_discord_check_api_errors( $created_dm_response ) ) {
					// this should be catch by Action schedule failed action.
					throw new Exception( 'Failed in function ets_memberpress_discord_create_member_dm_channel' );
				}
			} else {
				update_user_meta( $user_id, '_ets_memberpress_discord_dm_channel', $response_arr );
			}
		}
		return $response_arr;
	}

	/**
	 * Action schedule to schedule a function to run upon memberpress Expiry
	 *
	 * @param ARRAY   $txn
	 * @param BOOLEAN $status
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_expiry( $txn, $status = false ) {
		$access_token           = sanitize_text_field( trim( get_user_meta( $txn->user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$expired_membership    = array();
		if ( ! empty( $txn ) ) {
				$expired_membership = array(
					'product_id' => $txn->product_id,
					'created_at' => $txn->created_at,
					'expires_at' => $txn->expires_at,
				);
		}

		if ( $status == 'none' && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_expiry', array( $txn->user_id, $expired_membership ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}
	
	/**
	 * Action schedule to schedule a function to run upon memberpress cancel
	 *
	 * @param ARRAY   $event
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_complete_transactions( $event ) {
		$subscription       = $event->get_data();
		$user               = $subscription->user();
		$access_token       = sanitize_text_field( trim( get_user_meta( $user->ID, '_ets_memberpress_discord_access_token', true ) ) );
		$complete_txn = array();
		if ( ! empty( $subscription ) ) {
				$complete_txn = array(
					'product_id' => $subscription->product_id,
					'created_at' => $subscription->created_at,
					'expires_at' => $subscription->expires_at,
				);
		}
		if ( $complete_txn && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_complete_transaction', array( $user->ID, $complete_txn ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}

	/**
	 * Action scheduler method to process complete transaction event.
	 *
	 * @param INT $user_id
	 * @param INT $complete_txn
	 */
	public function ets_memberpress_discord_as_handler_memberpress_complete_transaction( $user_id, $complete_txn ) {
		$ets_memberpress_discord_role_mapping               = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $complete_txn['product_id'], $ets_memberpress_discord_role_mapping ) ) {
			$mapped_role_id = sanitize_text_field( trim( $ets_memberpress_discord_role_mapping[ 'level_id_' . $complete_txn['product_id'] ] ) );
			if ( $mapped_role_id && $expired_level_id == false && $cancel_level_id == false ) {
				$this->put_discord_role_api( $user_id, $mapped_role_id, $is_schedule );
				update_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $complete_txn['product_id'], $mapped_role_id );
			}
		}
	}

	/**
	 * Action schedule to schedule a function to run upon memberpress cancel
	 *
	 * @param ARRAY   $event
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_cancelled( $event ) {
		$subscription       = $event->get_data();
		$user               = $subscription->user();
		$access_token       = sanitize_text_field( trim( get_user_meta( $user->ID, '_ets_memberpress_discord_access_token', true ) ) );
		$cancelled_membership = array();
		if ( ! empty( $subscription ) ) {
				$cancelled_membership = array(
					'product_id' => $subscription->product_id,
					'created_at' => $subscription->created_at,
					'expires_at' => $subscription->expires_at,
				);
		}
		if ( $cancelled_membership && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_cancelled', array( $user->ID, $cancelled_membership ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}
	

	/*
	* Action scheduler method to process expired memberpress members.
	* @param INT $user_id
	* @param INT $expired_level_id
	*/
	public function ets_memberpress_discord_as_handler_memberpress_expiry( $user_id, $expired_membership ) {
		$this->ets_memberpress_discord_set_member_roles( $user_id, $expired_membership, false, true );
	}

	/*
	* Action scheduler method to process cancelled_membership memberpress members.
	* @param INT $user_id
	* @param INT $cancelled_membership
	*/
	public function ets_memberpress_discord_as_handler_memberpress_cancelled( $user_id, $cancelled_membership ) {
		$this->ets_memberpress_discord_set_member_roles( $user_id, false, $cancelled_membership, true );
	}

	/**
	 * Method to adjust level mapped and default role of a member.
	 *
	 * @param INT  $user_id
	 * @param INT  $expired_level_id
	 * @param INT  $cancel_level_id
	 * @param BOOL $is_schedule
	 */
	private function ets_memberpress_discord_set_member_roles( $user_id, $expired_membership = false, $cancelled_membership = false, $is_schedule = true ) {
		$expired_level_id                                   = $expired_membership['product_id'];
		$allow_none_member                                  = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
		$default_role                                       = sanitize_text_field( trim( get_option( '_ets_memberpress_discord_default_role_id' ) ) );
		$ets_memberpress_discord_role_mapping               = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$active_memberships                                 = $this->ets_memberpress_discord_get_active_memberships( $user_id );
		$previous_default_role                              = get_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
		$ets_memberpress_discord_send_membership_expired_dm = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_membership_expired_dm' ) ) );
		$ets_memberpress_discord_send_membership_cancel_dm  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_membership_cancel_dm' ) ) );
		$access_token                                       = get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true );
		if ( ! empty( $access_token ) ) {
			if ( $expired_membership ) {
				$curr_level_id = $expired_membership['product_id'];
			}
			if ( $cancelled_membership ) {
				$curr_level_id = $cancelled_membership['product_id'];
			}
			$_ets_memberpress_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $curr_level_id, true ) ) );
			// delete already assigned role.
			if ( isset( $_ets_memberpress_discord_role_id ) && $_ets_memberpress_discord_role_id != '' && $_ets_memberpress_discord_role_id != 'none' ) {
					$this->memberpress_delete_discord_role( $user_id, $_ets_memberpress_discord_role_id, $is_schedule );
					delete_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $curr_level_id, true );
			}
			if ( is_array( $active_memberships ) && count( $active_memberships ) != 0 ) {
				// Assign role which is mapped to the mmebership level.
				foreach ( $active_memberships as $active_membership ) {
					if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $active_membership->product_id, $ets_memberpress_discord_role_mapping ) ) {
						$mapped_role_id = sanitize_text_field( trim( $ets_memberpress_discord_role_mapping[ 'level_id_' . $active_membership->product_id ] ) );
						if ( $mapped_role_id && $expired_level_id == false && $cancel_level_id == false ) {
							$this->put_discord_role_api( $user_id, $mapped_role_id, $is_schedule );
							update_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_membership->product_id, $mapped_role_id );
						}
					}
				}
			}
			// Assign role which is saved as default.
			if ( $default_role != 'none' ) {
				if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
						$this->memberpress_delete_discord_role( $user_id, $previous_default_role, $is_schedule );
				}
				delete_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
				$this->put_discord_role_api( $user_id, $default_role, $is_schedule );
				update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
			} elseif ( $default_role == 'none' ) {
				if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
					$this->memberpress_delete_discord_role( $user_id, $previous_default_role, $is_schedule );
				}
				update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
			}

			if ( isset( $user_id ) && $allow_none_member == 'no' && empty( $active_memberships ) ) {
				$this->memberpress_delete_member_from_guild( $user_id, false );
			}
			delete_user_meta( $user_id, '_ets_memberpress_discord_expitration_warning_dm_for_' . $curr_level_id );

			// Send DM about expiry, but only when allow_none_member setting is yes
			if ( $ets_memberpress_discord_send_membership_expired_dm == true && $expired_level_id !== false && $allow_none_member = 'yes' ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $expired_membership, 'expired' ), 'ets-memberpress-discord' );
			}

			// Send DM about cancel, but only when allow_none_member setting is yes
			if ( $ets_memberpress_discord_send_membership_cancel_dm == true && $cancel_level_id !== false && $allow_none_member = 'yes' ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $cancelled_membership, 'cancel' ), 'ets-memberpress-discord' );
			}
		}
	}

	/**
	 * Schedule delete discord role for a member
	 *
	 * @param INT  $user_id
	 * @param INT  $ets_role_id
	 * @param BOOL $is_schedule
	 * @return OBJECT API response
	 */
	public function memberpress_delete_discord_role( $user_id, $ets_role_id, $is_schedule = true ) {
		if ( $is_schedule ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_schedule_delete_role', array( $user_id, $ets_role_id, $is_schedule ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		} else {
			$this->ets_memberpress_discord_as_handler_delete_memberrole( $user_id, $ets_role_id, $is_schedule );
		}
	}

	/**
	 * Action Schedule handler to process delete role of a member.
	 *
	 * @param INT  $user_id
	 * @param INT  $ets_role_id
	 * @param BOOL $is_schedule
	 * @return OBJECT API response
	 */
	public function ets_memberpress_discord_as_handler_delete_memberrole( $user_id, $ets_role_id, $is_schedule = true ) {

		$guild_id                    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_guild_id' ) ) );
		$_ets_memberpress_discord_user_id  = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$discord_bot_token           = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$discord_delete_role_api_url = MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id . '/roles/' . $ets_role_id;
		if ( $_ets_memberpress_discord_user_id ) {
			$param = array(
				'method'  => 'DELETE',
				'headers' => array(
					'Content-Type'   => 'application/json',
					'Authorization'  => 'Bot ' . $discord_bot_token,
					'Content-Length' => 0,
				),
			);

			$response = wp_remote_request( $discord_delete_role_api_url, $param );
			ets_memberpress_discord_log_api_response( $user_id, $discord_delete_role_api_url, $param, $response );
			if ( ets_memberpress_discord_check_api_errors( $response ) ) {
				$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				if ( $is_schedule ) {
					// this exception should be catch by action scheduler.
					throw new Exception( 'Failed in function ets_memberpress_discord_as_handler_delete_memberrole' );
				}
			}
			return $response;
		}
	}
}
