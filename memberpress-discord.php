<?php
/**
 * Memberpress discord add-on: Allow memberpress users to connect their site to discord and allow them to
 * be part of your discord community, site admin can allow discord roles based on the membership a member have and
 * can sell private content in role specific discord channels.
 *
 * @link              https://www.expresstechsoftwares.com
 * @since             1.0.0
 * @package           ETS_Memberpress_Discord
 *
 * @wordpress-plugin
 * Plugin Name:       Connect MemberPress To Discord
 * Plugin URI:        https://www.expresstechsoftwares.com/memberpress-discord-add-on/
 * Description:       Allow memberpress users to connect their site to discord and allow them to be part of your discord community, site admin can allow discord roles based on the membership a member have and can sell private content in role specific discord channels.
 * Version:           1.0.26
 * Author:            ExpressTech Softwares Solutions Pvt Ltd
 * Author URI:        https://www.expresstechsoftwares.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connect-memberpress-discord-add-on
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'ETS_MEMBERPRESS_DISCORD_VERSION', '1.0.26' );

/**
 * Discord API URL
 */
define( 'ETS_MEMBERPRESS_DISCORD_API_URL', 'https://discord.com/api/v10/' );

/**
 * Discord BOT Permissions
 */
define( 'ETS_MEMBERPRESS_DISCORD_BOT_PERMISSIONS', 8 );

/**
 * Discord API call scopes
 */
define( 'ETS_MEMBERPRESS_DISCORD_OAUTH_SCOPES', 'identify email guilds guilds.join' );

/**
 * Define group name for action scheduler actions
 */
define( 'ETS_MEMBERPRESS_DISCORD_AS_GROUP_NAME', 'ets-memberpress-discord' );

/**
 * Define plugin directory path
 */
define( 'ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Define plugin directory URL
 */
define( 'ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Follwing response codes not cosider for re-try API calls.
 */
define( 'ETS_MEMBERPRESS_DISCORD_DONOT_RETRY_THESE_API_CODES', array( 0, 10003, 50033, 10004, 50025, 10013, 10011 ) );

/**
 * Define plugin directory url
 */
define( 'ETS_MEMBERPRESS_DISCORD_DONOT_RETRY_HTTP_CODES', array( 400, 401, 403, 404, 405, 502 ) );

/**
 * The code that runs during plugin activation.
 */
function activate_memberpress_discord() {
	require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord-activator.php';
	ETS_Memberpress_Discord_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_memberpress_discord() {
	require_once ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord-deactivator.php';
	ETS_Memberpress_Discord_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_memberpress_discord' );
register_deactivation_hook( __FILE__, 'deactivate_memberpress_discord' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_memberpress_discord() {

	$plugin = new ETS_Memberpress_Discord();
	$plugin->run();

}
run_memberpress_discord();
