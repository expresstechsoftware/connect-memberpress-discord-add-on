<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.expresstechsoftwares.com
 * @since             1.0.0
 * @package           Memberpress_Discord
 *
 * @wordpress-plugin
 * Plugin Name:       MemberPress Discord
 * Plugin URI:        https://www.expresstechsoftwares.com/memberpress-discord-add-on/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            ExpressTech Softwares Solutions Pvt Ltd
 * Author URI:        https://www.expresstechsoftwares.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       memberpress-discord
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MEMBERPRESS_DISCORD_VERSION', '1.0.0' );

/**
 * Discord Api url
 */
define( 'MEMBERPRESS_DISCORD_API_URL', 'https://discord.com/api/v6/' );

/**
 * Discord Bot Permissions
 */
define( 'MEMBERPRESS_DISCORD_BOT_PERMISSIONS', 8 );

/**
 * Discord api call scopes
 */
define( 'MEMBERPRESS_DISCORD_OAUTH_SCOPES', 'identify email connections guilds guilds.join gdm.join rpc rpc.notifications.read rpc.voice.read rpc.voice.write rpc.activities.write bot webhook.incoming messages.read applications.builds.upload applications.builds.read applications.commands applications.store.update applications.entitlements activities.read activities.write relationships.read' );

/**
 * Define group name for action scheduler actions
 */
define( 'MEMBERPRESS_DISCORD_AS_GROUP_NAME', 'ets-memberpress-discord' );

/**
 * Define plugin directory path
 */
define( 'MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Define plugin directory url
 */
define( 'MEMBERPRESS_DISCORD_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

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
 * This action is documented in includes/class-memberpress-discord-activator.php
 */
function activate_memberpress_discord() {
	require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord-activator.php';
	Memberpress_Discord_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-memberpress-discord-deactivator.php
 */
function deactivate_memberpress_discord() {
	require_once MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord-deactivator.php';
	Memberpress_Discord_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_memberpress_discord' );
register_deactivation_hook( __FILE__, 'deactivate_memberpress_discord' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'includes/class-memberpress-discord.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_memberpress_discord() {

	$plugin = new Memberpress_Discord();
	$plugin->run();

}
run_memberpress_discord();
