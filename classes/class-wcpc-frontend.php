<?php
class WCPC_Frontend {
	
	public function __construct() {
		global $WCPC;
		add_action( 'WCPC_frontend_hook', array($this, 'WCPC_frontend_function'), 10, 2 );
		
		if(isset($WCPC->settings['is_enable_compare']) && $WCPC->settings['is_enable_compare'] == 'Enable'){
			add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'frontend_styles'));	
			$this->init_wcpc_compare();
		}			
	}

	function init_wcpc_compare(){
		global $WCPC;
		add_action('woocommerce_after_shop_loop_item',array($this, 'add_wcpc_compare_btn') );
		if(isset($WCPC->settings['is_enable_compare_single_page']) && $WCPC->settings['is_enable_compare_single_page'] == 'Enable')
			add_action('woocommerce_single_product_summary',array(&$this, 'add_wcpc_compare_btn'), 30 );

		if(isset($WCPC->settings['show_compare_item']) && $WCPC->settings['show_compare_item'] == 'popup')
			add_action( 'wp_footer', array($this, 'wcpc_compare_popup_wp_footer')); 
	}

	public function add_wcpc_compare_btn() {
		global $product, $WCPC, $post, $WCPC_Product;
		$add_compare_btn_text = apply_filters( 'wcpc_add_compare_btn_label' , __( 'Compare', $WCPC->text_domain ) );
		if(isset($WCPC->settings['custom_add_compare_text']) && !empty($WCPC->settings['custom_add_compare_text']))
			$add_compare_btn_text = $WCPC->settings['custom_add_compare_text'];
	
		$view_compare_btn_text = apply_filters( 'wcpc_view_compare_btn_label' , __( 'View Compare', $WCPC->text_domain ) );
		if(isset($WCPC->settings['custom_view_compare_text']) && !empty($WCPC->settings['custom_view_compare_text']))
			$view_compare_btn_text = $WCPC->settings['custom_view_compare_text'];

		$args         = array(
            'class'         	=> 'button',
            'wpnonce'       	=> wp_create_nonce( 'add-to-compare-' . $product->get_id() ),
            'product_id'    	=> $product->get_id(),
            'label'         	=> $add_compare_btn_text,
            'label_view_compare'=> $view_compare_btn_text,
            'wcpc_compare_url' 	=> $WCPC_Product->get_wcpc_compare_page_url(),
            'exists'        	=> $WCPC_Product->exists_in_compare( $product->get_id() )
        );
        $args['args'] = $args;
		$WCPC->template->get_template('wcpc-compare-button.php',$args);
	}


	function wcpc_compare_popup_wp_footer() { 
		global $WCPC;

		echo '<div id="wcpc_compare_popup" class="wcpc-modal" style="opacity:0">
			<div class="modal-content">
	    		<div class="modal-header">
	      			<span class="close">&times;</span>
	      			<h3>'.apply_filters( 'wcpc_compare_popup_heading_label' , __( 'WC Product Compare', $WCPC->text_domain ) ).'</h3>
	    		</div>
	    		<div class="modal-body">
	      			'.do_shortcode('[wcpc_compare]').'
	    		</div>
    		</div>
		</div>';
	}
	

	function frontend_scripts() {
		global $WCPC;
		$frontend_script_path = $WCPC->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		$pluginURL = str_replace( array( 'http:', 'https:' ), '', $WCPC->plugin_url );
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script('wcpc_frontend_js', $frontend_script_path.'frontend.js', array('jquery'), $WCPC->version, true);
		wp_localize_script(
			'wcpc_frontend_js', 
			'wcpc_frontend',	
			array(
				'ajaxurl' => admin_url('admin-ajax.php')
			));
	    wp_enqueue_script('wcpc_compare_js', $frontend_script_path.'add-to-compare.js', array('jquery'), $WCPC->version, true);
	    wp_localize_script(
			'wcpc_compare_js', 
			'wcpc_compare',	
			array(
				'ajaxurl' => admin_url('admin-ajax.php')
			));
	}

	function frontend_styles() {
		global $WCPC;

		$frontend_style_path = $WCPC->plugin_url . 'assets/frontend/css/';
		$frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
		$suffix 				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style('wcpc_frontend_css',  $frontend_style_path.'frontend.css', array(), $WCPC->version);


	}
	
	function WCPC_frontend_function() {
	  // Do your frontend work here
	  
	}

}
