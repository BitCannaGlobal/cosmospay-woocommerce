<?php

/**
 * Fired during plugin activation
 *
 * @link       https://twitter.com/atmon3r
 * @since      1.0.0
 *
 * @package    Cosmos_Woocomerce
 * @subpackage Cosmos_Woocomerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cosmos_Woocomerce
 * @subpackage Cosmos_Woocomerce/includes
 * @author     atmon3r <contact.atmoner@gmail.com>
 */
class Cosmos_Woocomerce_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
    global $wpdb;
    add_option( 'cosmos_pay_disclaimer_approved', 'false', '', 'yes' );
    // wp_redirect(admin_url('admin.php?page=cosmos-woocomerce-settings'));
    
    if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'api-cosmos'", 'ARRAY_A' ) ) {
      // Create post object
      $my_post = array(
        'post_title'    => wp_strip_all_tags( 'api-cosmos' ),
        'post_content'  => 'My custom page content',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'page',
      );

      // Insert the post into the database
      $postId = wp_insert_post( $my_post );
      add_option( 'cosmos_api_post_id', $postId, '', 'yes' );   
    }
	}

}
