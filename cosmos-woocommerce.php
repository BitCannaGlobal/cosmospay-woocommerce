<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://twitter.com/bitcannaglobal
 * @since             1.0.0
 * @package           Cosmos_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Cosmos Pay
 * Plugin URI:        https://twitter.com/bitcannaglobal
 * Description:       Easily accept cryptocurrency payments on your WordPress site. Enable multiple currencies from the interconnected Cosmos ecosystem.
 * Version:           1.0.22
 * Author:            BitCanna
 * Author URI:        https://commerce.bitcanna.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       Cosmos Pay
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
define( 'COSMOS_WOOCOMMERCE_VERSION', '1.0.22' );



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cosmos-woocommerce-activator.php
 */
function activate_cosmos_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cosmos-woocommerce-activator.php';
	Cosmos_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cosmos-woocommerce-deactivator.php
 */
function deactivate_cosmos_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cosmos-woocommerce-deactivator.php';
	Cosmos_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cosmos_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_cosmos_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cosmos-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cosmos_woocommerce() {

	$plugin = new Cosmos_Woocommerce();
	$plugin->run();

}
run_cosmos_woocommerce();
