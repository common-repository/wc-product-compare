<?php
 
class WCPC_Compare_Data_Shortcode {

	public function __construct() {

	}

	/**
	 * Output WCPC Compare Data.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function compare_content( $attr ) {
		global $WCPC,$WCPC_Product;
		$WCPC->nocache();
		// CSS
		$frontend_style_path = $WCPC->plugin_url . 'assets/frontend/css/';
		$frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
		wp_enqueue_style('wcpc_compare_page_css', $frontend_style_path . 'wcpc_compare_page.css', array(), $WCPC->version);
		wp_enqueue_style('wcpc_owl_carousel_css', $frontend_style_path . 'owl.carousel.min.css', array(), $WCPC->version);
		
		// JS
		$frontend_script_path = $WCPC->plugin_url . 'assets/frontend/js/';
		$frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
		wp_enqueue_script('wcpc_matchHeight_js', $frontend_script_path.'jquery.matchHeight-min.js', array('jquery'), $WCPC->version, false);
		wp_enqueue_script('wcpc_compare_page_js', $frontend_script_path.'wcpc_compare_page.js', array('jquery'), $WCPC->version, false);
		wp_enqueue_script('wcpc_owl_carousel_js', $frontend_script_path.'owl.carousel.min.js', array('jquery'), $WCPC->version, false);
		
		$WCPC->template->get_template( 'shortcode/wcpc_compare_data.php'); 

	}
}