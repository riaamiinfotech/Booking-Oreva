<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://https://profiles.wordpress.org/chandrakant7389/
 * @since      1.0.0
 *
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Rise_Bulk_Discounts_For_Woocommerce
 * @subpackage Rise_Bulk_Discounts_For_Woocommerce/includes
 * @author     Chandrakant <chandrakant7389@gmail.com>
 */
class Rise_Bulk_Discounts_For_Woocommerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		do_action('rise_bld_deactivated'); 
	}

}
