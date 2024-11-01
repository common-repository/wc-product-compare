<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCPC_Product' ) ) :

/**
 * WCPC_Product
 */
class WCPC_Product {
	
    public $session;
    public $wcpc_compare_content = array();

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		global $WCMP_Woocommerce_Catalog_Enquiry;
		add_action( 'init', array( $this, 'wcpc_session_start' ));
        add_action( 'wp_loaded', array( $this, 'init_callback' ));
        add_action( 'wp', array( $this, 'maybe_set_wcpc_compare_cookies' ), 99 ); 
        add_action( 'shutdown', array( $this, 'maybe_set_wcpc_compare_cookies' ), 0 ); 
        add_action( 'wcce_enquiry_clean_cron', array( $this, 'clean_session'));
        add_action( 'wp_loaded', array( $this, 'add_to_compare_action' ), 30);
	}

	/**
	 * Starts the php session data for the compare.
	 */
	function wcpc_session_start(){
		if( ! isset( $_COOKIE['woocommerce_items_in_cart'] ) ) {
			do_action( 'woocommerce_set_cart_cookies', true );
		}
		$this->session = new WCPC_Session();
		$this->set_session();
	}

	function init_callback() {
        $this->get_wcpc_compare_session();
        $this->session->set_customer_session_cookie(true);
        $this->wcpc_session_validation_schedule();
    }

    function get_wcpc_compare_session() {
        $this->wcpc_compare_content = $this->session->get( 'wcpc_compare', array() );
        return $this->wcpc_compare_content;
    }

    public function wcpc_session_validation_schedule(){

        if( ! wp_next_scheduled( 'wcpc_session_validation_schedule' ) ){
            $ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
            wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS'), 'daily', 'wcpc_session_validation_schedule' );
        }

        if ( !wp_next_scheduled( 'wcpc_session_clean_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'wcpc_session_clean_cron' );
        }
    }

    public function clean_session(){
        global $wpdb;
        $query = $wpdb->query("DELETE FROM ". $wpdb->prefix ."options  WHERE option_name LIKE '_wcpc_session_%'");
    }


	/**
	 * Sets the php session data for the enquiry cart.
	 */
	public function set_session($wcpc_compare_session = array(), $can_be_empty = false) {

		if ( empty( $wcpc_compare_session ) && !$can_be_empty) {
            $wcpc_compare_session = $this->get_wcpc_compare_session();
        }
        // Set wcpc_compare  session data
        $this->session->set( 'wcpc_compare', $wcpc_compare_session );
	}

	public function unset_session() {
        $this->session->__unset( 'wcpc_compare' );
    }

	function maybe_set_wcpc_compare_cookies() {
        $set = true;

        if ( !headers_sent() ) {
            if ( sizeof( $this->wcpc_compare_content ) > 0 ) {
                $this->set_wcpc_compare_cookies( true );
                $set = true;
            }
            elseif ( isset( $_COOKIE['wcpc_compare_items'] ) ) {
                $this->set_wcpc_compare_cookies( false );
                $set = false;
            }
        }

        do_action( 'wcpc_compare_session_cookies', $set );
    }

    private function set_wcpc_compare_cookies( $set = true ) {
        if ( $set ) {
            wc_setcookie( 'wcpc_compare_items', 1 );
            wc_setcookie( 'wcpc_session_hash', md5( json_encode( $this->wcpc_compare_content ) ) );
        }
        elseif ( isset( $_COOKIE['wcpc_compare_items'] ) ) {
            wc_setcookie( 'wcpc_compare_items', 0, time() - HOUR_IN_SECONDS );
            wc_setcookie( 'wcpc_session_hash', '', time() - HOUR_IN_SECONDS );
        }
    }

	public function add_to_compare_action() {
		global $WCPC;
	    if ( empty( $_REQUEST['add-to-compare'] ) || ! is_numeric( $_REQUEST['add-to-compare'] ) ) {
		    return;
	    }

	    $product_id      = apply_filters( 'wc_add_to_compare_product_id', absint( $_REQUEST['add-to-compare'] ) );
	    $adding_to_compare = wc_get_product( $product_id );
	    $variation_id    = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
	    $quantity        = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
	    $variations      = array();
	    $error           = false;

	    $add_to_compare_handler = apply_filters( 'wc_add_to_compare_handler', $adding_to_compare->product_type, $adding_to_compare );

	    if ( 'variation' === $add_to_compare_handler ) {
		    if ( isset( $adding_to_compare->variation_id ) ) {
			    $product_id   = $adding_to_compare->id;
			    $variation_id = $adding_to_compare->variation_id;
		    }
	    }
	    if ( 'variable' === $add_to_compare_handler ) {
		    if ( empty( $variation_id ) ) {
			    $variation_id = $adding_to_compare->get_matching_variation( wp_unslash( $_POST ) );
		    }
		    if ( ! empty( $variation_id ) ) {
			    $attributes = $adding_to_compare->get_attributes();
			    $variation  = wc_get_product( $variation_id );
			    foreach ( $attributes as $attribute ) {
				    if ( ! $attribute['is_variation'] ) {
					    continue;
				    }
				    $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );
				    if ( isset( $_REQUEST[ $taxonomy ] ) ) {
					    if ( $attribute['is_taxonomy'] ) {
						    $value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
					    } else {
						    $value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
					    }
					    $valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';
					    // Allow if valid
					    if ( '' === $valid_value || $valid_value === $value ) {
						    $variations[ $taxonomy ] = $value;
						    continue;
					    }

				    } else {
					    $missing_attributes[] = wc_attribute_label( $attribute['name'] );
				    }
			    }

			    if ( ! empty( $missing_attributes ) ) {
				    $error = true;
				    wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'WC_Product_Compare' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
			    }
		    } elseif ( empty( $variation_id ) ) {
			    $error = true;
			    wc_add_notice( __( 'Please choose product options&hellip;', 'WC_Product_Compare' ), 'error' );
		    }
	    }

	    if ( $error ) {
		    return;
	    }

	    $compare_data = array(
		    'product_id'   => $product_id,
		    'variation_id' => $variation_id,
		    'quantity'     => $quantity,
		    'variation'    => $variations
	    );

	    $return = $this->add_to_compare( $compare_data );

	    if ( $return == 'true' ) {
		    $message = apply_filters( 'wcpc_compare_product_added_to_list_message', __( 'Product added to compare!', $WCPC->text_domain ));
		    wc_add_notice( $message, 'success' );
	    } elseif ( $return == 'exists' ) {
		    $message = apply_filters( 'wcce_enquiry_product_already_in_list_message', __( 'Product already in compare.', $WCPC->text_domain ) );
		    wc_add_notice( $message, 'notice' );
	    }
    }

    public function add_to_compare( $compare_data ) {

        $compare_data['quantity'] = ( isset( $compare_data['quantity'] ) ) ? (int) $compare_data['quantity'] : 1;
        $return = '';
        if ( !isset( $compare_data['variations'] ) ) {
            // simple product
            if ( !$this->exists_in_compare( $compare_data['product_id'] ) ) {
                $compare = array(
                    'product_id' => $compare_data['product_id'],
                    'quantity'   => $compare_data['quantity']
                );
                $this->wcpc_compare_content[md5( $compare_data['product_id'] )] = $compare;
            }
            else {
                $return = 'exists';
            }
        }
        else {
            //variable product
            if ( !$this->exists_in_compare( $compare_data['product_id'] ) ) {
                $compare = array(
                    'product_id'   => $compare_data['product_id'],
                    'variations'   => $compare_data['variations'],
                    'quantity'     => $compare_data['quantity']
                );

                $this->wcpc_compare_content[md5( $compare_data['product_id'] . $compare_data['variation_id'] )] = $compare;
            }
            else {
                $return = 'exists';
            }
        }

        if ( $return != 'exists' ) {
            $this->set_session( $this->wcpc_compare_content );
            $return = 'true';
            $this->set_wcpc_compare_cookies( sizeof( $this->wcpc_compare_content ) > 0 );
        }
        return $return;
    }

    public function exists_in_compare( $product_id, $variation_id = false ) {
    	global $WCPC;
        if ( $variation_id ) {
            $key_to_find = md5( $product_id . $variation_id );
        } else {
            $key_to_find = md5( $product_id );
        }
        if ( array_key_exists( $key_to_find, $this->wcpc_compare_content ) ) {
            $this->errors[] = __( 'Product already in compare.', $WCPC->text_domain );
            return true;
        }
        return false;
    }

    public function get_compare_data() {
        return $this->wcpc_compare_content;
    }

    public function get_wcpc_compare_page_url() {
        $wcpc_compare_page_id = get_option( 'wcpc_compare_page_id' );
        $base_url     = get_the_permalink( $wcpc_compare_page_id );

        return apply_filters( 'wcpc_compare_page_url', $base_url );
    }

    public function is_empty_compare() {
        return empty( $this->wcpc_compare_content );
    }

    public function remove_compare( $key ) {

        if ( isset( $this->wcpc_compare_content[$key] ) ) {
            unset( $this->wcpc_compare_content[$key] );
            $this->set_session( $this->wcpc_compare_content, true );
            return true;
        }
        else {
            return false;
        }
    }

    public function clear_wcpc_compare() {
        $this->wcpc_compare_content = array();
        $this->set_session( $this->wcpc_compare_content, true );
    }

    public function update_compare( $key, $field = false, $value ) {
        if ( $field && isset( $this->wcpc_compare_content[$key][$field] ) ) {
            $this->wcpc_compare_content[$key][$field] = $value;
            $this->set_session( $this->wcpc_compare_content );
        }
        elseif ( isset( $this->wcpc_compare_content[$key] ) ) {
            $this->wcpc_compare_content[$key] = $value;
            $this->set_session( $this->wcpc_compare_content );
        }
        else {
            return false;
        }
        $this->set_session( $this->wcpc_compare_content );
        return true;
    }
}

endif;