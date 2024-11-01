<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WCPC_Shortcode {

	public function __construct() {
		// WCCE Enquiry cart
		add_shortcode('wcpc_compare',array(&$this,'wcpc_compare_data_content'));
	}
	
	/**
	 *
	 * @return void
	 */
	public function wcpc_compare_data_content($attr) {
		global $WCPC;
		$this->load_class('data');
		return $this->shortcode_wrapper(array('WCPC_Compare_Data_Shortcode', 'compare_content'));
	}
	
	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	
	public function load_class($class_name = '') {
		global $WCPC;
		if ('' != $class_name && '' != $WCPC->token) {
			require_once ('shortcode/class-' . esc_attr($WCPC->token) . '-shortcode-' . esc_attr($class_name) . '.php');
		}
	}

}
?>