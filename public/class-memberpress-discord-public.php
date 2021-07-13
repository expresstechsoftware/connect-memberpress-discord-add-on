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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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

		wp_enqueue_style( $this->plugin_name.'public_css', plugin_dir_url( __FILE__ ) . 'css/memberpress-discord-public.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name.'public_js', plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add discord connection buttons.
	 *
	 * @since    1.0.0
	 */
	public function ets_memberpress_discord_add_field() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$mepr_current_user = MeprUtils::get_currentuserinfo();
		$user_id = sanitize_text_field( trim( get_current_user_id() ) );

		$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true ) ) );

		$allow_none_member        = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
		$default_role             = sanitize_text_field( trim( get_option( '_ets_memberpress_discord_default_role_id' ) ) );
		$ets_memberpress_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$all_roles                = unserialize( get_option( 'ets_memberpress_discord_all_roles' ) );
		$curr_level_id            = $this->ets_memberpress_discord_get_current_level_id( $mepr_current_user );
		
		$mapped_role_name         = '';
		if ( $curr_level_id && is_array( $all_roles ) ) {
			if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $curr_level_id, $ets_memberpress_discord_role_mapping ) ) {
				$mapped_role_id = $ets_memberpress_discord_role_mapping[ 'level_id_' . $curr_level_id ];
				if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
					$mapped_role_name = $all_roles[ $mapped_role_id ];
				}
			}
		}
		$default_role_name = '';
		if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = $all_roles[ $default_role ];
		}
		if ( Check_saved_settings_status() ) {
			if ( $access_token ) {
				?>
				<label class="ets-connection-lbl"><?php echo __( 'Discord connection', 'ets_memberpress_discord' ); ?></label>
				<a href="#" class="ets-btn btn-disconnect" id="disconnect-discord" data-user-id="<?php echo $user_id; ?>"><?php echo __( 'Disconnect From Discord ', 'ets_memberpress_discord' ); ?><i class='fab fa-discord'></i></a>
				<span class="ets-spinner"></span>
				<?php
			} elseif ( current_user_can('memberpress_authorized') || $allow_none_member == 'yes' ) {
				?>
				<label class="ets-connection-lbl"><?php echo __( 'Discord connection', 'ets_memberpress_discord' ); ?></label>
				<a href="?action=memberpress-discord-login" class="btn-connect ets-btn" ><?php echo __( 'Connect To Discord', 'ets_memberpress_discord' ); ?> <i class='fab fa-discord'></i></a>
				<?php if ( $mapped_role_name ) { ?>
					<p class="ets_assigned_role">
					<?php
					echo __( 'Following Roles will be assigned to you in Discord: ', 'ets_memberpress_discord' );
					echo $mapped_role_name;
					if ( $default_role_name ) {
						echo ', ' . $default_role_name; }
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
	 * @return INT|NULL $curr_level_id
	 */
	function ets_memberpress_discord_get_current_level_id( $mepr_current_user ) {
		$active_prodcuts = $mepr_current_user->active_product_subscriptions('ids');
		if ( $active_prodcuts ) {
			$curr_level_id = sanitize_text_field( trim( $active_prodcuts[0] ) );
			return $curr_level_id;
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
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'memberpress-discord-login' ) {
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

			if ( isset( $_GET['action'] ) && $_GET['action'] == 'discord-connectToBot' ) {
				$params                    = array(
					'client_id'   => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
					'permissions' => MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
					'scope'       => 'bot',
					'guild_id'    => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_guild_id' ) ) ),
				);
				$discord_authorise_api_url = MEMBERPRESS_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );

				wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
				exit;
			}
			if ( isset( $_GET['code'] ) && isset( $_GET['via'] ) ) {
				$code     = sanitize_text_field( trim( $_GET['code'] ) );
				$response = $this->memberpress_create_discord_auth_token( $code, $user_id );

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

							if ( array_key_exists( 'discriminator', $user_body ) ) {
								$discord_user_number           = $user_body['discriminator'];
								$discord_user_name             = $user_body['username'];
								$discord_user_name_with_number = $discord_user_name . '#' . $discord_user_number;
								update_user_meta( $user_id, '_ets_memberpress_discord_username', $discord_user_name_with_number );
							}
							if ( is_array( $user_body ) && array_key_exists( 'id', $user_body ) ) {
								$_ets_memberpress_discord_user_id = sanitize_text_field( trim( $user_body['id'] ) );
								if ( $discord_exist_user_id == $_ets_memberpress_discord_user_id ) {
									$_ets_memberpress_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_role_id', true ) ) );
									if ( ! empty( $_ets_memberpress_discord_role_id ) && $_ets_memberpress_discord_role_id != 'none' ) {
										$this->delete_discord_role( $user_id, $_ets_memberpress_discord_role_id );
									}
								}
								update_user_meta( $user_id, '_ets_memberpress_discord_user_id', $_ets_memberpress_discord_user_id );
								$this->add_discord_member_in_guild( $_ets_memberpress_discord_user_id, $user_id, $access_token );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Create authentication token for discord API
	 *
	 * @param STRING $code
	 * @param INT    $user_id
	 * @return OBJECT API response
	 */
	public function memberpress_create_discord_auth_token( $code, $user_id ) {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		// stop users who having the direct URL of discord Oauth.
		// We must check IF NONE members is set to NO and user having no active membership.
		$allow_none_member = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
		$mepr_current_user = MeprUtils::get_currentuserinfo();
		$curr_level_id     = sanitize_text_field( trim( ets_memberpress_discord_get_current_level_id( $mepr_current_user ) ) );
		if ( $curr_level_id == null && $allow_none_member == 'no' ) {
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
					memberpress_Discord_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
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
				memberpress_Discord_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			}
		}
		return $response;
	}
	
}
