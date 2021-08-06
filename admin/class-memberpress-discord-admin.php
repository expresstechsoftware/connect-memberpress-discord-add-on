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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

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

		wp_enqueue_style( $this->plugin_name . 'tabs_css', plugin_dir_url( __FILE__ ) . 'css/skeletabs.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/memberpress-discord-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'font_awesome_css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css', array(), $this->version, 'all' );
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
		wp_enqueue_script( $this->plugin_name . 'tabs_js', plugin_dir_url( __FILE__ ) . 'js/skeletabs.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . 'font_awesome_js', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/all.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );

		$script_params = array(
			'admin_ajax'                    => admin_url( 'admin-ajax.php' ),
			'permissions_const'             => MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
			'is_admin'                      => is_admin(),
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
		add_submenu_page( 'memberpress', __( 'Discord Settings', 'memberpress' ), __( 'Discord Settings', 'memberpress' ), 'manage_options', 'memberpress-discord', array( $this, 'ets_memberpress_discord_setting_page' ) );
	}

	/**
	 * Add plugin admin view.
	 */
	public function ets_memberpress_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
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
					$ets_memberpress_discord_redirect_url = ets_memberpress_discord_get_memberpress_formated_discord_redirect_url( $ets_memberpress_discord_redirect_url );
					update_option( 'ets_memberpress_discord_redirect_url', $ets_memberpress_discord_redirect_url );
				}

				if ( $ets_memberpress_discord_server_id ) {
					update_option( 'ets_memberpress_discord_server_id', $ets_memberpress_discord_server_id );
				}

				$message = 'Your settings are saved successfully.';
				if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
					$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#skeletabsPanel1';
					wp_safe_redirect( $pre_location );
				}
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

		$ets_memberpress_discord_default_role_id = isset( $_POST['defaultRole'] ) ? sanitize_textarea_field( trim( $_POST['defaultRole'] ) ) : '';

		$allow_none_member = isset( $_POST['allow_none_member'] ) ? sanitize_textarea_field( trim( $_POST['allow_none_member'] ) ) : '';

		if ( $ets_discord_roles ) {
			$ets_discord_roles   = stripslashes( $ets_discord_roles );
			$save_mapping_status = update_option( 'ets_memberpress_discord_role_mapping', $ets_discord_roles );
			if ( isset( $_POST['ets_memberpress_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_memberpress_discord_role_mappings_nonce'], 'discord_role_mappings_nonce' ) ) {
				if ( ( $save_mapping_status || isset( $_POST['ets_memberpress_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
					if ( $ets_memberpress_discord_default_role_id ) {
						update_option( 'ets_memberpress_discord_default_role_id', $ets_memberpress_discord_default_role_id );
					}

					if ( $allow_none_member ) {
						update_option( 'ets_memberpress_allow_none_member', $allow_none_member );
					}

					$message = 'Your mappings are saved successfully.';
					if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
						$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#skeletabsPanel2';
						wp_safe_redirect( $pre_location );
					}
				}
				if ( isset( $_POST['flush'] ) ) {
					delete_option( 'ets_memberpress_discord_role_mapping' );
					delete_option( 'ets_memberpress_discord_default_role_id' );
					delete_option( 'ets_memberpress_allow_none_member' );
					$message = ' Your settings flushed successfully.';
					if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
						$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#skeletabsPanel2';
						wp_safe_redirect( $pre_location );
					}
				}
			}
		}
	}

	/**
	 * Save plugin general settings.
	 *
	 * @since    1.0.0
	 */
	public function ets_memberpress_discord_advance_settings() {
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

		$ets_memberpress_discord_expiration_expired_message = isset( $_POST['ets_memberpress_discord_expiration_expired_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_expiration_expired_message'] ) ) : '';

		$ets_memberpress_discord_send_welcome_dm = isset( $_POST['ets_memberpress_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_send_welcome_dm'] ) ) : false;

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

				if ( isset( $_POST['ets_memberpress_discord_welcome_message'] ) && $_POST['ets_memberpress_discord_welcome_message'] != '' ) {
					update_option( 'ets_memberpress_discord_welcome_message', $ets_memberpress_discord_welcome_message );
				} else {
					update_option( 'ets_memberpress_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_memberpress_discord_expiration_warning_message'] ) && $_POST['ets_memberpress_discord_expiration_warning_message'] != '' ) {
					update_option( 'ets_memberpress_discord_expiration_warning_message', $ets_memberpress_discord_expiration_warning_message );
				} else {
					update_option( 'ets_memberpress_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_memberpress_discord_expiration_expired_message'] ) && $_POST['ets_memberpress_discord_expiration_expired_message'] != '' ) {
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

				if ( isset( $_POST['ets_memberpress_discord_cancel_message'] ) && $_POST['ets_memberpress_discord_cancel_message'] != '' ) {
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
				$message = 'Your settings are saved successfully.';
				if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
					$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#skeletabsPanel3';
					wp_safe_redirect( $pre_location );
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
		// Check for nonce security.
		if ( isset( $_POST['ets_memberpress_discord_nonce'] ) && ! wp_verify_nonce( $_POST['ets_memberpress_discord_nonce'], 'ets-memberpress-discord-ajax-nonce' ) ) {
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
	 * Fetch all roles from discord server
	 *
	 * @return OBJECT REST API response
	 */
	public function ets_memberpress_load_discord_roles() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security.
		if ( ! wp_verify_nonce( $_POST['ets_memberpress_discord_nonce'], 'ets-memberpress-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$user_id = get_current_user_id();

		$server_id         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
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

			ets_memberpress_discord_log_api_response( $user_id, $discod_server_roles_api, $guild_args, $guild_response );

			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );

			if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
				if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
					write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
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
						if ( 'previous_mapping' !== $key && false === $isbot && isset( $value['name'] ) && $value['name'] != '@everyone' ) {
							$discord_roles[ $value['id'] ] = $value['name'];
						}
					}
					update_option( 'ets_memberpress_discord_all_roles', wp_json_encode( $discord_roles ) );
				}
			}
			return wp_send_json( $response_arr );
		}

	}

	/**
	 * Action schedule to schedule a function to run upon memberpress Expiry
	 *
	 * @param ARRAY   $txn
	 * @param BOOLEAN $status
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_expiry( $expired_txn, $status = false ) {
		$access_token       = sanitize_text_field( trim( get_user_meta( $expired_txn->user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$expired_membership = array();
		if ( ! empty( $expired_txn ) ) {
				$expired_membership = array(
					'product_id' => $expired_txn->product_id,
					'created_at' => $expired_txn->created_at,
					'expires_at' => $expired_txn->expires_at,
				);
		}

		if ( $status == 'none' && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_expiry', array( $expired_txn->user_id, $expired_membership ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}

	/**
	 * Action schedule to schedule a function to run upon memberpress cancel
	 *
	 * @param ARRAY $event
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_cancelled( $event ) {
		$subscription         = $event->get_data();
		$user                 = $subscription->user();
		$access_token         = sanitize_text_field( trim( get_user_meta( $user->ID, '_ets_memberpress_discord_access_token', true ) ) );
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

	/**
	 * Action schedule to schedule a function to run upon memberpress transaction delete
	 *
	 * @param ARRAY $txn
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_delete_transaction( $deleted_txn ) {
		$access_token       = sanitize_text_field( trim( get_user_meta( $deleted_txn->user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$deleted_membership = array();
		if ( ! empty( $deleted_txn ) ) {
				$deleted_membership = array(
					'product_id' => $deleted_txn->product_id,
					'created_at' => $deleted_txn->created_at,
					'expires_at' => $deleted_txn->expires_at,
				);
		}
		if ( $deleted_membership && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_cancelled', array( $deleted_txn->user_id, $deleted_membership ), MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}


	/*
	* Action scheduler method to process expired memberpress members.
	* @param INT $user_id
	* @param INT $expired_membership
	*/
	public function ets_memberpress_discord_as_handler_memberpress_expiry( $user_id, $expired_membership ) {
		$this->ets_memberpress_discord_set_member_roles( $user_id, $expired_membership, false, true );
	}

	/*
	* Action scheduler method to process cancelled memberpress members.
	* @param INT $user_id
	* @param INT $cancelled_membership
	*/
	public function ets_memberpress_discord_as_handler_memberpress_cancelled( $user_id, $cancelled_membership ) {
		$this->ets_memberpress_discord_set_member_roles( $user_id, false, $cancelled_membership, true );
	}

	/**
	 * Method to adjust level mapped and default role of a member.
	 *
	 * @param INT   $user_id
	 * @param ARRAY $expired_membership
	 * @param ARRAY $cancelled_membership
	 * @param BOOL  $is_schedule
	 */
	private function ets_memberpress_discord_set_member_roles( $user_id, $expired_membership = false, $cancelled_membership = false, $is_schedule = true ) {
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
						if ( $mapped_role_id && $expired_membership == false && $cancelled_membership == false ) {
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
			if ( $ets_memberpress_discord_send_membership_expired_dm == true && $expired_membership !== false && $allow_none_member = 'yes' ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $expired_membership, 'expired' ), 'ets-memberpress-discord' );
			}

			// Send DM about cancel, but only when allow_none_member setting is yes
			if ( $ets_memberpress_discord_send_membership_cancel_dm == true && $cancelled_membership !== false && $allow_none_member = 'yes' ) {
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

		$guild_id                         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_guild_id' ) ) );
		$_ets_memberpress_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$discord_bot_token                = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$discord_delete_role_api_url      = MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_memberpress_discord_user_id . '/roles/' . $ets_role_id;
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
