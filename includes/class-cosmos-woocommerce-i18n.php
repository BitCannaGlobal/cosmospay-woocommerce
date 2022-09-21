<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://twitter.com/BitCannaGlobal
 * @since      1.0.0
 *
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocomerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/includes
 * @author     BitCanna <dev@bitcanna.io>
 */
class Cosmos_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cosmos-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
