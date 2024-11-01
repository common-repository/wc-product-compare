<?php
class WCPC {

	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	public $shortcode;
	public $admin;
	public $frontend;
	public $template;
	public $ajax;
	private $file;
	public $settings;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCPC_PLUGIN_TOKEN;
		$this->text_domain = WCPC_TEXT_DOMAIN;
		$this->version = WCPC_PLUGIN_VERSION;
		$this->settings = get_option( 'wcpc_options' );
		add_action('init', array(&$this, 'init'), 0);
	}
	
	/**
	 * initilize plugin on WP init
	 */
	function init() {
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Init wcpc compare page
		$this->init_wcpc_compare_page();

		// Init ajax
		if(defined('DOING_AJAX')) {
	      $this->load_class('ajax');
	      $this->ajax = new  WCPC_Ajax();
	    }

	    // Init shortcode
		$this->load_class( 'shortcode' );
		$this->shortcode = new WCPC_Shortcode();

		// init templates
      	$this->load_class('template');
      	$this->template = new WCPC_Template();

		if (is_admin()) {
			$this->load_class('admin');
			$this->admin = new WCPC_Admin();
		}

		if (!is_admin() || defined('DOING_AJAX')) {
			$this->load_class('frontend');
			$this->frontend = new WCPC_Frontend();
		}

		// wcpc session
		$this->register_session_for_wcpc_comapre();

	}
	
	/**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
	public function load_plugin_textdomain() {
	    $locale = apply_filters( 'plugin_locale', get_locale(), $this->token );

	    load_textdomain( $this->text_domain, WP_LANG_DIR . "/wc-product-compare/wcpc-$locale.mo" );
	    load_textdomain( $this->text_domain, $this->plugin_path . "/languages/wcpc-$locale.mo" );
	}

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()

    function init_wcpc_compare_page(){
    	global $wpdb,$WCPC;
        $option_value = get_option( 'wcpc_compare_page_id' );
        if ( $option_value > 0 && get_post( $option_value ) )
            return;

        $page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'wcpc_compare' LIMIT 1;");
        if ( $page_found ) :
            if ( ! $option_value )
                update_option( 'wcpc_compare_page_id', $page_found );
            return;
        endif;

        $page_data = array(
            'post_status' 		=> 'publish',
            'post_type' 		=> 'page',
            'post_author' 		=> 1,
            'post_name' 		=> esc_sql( _x( 'wcpc_compare', 'page_slug', $WCPC->text_domain ) ),
            'post_title' 		=> __( 'WC Product Compare', $WCPC->text_domain ),
            'post_content' 		=> '[wcpc_compare]',
            'post_parent' 		=> 0,
            'comment_status' 	=> 'closed'
        );
        $page_id = wp_insert_post( $page_data );

        update_option( 'wcpc_compare_page_id', $page_id );

        register_activation_hook(__FILE__, 'flush_rewrite_rules');
    }
	
	/** Cache Helpers *********************************************************/
	  
	  /**
	   * UnInstall upon deactivation.
	   *
	   * @access public
	   * @return void
	   */
	static function deactivate_wc_product_compare() {
	    delete_option( 'wc_product_compare_installed' );
	}

	function register_session_for_wcpc_comapre() {
	    if(!session_id()) {
	        session_start();
	    }
	}

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if (!defined('DONOTCACHEPAGE'))
			define("DONOTCACHEPAGE", "true");
		// WP Super Cache constant
	}

}