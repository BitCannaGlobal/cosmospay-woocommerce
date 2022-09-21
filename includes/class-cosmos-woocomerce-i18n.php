<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://twitter.com/atmon3r
 * @since      1.0.0
 *
 * @package    Cosmos_Woocomerce
 * @subpackage Cosmos_Woocomerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cosmos_Woocomerce
 * @subpackage Cosmos_Woocomerce/includes
 * @author     atmon3r <contact.atmoner@gmail.com>
 */
class Cosmos_Woocomerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cosmos-woocomerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
