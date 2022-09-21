<?php

class WC_Gateway_Cosmos extends WC_Gateway_COD {

    /**
     * Setup general properties for the gateway.
     */
    protected function setup_properties() {
        $this->id                 = 'woo-cosmos';
        $this->icon               = apply_filters( 'woocommerce_coupon-on-deliver_icon', '' );
        $this->method_title       = __( 'Cosmos payment', 'woo-cosmos' );
        $this->method_description = __( 'Have your customers pay with a cosmos wallet. Change address <a href="/wp-admin/admin.php?page=cosmos-woocomerce-settings">here</a>', 'woo-cosmos' );
        $this->has_fields         = false;
        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();     
 
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {
        // Select chain part
        $countChain = 0;
        
        // Call configuration file from our server
        // $string = file_get_contents( "https://store-api.bitcanna.io" );
        $string = wp_remote_retrieve_body( wp_remote_get( 'https://store-api.bitcanna.io' ) );

        if (empty($string)) {
          wp_die('Unable to call configuration', 'Error');
        }
        // Decode configuration file from our server
        $json_a = json_decode($string, true);
        if ($json_a === null) {
          wp_die('Error in json configuration', 'Error');
        }   
        foreach ($json_a as $chains_data => $chain) {
          if ($chain['active'] === 'true') {
            $shipping_methods[$countChain++] = $chain['name'];
          }          
        }  

        /*foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
            $shipping_methods[ $method->id ] = $method->get_method_title();
        }*/

        $this->form_fields = array(
            'enabled' => array(
                'title'       => __( 'Enable/Disable', 'woo-cosmos' ),
                'label'       => __( 'Enable cosmos payment', 'woo-cosmos' ),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no',
            ),
            'title' => array(
                'title'       => __( 'Title', 'woo-cosmos' ),
                'type'        => 'text',
                'description' => __( 'Cosmos wallets', 'woo-cosmos' ),
                'default'     => __( 'Cosmos walletss', 'woo-cosmos' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', 'woo-cosmos' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your website.', 'woo-cosmos' ),
                'default'     => __( 'Pay with your wallets cosmos!', 'woo-cosmos' ),
                'desc_tip'    => true,
            ), 
            'option_name' => array(
                'title'             => __( 'Enable cosmos chains', 'woo-cosmos' ),
                'type'              => 'multiselect',
                'class'             => 'wc-enhanced-select',
                'css'               => 'width: 400px;',
                'default'           => '',
                'description'       => __( 'Select the cosmos chains that will be available for your customers.', 'woo-cosmos' ),
                'options'           => $shipping_methods,
                'desc_tip'          => true,
                'custom_attributes' => array(
                    'data-placeholder' => __( 'Select chain accepted', 'woo-cosmos' ),
                ),
            ),         
       );
        // Add input part
        foreach ($json_a as $chains_data => $chain) {
          if ($chain['active'] === 'true') {
            $title = $chain['name'];
            $this->form_fields[$chain['name']] = array( 
                  'title'       => __( 'Your adrress ' . $title, 'woo-cosmos' ),
                  'type'        => 'text',
                  'description' => __( 'Cosmos wallets', 'woo-cosmos' ),
                  'default'     => __( '', 'woo-cosmos' ),
                  'desc_tip'    => true,
                  'custom_attributes' => array('readonly' => 'readonly'),

            );         
            array_push($this->form_fields[$chain['name']], $this->form_fields[$chain['name']]);
          }
        }
        
    }			
} 
