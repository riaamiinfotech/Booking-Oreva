<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://profiles.wordpress.org/chandrakant7389/
 * @since      1.0.0
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/includes
 * @author     Chandrakant <chandrakant7389@gmail.com>
 */
class Rise_Bulk_Discounts_For_Woocommerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if (get_option(RISE_BLD_OPTION_KEY) === false) {
            add_option(RISE_BLD_OPTION_KEY, []); // default empty array
        }
    }

}
