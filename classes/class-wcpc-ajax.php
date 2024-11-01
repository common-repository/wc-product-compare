<?php
class WCPC_Ajax {

	public function __construct() {

		add_action( 'wp_ajax_wcpc_add_to_compare_action', array(&$this, 'wcpc_add_to_compare_action_callback') );
		add_action( 'wp_ajax_nopriv_wcpc_add_to_compare_action', array( &$this, 'wcpc_add_to_compare_action_callback' ) );
		add_action( 'wp_ajax_wcpc_remove_from_compare_action', array(&$this, 'wcpc_remove_from_compare_action_callback') );
		add_action( 'wp_ajax_nopriv_wcpc_remove_from_compare_action', array( &$this, 'wcpc_remove_from_compare_action_callback' ) );
        add_action( 'wp_ajax_wcpc_clear_compare_action', array(&$this, 'wcpc_clear_compare_action_callback') );
        add_action( 'wp_ajax_nopriv_wcpc_clear_compare_action', array( &$this, 'wcpc_clear_compare_action_callback' ) );
        add_action( 'wp_ajax_wcpc_show_compare_popup_data_action', array(&$this, 'wcpc_show_compare_popup_data_action_callback') );
        add_action( 'wp_ajax_nopriv_wcpc_show_compare_popup_data_action', array( &$this, 'wcpc_show_compare_popup_data_action_callback' ) );
        
	}

	public function wcpc_add_to_compare_action_callback() {
		global $WCPC, $woocommerce, $WCPC_Product;
        $return  = 'false';
        $message = '';
        $product_variation = array();
        $errors = array();
        $product_id = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;

		$product = wc_get_product( $product_id );

        if ( $product_id == false ) {
            $errors[] = __( 'Error occurred while adding product to compare.', $WCPC->text_domain );
        }
        else {
            $return = $WCPC_Product->add_to_compare( $_POST );
        }

        if ( $return == 'true' ) {
            $message = apply_filters( 'wcpc_added_to_compare_message', __( 'Product added to compare!', $WCPC->text_domain ) );
        }
        elseif ( $return == 'exists' ) {
            $message = apply_filters( 'wcpc_product_already_in_compare_list_message', __( 'Product already in compare list.', $WCPC->text_domain ) );
        }
        elseif ( count( $errors ) > 0 ) {
            $message = apply_filters( 'wcpc_error_adding_to_compare_message', $this->get_errors($errors) );
        }

        $view_compare_btn_text = apply_filters( 'wcpc_view_compare_btn_label' , __( 'View Compare', $WCPC->text_domain ) );
        if(isset($WCPC->settings['custom_view_compare_text']) && !empty($WCPC->settings['custom_view_compare_text']))
            $view_compare_btn_text = $WCPC->settings['custom_view_compare_text'];
        
        $view_compare_link='';
        if(isset($WCPC->settings['show_compare_item']) && $WCPC->settings['show_compare_item'] == 'popup'){
            $view_compare_link = '#wcpc_compare_popup';
        }else{
            $view_compare_link = $WCPC_Product->get_wcpc_compare_page_url();
        }
        wp_send_json(
            array(
                'result'       => $return,
                'message'      => $message,
                'label_view_compare' => $view_compare_btn_text,
                'wcpc_compare_page_url' => $view_compare_link,
            )
        );
    }

    public function wcpc_remove_from_compare_action_callback() {
    	global $WCPC, $woocommerce, $WCPC_Product;
        $product_id = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;
        $is_valid   = $product_id && isset( $_POST['key'] );
        if ( $is_valid ) {
            echo $WCPC_Product->remove_compare( $_POST['key'] );
        }
        else {
            echo false;
        }
        die();
    }

    public function wcpc_clear_compare_action_callback() {
        global $WCPC, $WCPC_Product;
        $WCPC_Product->clear_wcpc_compare();
        $empty_compare_list = apply_filters( 'wcpc_empty_compare_list_text_label', __('You Compare list is now Empty!', $WCPC->text_domain ) );
        wp_send_json(array('status' => true, 'empty_msg' => $empty_compare_list));
        die();
    }

    public function wcpc_show_compare_popup_data_action_callback() {
        global $WCPC, $WCPC_Product;
        $data = do_shortcode('[wcpc_compare]');
        wp_send_json(array('status' => true, 'data' => $data));
        die();
    }

}
