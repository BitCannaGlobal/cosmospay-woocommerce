<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://twitter.com/BitCannaGlobal
 * @since      1.0.0
 *
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/includes
 * @author     BitCanna <dev@bitcanna.io>
 */
class Cosmos_Woocommerce_Deactivator {

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
