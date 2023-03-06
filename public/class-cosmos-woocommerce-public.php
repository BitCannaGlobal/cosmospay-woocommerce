<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://twitter.com/BitCannaGlobal
 * @since      1.0.0
 *
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cosmos_Woocommerce
 * @subpackage Cosmos_Woocommerce/public
 * @author     BitCanna <dev@bitcanna.io>
 */
class Cosmos_Woocommerce_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cosmos-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($order_id) {

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
    global $woocommerce;    
    
    $selected_payment_method_id = $woocommerce->session->get( 'chosen_payment_method' );
    $selected_payment_method = $woocommerce->payment_gateways->payment_gateways()[ $selected_payment_method_id ];  
 
    if($selected_payment_method->title === 'Cosmos Pay') {
      if ( is_checkout() ) {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bundle.js', array( 'jquery' ), $this->version, 'all' );
        wp_enqueue_script( $this->plugin_name . '_mainscript', plugin_dir_url( __FILE__ ) . 'js/mainscript.js', array( 'jquery' ), $this->version, true );
      }
    }

	}
	/**
	 * Start cosmos pay form
	 *
	 * @since    1.0.0
	 * @param    string    $order_id       ID of order
	 */
  public function cosmos_pay_form( $order_id ) {
    global $woocommerce;

    
    
    $order = wc_get_order( $order_id );  
    $userWp = wp_get_current_user( );
    $userWoo = $order->user_id;
    $orderData = $order->get_data( ); 
    $order_status  = $order->get_status( );
    
    
    if($order->payment_method_title === 'Cosmos Pay') {
      $getMemo = wc_get_order_item_meta( $order_id , '_cosmos_memo', true ); 

      if ($order_status !== 'cancelled') {
        $order->update_status( 'pending', __( 'Awaiting Cosmos payment', 'wc-gateway-offline' ) );
      }
      
      if ( empty( $getMemo ) ) {
        // Mark as pending (we're awaiting the payment)
        // $order->update_status( 'pending', __( 'Awaiting Cosmos payment', 'wc-gateway-offline' ) ); 
        // Remove cart
        WC()->cart->empty_cart();
        // Generate Memo
        $memoBc = $this->generateRandomString();
        wc_update_order_item_meta( $order_id, '_cosmos_memo', esc_attr( $memoBc ) );
        $note = 'Memo created: '.esc_attr( $memoBc );
        $order->add_order_note( $note , true );
        $order->save(); 
      }
  
      if ( $orderData["status"] === 'completed' ) {
        if ( is_page( 'checkout' ) ) {
          wp_enqueue_script( 'jquery' );
          wp_enqueue_script(
            'finalBitcannaJs',
            plugin_dir_url(__FILE__).'js/finalBitcanna.js'
          );
        }
      } else { 
        
        $selected_payment_method_id = $woocommerce->session->get( 'chosen_payment_method' );
        $selected_payment_method = $woocommerce->payment_gateways->payment_gateways()[ $selected_payment_method_id ];       
        $configMake = get_option("cosmos_pay_config_approved");
  
        if ($woocommerce->payment_gateways->payment_gateways()[ 'woo-cosmos' ]->enabled === 'yes') {     
          if ($configMake === 'false' || $configMake === false || empty($selected_payment_method->settings['option_name'])) {
            include plugin_dir_path(__FILE__) . "partials/cosmos-payment-config-tpl.php";
          } else {
            if ( $order_status !== 'cancelled' ) {
              include plugin_dir_path(__FILE__) . "partials/cosmos-payment-tpl.php";
            } else
              include plugin_dir_path(__FILE__) . "partials/cosmos-payment-cancel-tpl.php";      
          }
        }
      }    
    }
        

  }
	/**
	 * Generate function for random memo
	 *
	 * @since    1.0.0
	 * @param    string    $length       Length of memo
	 */  
  public function generateRandomString( $length = 10 ) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen( $characters );
    $randomString = '';
    for ( $i = 0; $i < $length; $i++ ) {
      $randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
      }
    return $randomString;
  }    
}
