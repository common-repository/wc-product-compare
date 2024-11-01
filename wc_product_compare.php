<?php
/**
Plugin Name: WC Product Compare
Plugin URI: 
Description: Compare WooCommerce products to get the best one.
Author: itzmekhokan
Version: 1.0.1
Requires at least: 4.4
Tested up to: 5.2
WC requires at least: 3.0
WC tested up to: 3.6.3
Author URI: https://itzmekhokan.wordpress.com/
*/

if ( ! class_exists( 'WCPC_Dependencies' ) )
require_once trailingslashit(dirname(__FILE__)).'includes/class-wcpc-dependencies.php';
require_once trailingslashit(dirname(__FILE__)).'includes/wcpc-core-functions.php';
require_once trailingslashit(dirname(__FILE__)).'wcpc_config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WCPC_PLUGIN_TOKEN')) exit;
if(!defined('WCPC_TEXT_DOMAIN')) exit;
if(!WCPC_Dependencies::woocommerce_active_check()) {
  add_action( 'admin_notices', 'woocommerce_required_alert_notice' );
}

/**
* Plugin page links
*/
function wcpc_plugin_links( $links ) {	
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wcpc-setting-admin' ) . '">' . __( 'Settings', WCPC_TEXT_DOMAIN ) . '</a>',
		'<a href="https://wc-marketplace.com/support-forum/forum/wcmp-catalog-enquiry/">' . __( 'Support', WCPC_TEXT_DOMAIN ) . '</a>',			
	);	
	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wcpc_plugin_links' );

if(WCPC_Dependencies::woocommerce_active_check()) {
	require_once( trailingslashit(dirname(__FILE__)).'classes/class-wcpc.php' );
	global $WCPC;
	$WCPC = new WCPC( __FILE__ );
	$GLOBALS['WCPC'] = $WCPC;

	// Activation Hooks
	register_activation_hook( __FILE__, array('WCPC', 'activate_wcmp_Woocommerce_Catalog_Enquiry') );
	register_activation_hook( __FILE__, 'flush_rewrite_rules' );
		
	// Deactivation Hooks
	register_deactivation_hook( __FILE__, array('WCPC', 'deactivate_wcmp_Woocommerce_Catalog_Enquiry') );

	add_action( 'plugins_loaded', 'wcpc_session_init' );
}

function wcpc_session_init(){

	require_once trailingslashit(dirname(__FILE__)).'includes/class-wcpc-session.php';
    require_once trailingslashit(dirname(__FILE__)).'includes/class-wcpc-product.php';
    global $WCPC_Product;
	$WCPC_Product = new WCPC_Product();
	$GLOBALS['WCPC_Product'] = $WCPC_Product;

}
?>