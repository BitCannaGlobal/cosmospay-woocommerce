<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://twitter.com/BitCannaGlobal
 * @since      1.0.0
 *
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/admin
 * @author     BitCanna <dev@bitcanna.io>
 */
class Cosmos_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cosmos_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cosmos_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cosmos-woocommerce-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cosmos_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cosmos_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cosmos-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

	}

    function wc_offline_cosmos_init() {    
      if (defined( 'WC_VERSION' )) {
        include( 'class-wc-gateway-cosmos.php' );
      }  
    }
    public function add_cosmos_gateway_class( $methods ) {        
      if ( defined( 'WC_VERSION' ) ) {
        $methods[] = 'WC_Gateway_Cosmos';
        return $methods;
      }  
    }

    function load_cosmos_api( $template ) {
      if( is_page( 'api-cosmos' ) )
        $template = plugin_dir_path(__FILE__) . "../api-cosmos.php";
      return $template;
    }

    public function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen( $characters );
      $randomString = '';
      for ( $i = 0; $i < $length; $i++ ) {
        $randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
        }
      return $randomString;
    }   
    function cosmos_admin_menu() {
      add_menu_page( 
        "Cosmos Pay", 
        "Cosmos Pay", 
        'manage_options', 
        $this->plugin_name . '-settings', 
        array( $this, 'get_cosmos_settings' ), 
        'dashicons-money-alt'
      );   
    }
    function get_cosmos_settings(){
      global $woocommerce; 
      $selected_payment_method = $woocommerce->payment_gateways->payment_gateways()["woo-cosmos"];
      
      include( plugin_dir_path( __FILE__ ) . 'partials/cosmos-settings.php' );    
    }	
    function prefix_append_support_and_faq_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
        $configDisclaimer = get_option( "cosmos_pay_disclaimer_approved" );
        if ( $plugin_file_name === $this->plugin_name . '/cosmos-woocommerce.php' ) { 
            $links_array[] = '<a href="#">FAQ</a>';
            $links_array[] = '<a href="#">Support</a>';
            if ( $configDisclaimer === 'false' ) {
              $links_array[] = '<a href="/wp-admin/admin.php?page=' . $this->plugin_name . '-settings"><font color="red">Accept disclaimer</font></a>';
            }  
            if ( !defined( 'WC_VERSION' ) ) {
                // no woocommerce :(
                $links_array[] = '<font color="red">WooCommerce is not installed!</font></a>';
            }           
        }   
        return $links_array;
    }
 
}
