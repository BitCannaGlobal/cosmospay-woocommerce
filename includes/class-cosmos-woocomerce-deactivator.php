<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://twitter.com/atmon3r
 * @since      1.0.0
 *
 * @package    Cosmos_Woocomerce
 * @subpackage Cosmos_Woocomerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cosmos_Woocomerce
 * @subpackage Cosmos_Woocomerce/includes
 * @author     atmon3r <contact.atmoner@gmail.com>
 */
class Cosmos_Woocomerce_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
    delete_option( 'cosmos_pay_disclaimer_approved' );
    //delete_option( 'woocommerce_woo-cosmos_settings'); 
	}

}
