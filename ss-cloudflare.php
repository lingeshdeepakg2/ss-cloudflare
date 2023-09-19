<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://spotlightstudios.co.uk/
 * @since             1.0.0
 * @package           Ss_Cloudflare
 *
 * @wordpress-plugin
 * Plugin Name:       SS Cloudflare
 * Plugin URI:        https://https://spotlightstudios.co.uk/
 * Description:       SS Cloudflare plugin
 * Version:           1.0.0
 * Author:            Spotlight
 * Author URI:        https://https://spotlightstudios.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ss-cloudflare
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
define( 'SS_CLOUDFLARE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ss-cloudflare-activator.php
 */
function activate_ss_cloudflare() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ss-cloudflare-activator.php';
	Ss_Cloudflare_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ss-cloudflare-deactivator.php
 */
function deactivate_ss_cloudflare() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ss-cloudflare-deactivator.php';
	Ss_Cloudflare_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ss_cloudflare' );
register_deactivation_hook( __FILE__, 'deactivate_ss_cloudflare' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ss-cloudflare.php';

/**
 * Include config file 
 * Include curl file
 */
require plugin_dir_path( __FILE__ ) . 'includes/config.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ss_cloudflare() {

	$plugin = new Ss_Cloudflare();
	$plugin->run();

}
run_ss_cloudflare();
