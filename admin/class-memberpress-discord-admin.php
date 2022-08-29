<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    ETS_Memberpress_Discord
 * @subpackage ETS_Memberpress_Discord/admin
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class ETS_Memberpress_Discord_Admin {

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
		wp_register_style( $this->plugin_name . 'tabs_css', plugin_dir_url( __FILE__ ) . 'css/skeletabs.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/memberpress-discord-admin.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$min_js = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_script( $this->plugin_name . 'tabs_js', plugin_dir_url( __FILE__ ) . 'js/skeletabs.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-admin' . $min_js . '.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name . '-search', plugin_dir_url( __FILE__ ) . 'js/memberpress-discord-search' . $min_js . '.js', array( 'jquery' ), $this->version, false );
		$script_params = array(
			'admin_ajax'                    => admin_url( 'admin-ajax.php' ),
			'permissions_const'             => ETS_MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
			'is_admin'                      => is_admin(),
			'ets_memberpress_discord_nonce' => wp_create_nonce( 'ets-memberpress-discord-ajax-nonce' ),
			'discord_icon'                  => ETS_Memberpress_Discord::get_discord_logo_white(),
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
		wp_enqueue_style( $this->plugin_name . 'tabs_css' );
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name . 'tabs_js' );
		wp_enqueue_script( $this->plugin_name );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/memberpress-discord-admin-display.php';
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

		if ( isset( $_POST['submit'] ) && ! isset( $_POST['ets_memberpress_discord_role_mapping'] ) ) {
			if ( isset( $_POST['ets_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_settings'], 'save_discord_settings' ) ) {
				$ets_memberpress_discord_client_id = isset( $_POST['ets_memberpress_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_client_id'] ) ) : '';

				$discord_client_secret = isset( $_POST['ets_memberpress_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_client_secret'] ) ) : '';

				$discord_bot_token = isset( $_POST['ets_memberpress_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_bot_token'] ) ) : '';

				$ets_memberpress_discord_redirect_url = isset( $_POST['ets_memberpress_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_redirect_url'] ) ) : '';

				$ets_memberpress_discord_server_id = isset( $_POST['ets_memberpress_discord_server_id'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_server_id'] ) ) : '';

				$ets_memberpress_discord_bot_auth_redirect = isset( $_POST['ets_memberpress_discord_bot_auth_redirect'] ) ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_bot_auth_redirect'] ) ) : '';

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
				update_option( 'ets_memberpress_discord_bot_auth_redirect', $ets_memberpress_discord_bot_auth_redirect );

				ets_memberpress_discord_update_bot_name_option();
				$message = 'Your settings are saved successfully.';
				if ( isset( $_POST['current_url'] ) ) {
					// This will delete Stale DM channels.
					delete_metadata( 'user', 0, '_ets_memberpress_discord_dm_channel', '', true );
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_general_settings';
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

		$ets_discord_roles   = stripslashes( $ets_discord_roles );
		$save_mapping_status = update_option( 'ets_memberpress_discord_role_mapping', $ets_discord_roles );
		if ( isset( $_POST['ets_memberpress_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_memberpress_discord_role_mappings_nonce'], 'discord_role_mappings_nonce' ) ) {
			if ( ( $save_mapping_status || isset( $_POST['ets_memberpress_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
				if ( $ets_memberpress_discord_default_role_id ) {
					update_option( 'ets_memberpress_discord_default_role_id', $ets_memberpress_discord_default_role_id );
				}

				if ( $allow_none_member ) {
					update_option( 'ets_memberpress_discord_allow_none_member', $allow_none_member );
				}

				$message = 'Your mappings are saved successfully.';
				if ( isset( $_POST['current_url'] ) ) {
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_role_mapping';
					wp_safe_redirect( $pre_location );
				}
			}
			if ( isset( $_POST['flush'] ) ) {
				delete_option( 'ets_memberpress_discord_role_mapping' );
				delete_option( 'ets_memberpress_discord_default_role_id' );
				delete_option( 'ets_memberpress_discord_allow_none_member' );
				$message = ' Your settings flushed successfully.';
				if ( isset( $_POST['current_url'] ) ) {
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_role_mapping';
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
	public function ets_memberpress_discord_advance_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$set_job_cnrc = isset( $_POST['set_job_cnrc'] ) ? sanitize_textarea_field( trim( $_POST['set_job_cnrc'] ) ) : '';

		$set_job_q_batch_size = isset( $_POST['set_job_q_batch_size'] ) ? sanitize_textarea_field( trim( $_POST['set_job_q_batch_size'] ) ) : '';

		$retry_api_count = isset( $_POST['ets_memberpress_discord_retry_api_count'] ) ? sanitize_textarea_field( trim( $_POST['ets_memberpress_discord_retry_api_count'] ) ) : '';

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
					update_option( 'ets_memberpress_discord_retry_failed_api', true );
				} else {
					update_option( 'ets_memberpress_discord_retry_failed_api', false );
				}

				if ( isset( $_POST['memberpress_member_kick_out'] ) ) {
					update_option( 'ets_memberpress_discord_member_kick_out', true );
				} else {
					update_option( 'ets_memberpress_discord_member_kick_out', false );
				}

				if ( isset( $_POST['memberpress_member_discord_login'] ) ) {
					update_option( 'ets_memberpress_discord_login_with_discord', true );
				} else {
					update_option( 'ets_memberpress_discord_login_with_discord', false );
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

				if ( isset( $_POST['ets_memberpress_discord_retry_api_count'] ) ) {
					if ( $retry_api_count < 1 ) {
						update_option( 'ets_memberpress_discord_retry_api_count', 1 );
					} else {
						update_option( 'ets_memberpress_discord_retry_api_count', $retry_api_count );
					}
				}
				$message = 'Your settings are saved successfully.';
				if ( isset( $_POST['current_url'] ) ) {
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_advance';
					wp_safe_redirect( $pre_location );
				}
			}
		}
	}

	/**
	 * Save apearance settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_memberpress_discord_save_appearance_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$ets_memberpress_btn_color                    = isset( $_POST['ets_memberpress_btn_color'] ) && $_POST['ets_memberpress_btn_color'] !== '' ? sanitize_text_field( trim( $_POST['ets_memberpress_btn_color'] ) ) : '#77a02e';
		$ets_memberpress_discord_btn_disconnect_color = isset( $_POST['ets_memberpress_discord_btn_disconnect_color'] ) && $_POST['ets_memberpress_discord_btn_disconnect_color'] != '' ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_btn_disconnect_color'] ) ) : '#ff0000';
		$ets_memberpress_loggedin_btn_text            = isset( $_POST['ets_memberpress_loggedin_btn_text'] ) && $_POST['ets_memberpress_loggedin_btn_text'] != '' ? sanitize_text_field( trim( $_POST['ets_memberpress_loggedin_btn_text'] ) ) : 'Connect To Discord';
		$ets_memberpress_loggedout_btn_text           = isset( $_POST['ets_memberpress_loggedout_btn_text'] ) && $_POST['ets_memberpress_loggedout_btn_text'] != '' ? sanitize_text_field( trim( $_POST['ets_memberpress_loggedout_btn_text'] ) ) : 'Login With Discord';
		$ets_memberpress_discord_disconnect_btn_text  = $_POST['ets_memberpress_discord_disconnect_btn_text'] ? sanitize_text_field( trim( $_POST['ets_memberpress_discord_disconnect_btn_text'] ) ) : 'Disconnect From Discord';

		if ( isset( $_POST['apr_submit'] ) ) {

			if ( isset( $_POST['ets_discord_save_aprnc_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_aprnc_settings'], 'save_discord_aprnc_settings' ) ) {
				if ( $ets_memberpress_btn_color ) {
					update_option( 'ets_memberpress_discord_btn_color', $ets_memberpress_btn_color );
				}
				if ( $ets_memberpress_discord_btn_disconnect_color ) {
					update_option( 'ets_memberpress_discord_btn_disconnect_color', $ets_memberpress_discord_btn_disconnect_color );
				}
				if ( $ets_memberpress_loggedout_btn_text ) {
					update_option( 'ets_memberpress_discord_loggedout_btn_text', $ets_memberpress_loggedout_btn_text );
				}
				if ( $ets_memberpress_loggedin_btn_text ) {
					update_option( 'ets_memberpress_discord_loggedin_btn_text', $ets_memberpress_loggedin_btn_text );
				}
				if ( $ets_memberpress_discord_disconnect_btn_text ) {
					update_option( 'ets_memberpress_discord_disconnect_btn_text', $ets_memberpress_discord_disconnect_btn_text );
				}
				$message = 'Your settings are saved successfully.';
				if ( isset( $_POST['current_url'] ) ) {
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_appearance';
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
			$uuid      = get_option( 'ets_memberpress_discord_uuid_file_name' );
			$file_name = $uuid . $this::$log_file_name;
			if ( fopen( WP_CONTENT_DIR . '/' . $file_name, 'w' ) ) {
				$myfile = fopen( WP_CONTENT_DIR . '/' . $file_name, 'w' );
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
	 * Send mail to support form current user
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_memberpress_discord_send_support_mail() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		if ( isset( $_POST['save'] ) ) {
			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['ets_discord_send_support_mail'], 'send_support_mail' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
			}
			$etsUserName  = isset( $_POST['ets_user_name'] ) ? sanitize_text_field( trim( $_POST['ets_user_name'] ) ) : '';
			$etsUserEmail = isset( $_POST['ets_user_email'] ) ? sanitize_text_field( trim( $_POST['ets_user_email'] ) ) : '';
			$message      = isset( $_POST['ets_support_msg'] ) ? sanitize_text_field( trim( $_POST['ets_support_msg'] ) ) : '';
			$sub          = isset( $_POST['ets_support_subject'] ) ? sanitize_text_field( trim( $_POST['ets_support_subject'] ) ) : '';

			if ( $etsUserName && $etsUserEmail && $message && $sub ) {

				$subject   = $sub;
				$to        = 'contact@expresstechsoftwares.com';
				$content   = 'Name: ' . $etsUserName . '<br>';
				$content  .= 'Contact Email: ' . $etsUserEmail . '<br>';
				$content  .= 'MemberPress Support Message: ' . $message;
				$headers   = array();
				$blogemail = get_bloginfo( 'admin_email' );
				$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $blogemail . '>' . "\r\n";
				$mail      = wp_mail( $to, $subject, $content, $headers );

				if ( $mail ) {
					$message = 'Your request have been successfully submitted!';
					if ( isset( $_POST['current_url'] ) ) {
						$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#mepr_support';
						wp_safe_redirect( $pre_location );
					}
				}
			}
		}
	}

	/**
	 * Manage user roles on subscription  payment failed
	 *
	 * @param ARRAY $old_order
	 */
	public function ets_memberpress_discord_subscription_payment_failed( $txn ) {
		$user_id         = $txn->user_id;
		$ets_payment_fld = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_payment_failed' ) ) );

		if ( $ets_payment_fld == true && isset( $user_id ) ) {
			$this->ets_memberpress_discord_set_member_roles( $user_id, false, false, true );
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
			$discod_server_roles_api = ETS_MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $server_id . '/roles';
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
							$discord_roles[ $value['id'] ]       = $value['name'];
							$discord_roles_color[ $value['id'] ] = $value['color'];
						}
					}
					update_option( 'ets_memberpress_discord_all_roles', wp_json_encode( $discord_roles ) );
					update_option( 'ets_memberpress_discord_roles_color', serialize( $discord_roles_color ) );
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
					'txn_number' => $expired_txn->trans_num,
				);
		}

		if ( $status == 'none' && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_expiry', array( $expired_txn->user_id, $expired_membership ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
					'txn_number' => $subscription->trans_num,
					'expires_at' => $subscription->expires_at,
				);
		}
		if ( $cancelled_membership && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_cancelled', array( $user->ID, $cancelled_membership ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
					'txn_number' => $deleted_txn->trans_num,
					'created_at' => $deleted_txn->created_at,
					'expires_at' => $deleted_txn->expires_at,
				);
		}
		if ( $deleted_membership && $access_token ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_cancelled', array( $deleted_txn->user_id, $deleted_membership ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
		$memberpress_discord                                = new ETS_Memberpress_Discord();
		$plugin_admin                                       = new ETS_Memberpress_Discord_Admin( $memberpress_discord->get_plugin_name(), $memberpress_discord->get_version() );
		$plugin_public                                      = new ETS_Memberpress_Discord_Public( $memberpress_discord->get_plugin_name(), $memberpress_discord->get_version(), $plugin_admin );
		$allow_none_member                                  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_allow_none_member' ) ) );
		$default_role                                       = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
		$ets_memberpress_discord_role_mapping               = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$active_memberships                                 = ets_memberpress_discord_get_active_memberships( $user_id );
		$previous_default_role                              = get_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
		$ets_memberpress_discord_send_membership_expired_dm = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_membership_expired_dm' ) ) );
		$ets_memberpress_discord_send_membership_cancel_dm  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_membership_cancel_dm' ) ) );
		$access_token                                       = get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true );
		$user_txn = null;
		if ( ! empty( $access_token ) ) {
			if ( $expired_membership ) {
				$user_txn = $expired_membership['txn_number'];
			}
			if ( $cancelled_membership ) {
				$user_txn = $cancelled_membership['txn_number'];
			}

			if ( $user_txn !== null ) {
				$_ets_memberpress_discord_role_id = get_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $user_txn, true );
				// delete already assigned role.
				if ( isset( $_ets_memberpress_discord_role_id ) && $_ets_memberpress_discord_role_id != '' && $_ets_memberpress_discord_role_id != 'none' ) {
						$this->memberpress_delete_discord_role( $user_id, $_ets_memberpress_discord_role_id['role_id'], $is_schedule );
						delete_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $user_txn, true );
				}
				delete_user_meta( $user_id, '_ets_memberpress_discord_expitration_warning_dm_for_' . $user_txn );
			}

			if ( is_array( $active_memberships ) && count( $active_memberships ) != 0 ) {
				// Assign role which is mapped to the mmebership level.
				foreach ( $active_memberships as $active_membership ) {
					if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $active_membership->product_id, $ets_memberpress_discord_role_mapping ) ) {
						$mapped_role_id = sanitize_text_field( trim( $ets_memberpress_discord_role_mapping[ 'level_id_' . $active_membership->product_id ] ) );
						if ( $mapped_role_id && $expired_membership == false && $cancelled_membership == false ) {
							$plugin_public->put_discord_role_api( $user_id, $mapped_role_id, $is_schedule );
							$assigned_role = array(
								'role_id'    => $mapped_role_id,
								'product_id' => $active_membership->product_id,
							);
							update_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $active_membership->txn_number, $assigned_role );
							delete_user_meta( $user_id, '_ets_memberpress_discord_expitration_warning_dm_for_' . $active_membership->txn_number );
						}
					}
				}
			}
			// Assign role which is saved as default.
			if ( $default_role != 'none' && $previous_default_role != $default_role ) {
				if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
						$this->memberpress_delete_discord_role( $user_id, $previous_default_role, $is_schedule );
					delete_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
				}
				$plugin_public->put_discord_role_api( $user_id, $default_role, $is_schedule );
				update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
			} elseif ( $default_role == 'none' ) {
				if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
					$this->memberpress_delete_discord_role( $user_id, $previous_default_role, $is_schedule );
				}
				update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
			}

			if ( isset( $user_id ) && $allow_none_member == 'no' && empty( $active_memberships ) ) {
				$plugin_public->memberpress_delete_member_from_guild( $user_id, false );
			}

			// Send DM about expiry, but only when allow_none_member setting is yes
			if ( $ets_memberpress_discord_send_membership_expired_dm == true && $expired_membership !== false && $allow_none_member = 'yes' ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $expired_membership, 'expired' ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
			}

			// Send DM about cancel, but only when allow_none_member setting is yes
			if ( $ets_memberpress_discord_send_membership_cancel_dm == true && $cancelled_membership !== false && $allow_none_member = 'yes' ) {
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $cancelled_membership, 'cancel' ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_schedule_delete_role', array( $user_id, $ets_role_id, $is_schedule ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
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
		$server_id                        = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
		$_ets_memberpress_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_user_id', true ) ) );
		$discord_bot_token                = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
		$discord_delete_role_api_url      = ETS_MEMBERPRESS_DISCORD_API_URL . 'guilds/' . $server_id . '/members/' . $_ets_memberpress_discord_user_id . '/roles/' . $ets_role_id;
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

	/**
	 * Action scheduler method to process complete transaction event.
	 *
	 * @param INT $user_id
	 * @param INT $complete_txn
	 */
	public function ets_memberpress_discord_as_handler_memberpress_complete_transaction( $user_id, $complete_txn ) {
		$memberpress_discord                     = new ETS_Memberpress_Discord();
		$plugin_admin                            = new ETS_Memberpress_Discord_Admin( $memberpress_discord->get_plugin_name(), $memberpress_discord->get_version() );
		$plugin_public                           = new ETS_Memberpress_Discord_Public( $memberpress_discord->get_plugin_name(), $memberpress_discord->get_version(), $plugin_admin );
		$ets_memberpress_discord_role_mapping    = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
		$default_role                            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
		$previous_default_role                   = get_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
		$ets_memberpress_discord_send_welcome_dm = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_welcome_dm' ) ) );

		if ( is_array( $ets_memberpress_discord_role_mapping ) && array_key_exists( 'level_id_' . $complete_txn['product_id'], $ets_memberpress_discord_role_mapping ) ) {
			$mapped_role_id = sanitize_text_field( trim( $ets_memberpress_discord_role_mapping[ 'level_id_' . $complete_txn['product_id'] ] ) );
			if ( $mapped_role_id ) {
				$plugin_public->put_discord_role_api( $user_id, $mapped_role_id, true );
				$assigned_role = array(
					'role_id'    => $mapped_role_id,
					'product_id' => $complete_txn['product_id'],
				);
				update_user_meta( $user_id, '_ets_memberpress_discord_role_id_for_' . $complete_txn['txn_number'], $assigned_role );
				// Send welcome message.
				if ( true == $ets_memberpress_discord_send_welcome_dm && $complete_txn ) {
					as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $user_id, $complete_txn, 'welcome' ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
				}
			}
		}

		// Assign role which is saved as default.
		if ( $default_role != 'none' ) {
			if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
				$this->memberpress_delete_discord_role( $user_id, $previous_default_role, false );
				delete_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', true );
			}
			$plugin_public->put_discord_role_api( $user_id, $default_role, true );
			update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
		} elseif ( $default_role == 'none' ) {
			if ( isset( $previous_default_role ) && $previous_default_role != '' && $previous_default_role != 'none' ) {
				$this->memberpress_delete_discord_role( $user_id, $previous_default_role, true );
			}
			update_user_meta( $user_id, '_ets_memberpress_discord_default_role_id', $default_role );
		}
	}

	/**
	 * Action schedule to schedule a function to run upon memberpress complete transaction
	 *
	 * @param ARRAY $event
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schdule_job_memberpress_transactions_status_changed( $old_status, $new_status, $txn ) {
		$access_token          = sanitize_text_field( trim( get_user_meta( $txn->user_id, '_ets_memberpress_discord_access_token', true ) ) );
		$pre_membership_on_txn = get_user_meta( $txn->user_id, '_ets_memberpress_discord_role_id_for_' . $txn->trans_num, true );
		$active_memberships    = ets_memberpress_discord_get_active_memberships( $txn->user_id );
		$complete_txn          = array();
		$active_product_ids    = array();
		if ( is_array( $active_memberships ) && count( $active_memberships ) > 0 ) {
			foreach ( $active_memberships as $active_membership ) {
				array_push( $active_product_ids, $active_membership->product_id );
			}
		}
		if ( ! empty( $txn ) ) {
				$complete_txn = array(
					'product_id' => $txn->product_id,
					'txn_number' => $txn->trans_num,
					'created_at' => $txn->created_at,
					'expires_at' => $txn->expires_at,
				);
		}

		if ( isset( $pre_membership_on_txn['product_id'] ) ) {
			if ( $complete_txn && $access_token && $new_status == 'complete' && $pre_membership_on_txn['product_id'] != $complete_txn['product_id'] ) {
				if ( ! in_array( $pre_membership_on_txn['product_id'], $active_product_ids ) ) {
					$this->memberpress_delete_discord_role( $txn->user_id, $pre_membership_on_txn['role_id'], true );
					delete_user_meta( $txn->user_id, '_ets_memberpress_discord_role_id_for_' . $txn->trans_num, true );
				}
				as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_complete_transaction', array( $txn->user_id, $complete_txn ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
			} else {
				$this->ets_memberpress_discord_set_member_roles( $txn->user_id, false, false, false );
			}
		} elseif ( $complete_txn && $access_token && $new_status == 'complete' ) {
			as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_complete_transaction', array( $txn->user_id, $complete_txn ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
		}
	}

	/**
	 * Action scheduler method to process complete transaction event.
	 *
	 * @param ARRAY $columns
	 * @return ARRAY  $return
	 */
	public function ets_memberpress_discord_members_list_add_column( $columns ) {
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );
		$columns['col_memberpress_discord']     = __( 'Discord', 'connect-memberpress-discord-add-on' );
		$columns['col_memberpress_joined_date'] = __( 'Joined Date', 'connect-memberpress-discord-add-on' );
		return $columns;
	}

	/**
	 * Action scheduler method to process complete transaction event.
	 *
	 * @param String $attributes
	 * @param Object $rec
	 * @param String $column_name
	 * @param String $column_display_name
	 */
	public function ets_memberpress_discord_members_list_add_custom_column_value( $attributes, $rec, $column_name, $column_display_name ) {
		$access_token    = sanitize_text_field( trim( get_user_meta( $rec->ID, '_ets_memberpress_discord_access_token', true ) ) );
		$discord_user_id = sanitize_text_field( trim( get_user_meta( $rec->ID, '_ets_memberpress_discord_user_id', true ) ) );
		switch ( $column_name ) {
			case 'col_memberpress_discord':
				?>
				<td <?php echo esc_attr( $attributes ); ?>>
				<?php
				if ( $access_token ) {
					$discord_username = sanitize_text_field( trim( get_user_meta( $rec->ID, '_ets_memberpress_discord_username', true ) ) );
					echo '<p class="' . esc_attr( $rec->ID ) . ' ets-save-success">Success</p><a class="button button-primary ets-memberpress-run-api" data-uid="' . esc_attr( $rec->ID ) . '" href="#">';
					echo __( 'Run API', 'connect-memberpress-discord-add-on' );
					echo '</a><span class="' . esc_attr( $rec->ID ) . ' spinner"></span>';
					echo esc_html( $discord_username );
					echo 'Discord ID - <p>' . $discord_user_id . '</p>';
				} else {
					echo __( 'Not Connected', 'connect-memberpress-discord-add-on' );
				}
				?>
			  </td>
				<?php
				break;
			case 'col_memberpress_joined_date':
				?>
					<td <?php echo esc_attr( $attributes ); ?>><?php echo esc_html( get_user_meta( $rec->ID, '_ets_memberpress_discord_join_date', true ) ); ?></td>
					<?php
				break;
			default:
				// MeprHooks::do_action( 'mepr_members_list_table_row', $attributes, $rec, $column_name, $column_display_name );
		}
	}

	/**
	 * Manage user roles api calls
	 *
	 * @param NONE
	 * @return OBJECT JSON response
	 */
	public function ets_memberpress_discord_member_table_run_api() {
		if ( ! is_user_logged_in() && current_user_can( 'edit_user' ) ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_memberpress_discord_nonce'], 'ets-memberpress-discord-ajax-nonce' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
		}
		$user_id = sanitize_text_field( $_POST['user_id'] );
		$this->ets_memberpress_discord_set_member_roles( $user_id, false, false, false );

		$event_res = array(
			'status'  => 1,
			'message' => __( 'success', 'connect-memberpress-discord-add-on' ),
		);
		return wp_send_json( $event_res );
	}

	/**
	 * Send expiration warning DM to discord members.
	 *
	 * @param INT $reminder_id
	 */

	public function ets_memberpress_discord_send_expiration_warning_dm( $reminder_id ) {
		$reminder_ctrl = new MeprRemindersCtrl();
		$reminder      = $reminder_ctrl->get_valid_reminder( $reminder_id );
		if ( $reminder->trigger_event == 'sub-expires' ) {
			if ( ( $txn = $reminder->get_next_expiring_txn() ) ) {
				$transaction = new MeprTransaction( $txn->id ); // we need the actual model
			}
			if ( isset( $transaction ) ) {
				$access_token          = get_user_meta( $transaction->user_id, '_ets_memberpress_discord_access_token', true );
				$sub_expire_membership = array(
					'product_id' => $transaction->product_id,
					'created_at' => $transaction->created_at,
					'expires_at' => $transaction->expires_at,
				);

				if ( ! empty( $access_token ) && $sub_expire_membership ) {
					as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_send_dm', array( $transaction->user_id, $sub_expire_membership, 'warning' ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
				}
			}
		}
	}

	/**
	 * Method to queue all members into cancel job when memberpress level is deleted.
	 *
	 * @param INT $level_id
	 * @return NONE
	 */
	public function ets_memberpress_discord_as_schedule_job_membership_level_deleted( $level_id ) {
		if ( get_post_type( $level_id ) == 'memberpressproduct' ) {
			global $wpdb;
			$result                         = $wpdb->get_results( $wpdb->prepare( 'SELECT `user_id`, `trans_num`, `created_at`, `expires_at` FROM ' . $wpdb->prefix . 'mepr_transactions' . ' WHERE `product_id` = %d GROUP BY `user_id`', array( $level_id ) ) );
			$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_memberpress_discord_role_mapping' ), true );
			foreach ( $result as $key => $transaction ) {
				$user_id              = $transaction->user_id;
				$access_token         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_memberpress_discord_access_token', true ) ) );
				$cancelled_membership = array();
				if ( ! empty( $transaction ) ) {
						$cancelled_membership = array(
							'product_id' => $level_id,
							'txn_number' => $transaction->trans_num,
							'created_at' => $transaction->created_at,
							'expires_at' => $transaction->expires_at,
						);
				}
				if ( $cancelled_membership && $access_token ) {
					as_schedule_single_action( ets_memberpress_discord_get_random_timestamp( ets_memberpress_discord_get_highest_last_attempt_timestamp() ), 'ets_memberpress_discord_as_handle_memberpress_cancelled', array( $user_id, $cancelled_membership ), ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME );
				}
			}
		}
	}

	/*
	Method to catch the admin BOT connect action
	* @param NONE
	* @return NONE
	*/
	public function ets_memberpress_discord_connect_bot() {
		if ( isset( $_GET['action'] ) && 'mepr-discord-connectToBot' === $_GET['action'] ) {
			if ( ! current_user_can( 'administrator' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
			}
			$params                    = array(
				'client_id'            => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) ),
				'permissions'          => ETS_MEMBERPRESS_DISCORD_BOT_PERMISSIONS,
				'response_type'        => 'code',
				'scope'                => 'bot',
				'guild_id'             => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) ),
				'disable_guild_select' => 'true',
				'redirect_uri'         => sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_auth_redirect ' ) ) ),
			);
			$discord_authorise_api_url = ETS_MEMBERPRESS_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );

			wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
			exit;
		}
	}

	/**
	 * Method to remove user from discord
	 *
	 * @param INT $user_id The User 's ID.
	 */
	public function ets_memberpress_discord_remove_user_from_server( $user_id ) {
		if ( ! is_user_logged_in() && current_user_can( 'remove_users' ) ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		if ( $user_id ) {
			$memberpress_discord = new ETS_Memberpress_Discord();
			$plugin_admin        = new ETS_Memberpress_Discord_Admin( $memberpress_discord->get_plugin_name(), $memberpress_discord->get_version() );
			$plugin_public       = new ETS_Memberpress_Discord_Public( $memberpress_discord->get_plugin_name(), $memberpress_discord->get_version(), $plugin_admin );
			$plugin_public->memberpress_delete_member_from_guild( $user_id, false );

			// delete all user_meta keys.
			ets_memberpress_discord_remove_usermeta( $user_id );
		}
	}

	public function ets_memberpress_discord_search_by_discord( $search, $perpage ) {

		if ( $_GET['page'] !== 'memberpress-members' ) {
			return;
		}
		wp_dequeue_script( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name . '-search' );
		$search_discord       = ( isset( $_GET['search-discord'] ) ) ? $_GET['search-discord'] : '';
		$search_field_discord = ( isset( $_GET['search-field-discord'] ) ) ? $_GET['search-field-discord'] : '';

		?>
		<span class="search-fields">
		<span><?php _e( 'Discord Search', 'connect-memberpress-discord-add-on' ); ?></span>
		<input id="ets-cspf-table-search" value="<?php echo $search_discord; ?>" />
		<span><?php _e( 'by Field', 'connect-memberpress-discord-add-on' ); ?></span>
		<select id="ets-cspf-table-search-field">
		  <option value="_ets_memberpress_discord_username" <?php selected( $search_field_discord, '_ets_memberpress_discord_username' ); ?>><?php _e( 'Discord User Name', 'connect-memberpress-discord-add-on' ); ?></option>
		  <option value="_ets_memberpress_discord_user_id" <?php selected( $search_field_discord, '_ets_memberpress_discord_user_id' ); ?>><?php _e( 'Discord User ID', 'connect-memberpress-discord-add-on' ); ?></option>		  
		</select>
		<input id="ets-cspf-table-search-submit" class="button" type="submit" value="<?php _e( 'Go', 'connect-memberpress-discord-add-on' ); ?>" />
		<?php
		if ( isset( $_REQUEST['search-discord'] ) || isset( $_REQUEST['search-filter-discord'] ) ) {
			$uri = $_SERVER['REQUEST_URI'];
			$uri = preg_replace( '/[\?&]search-discord=[^&]*/', '', $uri );
			$uri = preg_replace( '/[\?&]search-field-discord=[^&]*/', '', $uri );
			?>
			<a href="<?php echo $uri; ?>">[x]</a>
			<?php
		}
		?>
	  </span>
		<?php
	}

	public function ets_memberperss_add_search_filter() {
		if ( isset( $_GET['page'] ) && $_GET['page'] !== 'memberpress-members' ) {
			return;
		}

		if ( isset( $_REQUEST['search-discord'] ) || isset( $_REQUEST['search-filter-discord'] ) ) {

				add_filter(
					'mepr-list-table-joins',
					function( $joins ) {
						$search_field_discord = ( isset( $_GET['search-field-discord'] ) ) ? $_GET['search-field-discord'] : '';
							global $wpdb;
							$joins[] = " /* IMPORTANT */ LEFT JOIN {$wpdb->usermeta} AS da ON da.user_id = u.ID AND da.meta_key='" . esc_sql( $search_field_discord ) . "'";
							// $joins[] = "LEFT JOIN {$wpdb->usermeta} AS dis ON dis.user_id = u.ID AND d.meta_key='" . esc_sql( $search_field_discord ) . "'";
							return $joins;
					}
				);

				add_filter(
					'mepr-list-table-args',
					function( $args ) {
						$search_field = ( isset( $_GET['search-discord'] ) ) ? $_GET['search-discord'] : '';
							global $wpdb;
							$args[] = $wpdb->prepare( " ( da.meta_value LIKE '%" . esc_sql( $search_field ) . "%' ) " );
							return $args;
					}
				);
		}
	}
}
