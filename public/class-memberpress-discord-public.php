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
		if ( $this->Check_saved_settings_status() ) {
			if ( $access_token ) {
				?>
				<label class="ets-connection-lbl"><?php echo __( 'Discord connection', 'ets_memberpress_discord' ); ?></label>
				<a href="#" class="ets-btn btn-disconnect" id="disconnect-discord" data-user-id="<?php echo $user_id; ?>"><?php echo __( 'Disconnect From Discord ', 'ets_memberpress_discord' ); ?><i class='fab fa-discord'></i></a>
				<span class="ets-spinner"></span>
				<?php
			} elseif ( current_user_can('memberpress_authorized') || $allow_none_member == 'yes' ) {
				?>
				<label class="ets-connection-lbl"><?php echo __( 'Discord connection', 'ets_memberpress_discord' ); ?></label>
				<a href="?action=discord-login" class="btn-connect ets-btn" ><?php echo __( 'Connect To Discord', 'ets_memberpress_discord' ); ?> <i class='fab fa-discord'></i></a>
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
	 * To check settings values saved or not
	 *
	 * @param NONE
	 * @return BOOL $status
	 */
	public function Check_saved_settings_status() {
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
	
}
