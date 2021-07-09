<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/admin
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Memberpress_Discord_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Static property to define log file name
	 *
	 * @param None
	 * @return string $log_file_name
	 */
	public static $log_file_name = 'discord_api_logs.txt';

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name.'tabs_css', plugin_dir_url( __FILE__ ) . 'css/skeletabs.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/memberpress-discord-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'font_awesome_css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script( $this->plugin_name.'tabs_js', plugin_dir_url( __FILE__ ) . 'js/skeletabs.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'font_awesome_js', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/all.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'ui_js', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array( 'jquery' ), $this->version, false );

		$script_params = array(
			'admin_ajax'        => admin_url( 'admin-ajax.php' ),
			'permissions_const' => MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
			'is_admin'          => is_admin(),
			'ets_memberpress_discord_nonce' => wp_create_nonce( 'ets-memberpress-discord-ajax-nonce' ),
		);

		wp_localize_script( $this->plugin_name, 'etsMemberpressParams', $script_params );
	}
	
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {

		add_submenu_page('memberpress', __('Discord Settings', 'memberpress'), __('Discord Settings', 'memberpress'), 'manage_options', 'memberpress-discord', array($this,'ets_memberpress_discord_setting_page'));

	}

	public function ets_memberpress_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
	
		$set_job_cnrc = isset( $_POST['set_job_cnrc'] ) ? sanitize_textarea_field( trim( $_POST['set_job_cnrc'] ) ) : '';

		$set_job_q_batch_size = isset( $_POST['set_job_q_batch_size'] ) ? sanitize_textarea_field( trim( $_POST['set_job_q_batch_size'] ) ) : '';

		$retry_api_count = isset( $_POST['ets_memberpress_retry_api_count'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_retry_api_count'] ) ) : '';

		$ets_memberpress_discord_send_expiration_warning_dm = isset( $_POST['ets_memberpress_discord_send_expiration_warning_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_send_expiration_warning_dm'] ) ) : false;

		$ets_memberpress_discord_expiration_warning_message = isset( $_POST['ets_memberpress_discord_expiration_warning_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_expiration_warning_message'] ) ) : '';

		$ets_memberpress_discord_send_membership_expired_dm = isset( $_POST['ets_memberpress_discord_send_membership_expired_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_send_membership_expired_dm'] ) ) : false;

		$ets_memberpress_discord_expiration_expired_message  = isset( $_POST['ets_memberpress_discord_expiration_expired_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_expiration_expired_message'] ) ) : '';

		$ets_memberpress_discord_send_welcome_dm  = isset( $_POST['ets_memberpress_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_send_welcome_dm'] ) ) : false;

		$ets_memberpress_discord_welcome_message = isset( $_POST['ets_memberpress_discord_welcome_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_welcome_message'] ) ) : '';

		$ets_memberpress_discord_send_membership_cancel_dm = isset( $_POST['ets_memberpress_discord_send_membership_cancel_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_send_membership_cancel_dm'] ) ) : '';

		$ets_memberpress_discord_cancel_message = isset( $_POST['ets_memberpress_discord_cancel_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_cancel_message'] ) ) : '';

		
		if ( isset( $_POST['adv_submit'] ) ) {
			if ( isset( $_POST['ets_discord_save_adv_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_adv_settings'], 'save_discord_adv_settings' ) ) {
				if ( isset( $_POST['upon_failed_payment'] ) ) {
					update_option( 'ets_memberpress_discord_payment_failed', true );
				} else {
					update_option( 'ets_memberpress_discord_payment_failed', false );
				}

				if ( isset( $_POST['log_api_res'] ) ) {
					update_option( 'ets_memberpress_discord_log_api_response', true );
				} else {
					update_option( 'ets_memberpress_discord_log_api_response', false );
				}

				if ( isset( $_POST['retry_failed_api'] ) ) {
					update_option( 'ets_memberpress_retry_failed_api', true );
				} else {
					update_option( 'ets_memberpress_retry_failed_api', false );
				}

				if ( isset( $_POST['ets_memberpress_discord_send_welcome_dm'] ) ) {
					update_option( 'ets_memberpress_discord_send_welcome_dm', true );
				} else {
					update_option( 'ets_memberpress_discord_send_welcome_dm', false );
				}

				if ( isset( $_POST['ets_memberpress_discord_send_expiration_warning_dm'] ) ) {
					update_option( 'ets_memberpress_discord_send_expiration_warning_dm', true );
				} else {
					update_option( 'ets_memberpress_discord_send_expiration_warning_dm', false );
				}

				if ( isset( $_POST['ets_memberpress_discord_welcome_message'] ) && $_POST['ets_memberpress_discord_welcome_message']!='' ) {
					update_option( 'ets_memberpress_discord_welcome_message', $ets_memberpress_discord_welcome_message );
				} else {
					update_option( 'ets_memberpress_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_memberpress_discord_expiration_warning_message'] ) && $_POST['ets_memberpress_discord_expiration_warning_message']!='' ) {
					update_option( 'ets_memberpress_discord_expiration_warning_message', $ets_memberpress_discord_expiration_warning_message );
				} else {
					update_option( 'ets_memberpress_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_memberpress_discord_expiration_expired_message'] ) && $_POST['ets_memberpress_discord_expiration_expired_message']!='' ) {
					update_option( 'ets_memberpress_discord_expiration_expired_message', $ets_memberpress_discord_expiration_expired_message );
				} else {
					update_option( 'ets_memberpress_discord_expiration_expired_message', 'Your membership is expired' );
				}

				if ( isset( $_POST['ets_memberpress_discord_send_membership_expired_dm'] ) ) {
					update_option( 'ets_memberpress_discord_send_membership_expired_dm', true );
				} else {
					update_option( 'ets_memberpress_discord_send_membership_expired_dm', false );
				}

				if ( isset( $_POST['ets_memberpress_discord_send_membership_cancel_dm'] ) ) {
					update_option( 'ets_memberpress_discord_send_membership_cancel_dm', true );
				} else {
					update_option( 'ets_memberpress_discord_send_membership_cancel_dm', false );
				}

				if ( isset( $_POST['ets_memberpress_discord_cancel_message'] ) && $_POST['ets_memberpress_discord_cancel_message']!='' ) {
					update_option( 'ets_memberpress_discord_cancel_message', $ets_memberpress_discord_cancel_message );
				} else {
					update_option( 'ets_memberpress_discord_cancel_message', 'Your membership is cancled' );
				}

				if ( isset( $_POST['set_job_cnrc'] ) ) {
					if ( $set_job_cnrc < 1 ) {
						update_option( 'ets_memberpress_discord_job_queue_concurrency', 1 );
					} else {
						update_option( 'ets_memberpress_discord_job_queue_concurrency', $set_job_cnrc );
					}
				}

				if ( isset( $_POST['set_job_q_batch_size'] ) ) {
					if ( $set_job_q_batch_size < 1 ) {
						update_option( 'ets_memberpress_discord_job_queue_batch_size', 1 );
					} else {
						update_option( 'ets_memberpress_discord_job_queue_batch_size', $set_job_q_batch_size );
					}
				}

				if ( isset( $_POST['ets_memberpress_retry_api_count'] ) ) {
					if ( $retry_api_count < 1 ) {
						update_option( 'ets_memberpress_retry_api_count', 1 );
					} else {
						update_option( 'ets_memberpress_retry_api_count', $retry_api_count );
					}
				}
				?>
					<div class="notice notice-success is-dismissible support-success-msg">
						<p><?php echo __( 'Your settings are saved successfully.', 'ets_memberpress_discord' ); ?></p>
					</div>
				<?php
			}
		}
		
		$ets_memberpress_discord_client_id    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) );
		$discord_client_secret    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_secret' ) ) );
		$discord_bot_token        = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$ets_memberpress_discord_redirect_url = sanitize_text_field( trim( get_site_url().'/account' ) );
		$ets_discord_roles        = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_role_mapping' ) ) );
		$ets_memberpress_discord_server_id     = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );

		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/memberpress-discord-admin-display.php';
	}

	/**
	 * Save plugin general settings.
	 *
	 * @since    1.0.0
	 */
	public function ets_memberpress_discord_general_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_memberpress_discord_client_id = isset( $_POST['ets_memberpress_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_client_id'] ) ) : '';

		$discord_client_secret = isset( $_POST['ets_memberpress_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_client_secret'] ) ) : '';

		$discord_bot_token = isset( $_POST['ets_memberpress_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_bot_token'] ) ) : '';

		$ets_memberpress_discord_redirect_url = isset( $_POST['ets_memberpress_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_redirect_url'] ) ) : '';

		$ets_memberpress_discord_server_id = isset( $_POST['ets_memberpress_discord_server_id'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_server_id'] ) ) : '';

		if ( isset( $_POST['submit'] ) && ! isset( $_POST['ets_memberpress_discord_role_mapping'] ) ) {
			if ( isset( $_POST['ets_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_settings'], 'save_discord_settings' ) ) {
				if ( $ets_memberpress_discord_client_id ) {
					update_option( 'ets_memberpress_discord_client_id', $ets_memberpress_discord_client_id );
				}

				if ( $discord_client_secret ) {
					update_option( 'ets_memberpress_discord_client_secret', $discord_client_secret );
				}

				if ( $discord_bot_token ) {
					update_option( 'ets_memberpress_discord_bot_token', $discord_bot_token );
				}

				if ( $ets_memberpress_discord_redirect_url ) {
					// add a query string param `via` GH #185.
					$ets_memberpress_discord_redirect_url = $this->get_memberpress_formated_discord_redirect_url( $ets_memberpress_discord_redirect_url );
					update_option( 'ets_memberpress_discord_redirect_url', $ets_memberpress_discord_redirect_url );
				}

				if ( $ets_memberpress_discord_server_id ) {
					update_option( 'ets_memberpress_discord_server_id', $ets_memberpress_discord_server_id );
				}
				
				$message = 'Your settings are saved successfully.';
				$pre_location = $_SERVER['HTTP_REFERER'].'&&save_settings_msg='.$message.'#skeletabsPanel1';
				wp_safe_redirect($pre_location);
			}
		}
	}

	/**
	 * Save plugin general settings.
	 *
	 * @since    1.0.0
	 */
	public function ets_memberpress_discord_role_mapping() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		
		$ets_discord_roles = isset( $_POST['ets_memberpress_discord_role_mapping'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_role_mapping'] ) ) : '';

		$_ets_memberpress_discord_default_role_id = isset( $_POST['defaultRole'] ) ? sanitize_textarea_field( trim( $_POST['defaultRole'] ) ) : '';

		$allow_none_member = isset( $_POST['allow_none_member'] ) ? sanitize_textarea_field( trim( $_POST['allow_none_member'] ) ) : '';

		if ( $ets_discord_roles ) {
			$ets_discord_roles   = stripslashes( $ets_discord_roles );
			$save_mapping_status = update_option( 'ets_memberpress_discord_role_mapping', $ets_discord_roles );
			if ( isset( $_POST['ets_memberpress_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_memberpress_discord_role_mappings_nonce'], 'discord_role_mappings_nonce' ) ) {
				if ( ( $save_mapping_status || isset( $_POST['ets_memberpress_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
					if ( $_ets_memberpress_discord_default_role_id ) {
						update_option( '_ets_memberpress_discord_default_role_id', $_ets_memberpress_discord_default_role_id );
					}

					if ( $allow_none_member ) {
						update_option( 'ets_memberpress_allow_none_member', $allow_none_member );
					}

					$message = 'Your mappings are saved successfully.';
					$pre_location = $_SERVER['HTTP_REFERER'].'&&save_settings_msg='.$message.'#skeletabsPanel2';
					wp_safe_redirect($pre_location);
				}
				if ( isset( $_POST['flush'] ) ) {
					delete_option( 'ets_memberpress_discord_role_mapping' );
					delete_option( '_ets_memberpress_discord_default_role_id' );
					delete_option( 'ets_memberpress_allow_none_member' );
					$message = ' Your settings flushed successfully.';
					$pre_location = $_SERVER['HTTP_REFERER'].'&&save_settings_msg='.$message.'#skeletabsPanel2';
					wp_safe_redirect($pre_location);
				}
			}
		}
	}

	/**
	 * Clear previous logs history
	 *
	 * @param None
	 * @return None
	 */
	public function ets_memberpress_discord_clear_logs() {
		if ( ! is_user_logged_in() && ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_memberpress_discord_nonce'], 'ets-memberpress-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		try {
			$file_name = $this::$log_file_name;
			if ( fopen( MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . $file_name, 'w' ) ) {
				$myfile = fopen( MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . $file_name, 'w' );
				$txt    = current_time( 'mysql' ) . " => Clear logs Successfully\n";
				fwrite( $myfile, $txt );
				fclose( $myfile );
			} else {
				throw new Exception( 'Could not open the file!' );
			}
		} catch ( Exception $e ) {
			return wp_send_json(
				array(
					'error' => array(
						'msg'  => $e->getMessage(),
						'code' => $e->getCode(),
					),
				)
			);
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
	static function write_api_response_logs( $response_arr, $user_id, $backtrace_arr = [] ) {
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
		$log_file_name = self::$log_file_name;
		if ( is_array( $response_arr ) && array_key_exists( 'code', $response_arr ) ) {
			$error .= '==>File:' . $backtrace_arr['file'] . $user_details . '::Line:' . $backtrace_arr['line'] . '::Function:' . $backtrace_arr['function'] . '::' . $response_arr['code'] . ':' . $response_arr['message'];
      file_put_contents( plugin_dir_url( __FILE__ ) . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
		} elseif ( is_array( $response_arr ) && array_key_exists( 'error', $response_arr ) ) {
			$error .= '==>File:' . $backtrace_arr['file'] . $user_details . '::Line:' . $backtrace_arr['line'] . '::Function:' . $backtrace_arr['function'] . '::' . $response_arr['error'];
      file_put_contents( plugin_dir_url( __FILE__ ) . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
		} elseif ( $log_api_response == true ) {
			$error .= json_encode($response_arr).'::'.$user_id;
      file_put_contents( plugin_dir_url( __FILE__ ) . $log_file_name, $error . PHP_EOL, FILE_APPEND | LOCK_EX );
		}
		
	}

	/**
	* Fetch all roles from discord server
	*
	* @return OBJECT REST API response
	*/
 static function ets_memberpress_load_discord_roles() {
	if ( ! current_user_can( 'administrator' ) ) {
		wp_send_json_error( 'You do not have sufficient rights', 403 );
		exit();
	}
	// Check for nonce security
	if ( ! wp_verify_nonce( $_POST['ets_memberpress_discord_nonce'], 'ets-memberpress-discord-ajax-nonce' ) ) {
		wp_send_json_error( 'You do not have sufficient rights', 403 );
		exit();
	}
	
	$user_id = get_current_user_id();

	$server_id          = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
	$discord_bot_token = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
	if ( $server_id && $discord_bot_token ) {
		$discod_server_roles_api = MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $server_id . '/roles';
		$guild_args              = array(
			'method'  => 'GET',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
		);
		$guild_response          = wp_remote_post( $discod_server_roles_api, $guild_args );

		// ets_memberpress_discord_log_api_response( $user_id, $discod_server_roles_api, $guild_args, $guild_response );

		$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );

		if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
			if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
				$this->write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			} else {
				$response_arr['previous_mapping'] = get_option( 'ets_memberpress_discord_role_mapping' );

				$discord_roles = array();
				foreach ( $response_arr as $key => $value ) {
					$isbot = false;
					if ( is_array( $value ) ) {
						if ( array_key_exists( 'tags', $value ) ) {
							if ( array_key_exists( 'bot_id', $value['tags'] ) ) {
								$isbot = true;
							}
						}
					}
					if ( $key != 'previous_mapping' && $isbot == false && isset( $value['name'] ) && $value['name'] != '@everyone' ) {
						$discord_roles[ $value['id'] ] = $value['name'];
					}
				}
				update_option( 'ets_memberpress_discord_all_roles', serialize( $discord_roles ) );
			}
		}
			return wp_send_json( $response_arr );
	}
	 
 }

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
}
