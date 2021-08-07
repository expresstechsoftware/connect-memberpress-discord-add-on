<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Memberpress_Discord
 * @subpackage Memberpress_Discord/includes
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Memberpress_Discord {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Memberpress_Discord_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MEMBERPRESS_DISCORD_VERSION' ) ) {
			$this->version = MEMBERPRESS_DISCORD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'memberpress-discord';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Memberpress_Discord_Loader. Orchestrates the hooks of the plugin.
	 * - Memberpress_Discord_i18n. Defines internationalization functionality.
	 * - Memberpress_Discord_Admin. Defines all hooks for the admin area.
	 * - Memberpress_Discord_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining all methods that help to schedule actions.
		 */
		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/libraries/action-scheduler/action-scheduler.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH. 'includes/class-memberpress-discord-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'admin/class-memberpress-discord-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'public/class-memberpress-discord-public.php';

		/**
		 * The class responsible for defining all methods that occur in the public-facing and admin
		 * side of the site.
		 */
		require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/functions.php';

		$this->loader = new Memberpress_Discord_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Memberpress_Discord_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Memberpress_Discord_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Memberpress_Discord_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu', 11 );
		$this->loader->add_action( 'admin_post_memberpress_discord_general_settings', $plugin_admin, 'ets_memberpress_discord_general_settings' );
		$this->loader->add_action( 'admin_post_memberpress_discord_role_mapping', $plugin_admin, 'ets_memberpress_discord_role_mapping' );
		$this->loader->add_action( 'admin_post_memberpress_discord_advance_settings', $plugin_admin, 'ets_memberpress_discord_advance_settings' );
		$this->loader->add_action( 'wp_ajax_memberpress_load_discord_roles', $plugin_admin, 'ets_memberpress_load_discord_roles' );
		$this->loader->add_action( 'wp_ajax_memberpress_discord_clear_logs', $plugin_admin, 'ets_memberpress_discord_clear_logs' );
		$this->loader->add_action( 'mepr-transaction-expired', $plugin_admin, 'ets_memberpress_discord_as_schdule_job_memberpress_expiry', 10, 2 );
		$this->loader->add_action( 'mepr_pre_delete_transaction', $plugin_admin, 'ets_memberpress_discord_as_schdule_job_memberpress_delete_transaction' );
		$this->loader->add_action( 'mepr-event-subscription-stopped', $plugin_admin, 'ets_memberpress_discord_as_schdule_job_memberpress_cancelled' );
		$this->loader->add_action( 'mepr-event-transaction-completed', $plugin_admin, 'ets_memberpress_discord_as_schdule_job_memberpress_complete_transactions' );
		$this->loader->add_action( 'ets_memberpress_discord_as_handle_memberpress_expiry', $plugin_admin, 'ets_memberpress_discord_as_handler_memberpress_expiry', 10, 2 );
		$this->loader->add_action( 'ets_memberpress_discord_as_handle_memberpress_cancelled', $plugin_admin, 'ets_memberpress_discord_as_handler_memberpress_cancelled', 10, 2 );
		$this->loader->add_action( 'ets_memberpress_discord_as_send_dm', $this, 'ets_memberpress_discord_handler_send_dm', 10, 3 );
		$this->loader->add_action( 'ets_memberpress_discord_as_schedule_delete_role', $plugin_admin, 'ets_memberpress_discord_as_handler_delete_memberrole', 10, 3 );
		$this->loader->add_action( 'ets_memberpress_discord_as_handle_memberpress_complete_transaction', $plugin_admin, 'ets_memberpress_discord_as_handler_memberpress_complete_transaction', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Memberpress_Discord_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'mepr-account-home-fields', $plugin_public, 'ets_memberpress_discord_add_connect_button' );
		$this->loader->add_action( 'init', $plugin_public, 'ets_memberpress_discord_discord_api_callback' );
		$this->loader->add_action( 'wp_ajax_memberpress_disconnect_from_discord', $plugin_public, 'ets_memberpress_disconnect_from_discord' );
		$this->loader->add_action( 'ets_memberpress_discord_as_handle_add_member_to_guild', $plugin_public, 'ets_memberpress_discord_as_handler_add_member_to_guild', 10, 4 );
		$this->loader->add_action( 'ets_memberpress_discord_as_schedule_delete_member', $plugin_public, 'ets_memberpress_discord_as_handler_delete_member_from_guild', 10, 3 );
		$this->loader->add_action( 'ets_memberpress_discord_as_send_welcome_dm', $this, 'ets_memberpress_discord_handler_send_dm', 10, 3 );
		$this->loader->add_action( 'ets_memberpress_discord_as_schedule_member_put_role', $plugin_public, 'ets_memberpress_discord_as_handler_put_memberrole', 10, 3 );
	}

	/**
	 * Discord DM a member using bot.
	 *
	 * @param INT    $user_id
	 * @param ARRAY  $active_membership
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
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Memberpress_Discord_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
