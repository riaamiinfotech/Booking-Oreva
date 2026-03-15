<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/chandrakant7389/
 * @since             1.0.0
 * @package           Rise_Bulk_Discounts_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Rise Bulk Discounts for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/rise-bulk-discounts-for-woocommerce
 * Description:       Rise Bulk Discounts for WooCommerce lets you create quantity-based discount rules by product category, with full control over minimum/maximum quantities and child category inclusion or exclusion.
 * Version:           1.0.0
 * Author:            Chandrakant
 * Author URI:        https://profiles.wordpress.org/chandrakant7389/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rise-bulk-discounts-for-woocommerce
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
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
define( 'RISE_BULK_DISCOUNTS_FOR_WOOCOMMERCE_VERSION', '1.0.0' );

/**
 * Plugin constants (safe for existing code)
 */
define( 'RISE_BLD_PLUGIN_FILE', __FILE__ );
define( 'RISE_BLD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'RISE_BLD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RISE_BLD_OPTION_KEY', 'rise_bld_rules' );
define( 'RISE_BLD_LEGACY_PREFIX', 'rise_bld_' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rise-bulk-discounts-for-woocommerce-activator.php
 */
function rise_bld_activate_rise_bulk_discounts_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rise-bulk-discounts-for-woocommerce-activator.php';
	Rise_Bulk_Discounts_For_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rise-bulk-discounts-for-woocommerce-deactivator.php
 */
function rise_bld_deactivate_rise_bulk_discounts_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rise-bulk-discounts-for-woocommerce-deactivator.php';
	Rise_Bulk_Discounts_For_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'rise_bld_activate_rise_bulk_discounts_for_woocommerce' );
register_deactivation_hook( __FILE__, 'rise_bld_deactivate_rise_bulk_discounts_for_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rise-bulk-discounts-for-woocommerce.php';
// Include the Admin class

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function rise_bld_run_rise_bulk_discounts_for_woocommerce() {

	$plugin = new Rise_Bulk_Discounts_For_Woocommerce();
	// $plugin->run();

}
rise_bld_run_rise_bulk_discounts_for_woocommerce();
