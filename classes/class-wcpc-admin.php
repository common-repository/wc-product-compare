<?php
class WCPC_Admin {

	public function __construct() {
		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
		add_action( 'admin_init', array( &$this, 'wcpc_settings_init' ) );
		add_action( 'admin_menu', array( &$this, 'wcpc_options_page' ) );
		add_filter("settings_pages_tab_new_input", array($this, 'wcpc_compare_settings_sanitize'), 10, 2);
		add_action('wcpc_ks_settings_footer', array(&$this, 'ks_admin_footer_for_wcpc'));
	}

	 /**
	 * Custom option and settings
	 */
	function wcpc_settings_init() {
		global $WCPC;
		register_setting( 'wcpc', 'wcpc_options' );
		 
		add_settings_section(
			'wcpc_section',
			__( 'WC Product Compare Settings', $WCPC->text_domain ),
			array( &$this, 'wcpc_section_callback'),
			'wcpc'
		);
		
		add_settings_field(
			'wcpc_enable_compare', 
			__( 'Enable WC Product Compare', $WCPC->text_domain ),
				array( &$this, 'wcpc_settings_enable_compare_mapping' ),
			'wcpc',
			'wcpc_section',
			[
			'label_for' => 'is_enable_compare',
			'class' => 'wcpc_row',
			'wcpc_custom_data' => '',
			]
		);
		add_settings_field(
			'wcpc_enable_compare_single_page',
			__( 'Enable WC Product Compare in Single Page', $WCPC->text_domain ),
				array( &$this, 'wcpc_settings_enable_compare_single_mapping' ),
			'wcpc',
			'wcpc_section',
			[
			'label_for' => 'is_enable_compare_single_page',
			'class' => 'wcpc_row',
			'wcpc_custom_data' => '',
			]
		);
		add_settings_field(
			'wcpc_show_compare_item',
			__( 'Show Compare item in', $WCPC->text_domain ),
				array( &$this, 'wcpc_settings_show_compare_item_mapping' ),
			'wcpc',
			'wcpc_section',
			[
			'label_for' => 'show_compare_item',
			'class' => 'wcpc_row',
			'wcpc_custom_data' => '',
			]
		);
		add_settings_field(
			'wcpc_add_compare_text',
			__( 'Add Compare Button Text', $WCPC->text_domain ),
				array( &$this, 'wcpc_settings_compare_text_mapping' ),
			'wcpc',
			'wcpc_section',
			[
			'label_for' => 'custom_add_compare_text',
			'class' => 'wcpc_row',
			'wcpc_custom_data' => '',
			]
		);
		add_settings_field(
			'wcpc_view_compare_text',
			__( 'View Compare Button Text', $WCPC->text_domain ),
				array( &$this, 'wcpc_settings_view_compare_text_mapping' ),
			'wcpc',
			'wcpc_section',
			[
			'label_for' => 'custom_view_compare_text',
			'class' => 'wcpc_row',
			'wcpc_custom_data' => '',
			]
		);
		add_settings_field(
			'wcpc_compare_product_fields',
			__( 'Enable Product Fields', $WCPC->text_domain ),
				array( &$this, 'wcpc_settings_compare_product_fields_mapping' ),
			'wcpc',
			'wcpc_section',
			[
			'label_for' => 'compare_product_fields',
			'class' => 'wcpc_row',
			'wcpc_custom_data' => '',
			]
		);

	}

	function wcpc_section_callback( $args ) {
		global $WCPC;
	 ?>
	 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Setup basic options', $WCPC->text_domain ); ?></p>
	 <?php
	}
	 
	function wcpc_settings_enable_compare_mapping( $args ) {
		global $WCPC;
		?>
		<div class="wcpc_field">
			<div class="wcpc_check">
        		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="Enable" <?php if(isset($WCPC->settings[$args['label_for']]) && $WCPC->settings[$args['label_for']] == 'Enable') echo 'checked'; ?> />
        		<label for="<?php echo esc_attr( $args['label_for'] ); ?>"></label>
        	</div>
		</div>
		<?php 
	}

	function wcpc_settings_enable_compare_single_mapping( $args ) {
		global $WCPC;
		?>
		<div class="wcpc_field">
			<div class="wcpc_check">
        		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="Enable" <?php if(isset($WCPC->settings[$args['label_for']]) && $WCPC->settings[$args['label_for']] == 'Enable') echo 'checked'; ?>/>
        		<label for="<?php echo esc_attr( $args['label_for'] ); ?>"></label>
        	</div>
		</div>
		<?php 
	}

	function wcpc_settings_show_compare_item_mapping( $args ) {
		global $WCPC;
		?>
		<div class="wcpc_field">
			<div class="wcpc_radio">
        		<input type="radio" id="popup_<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="popup" <?php if(isset($WCPC->settings[$args['label_for']]) && $WCPC->settings[$args['label_for']] == 'popup') echo 'checked'; ?>>
    			<label for="popup_<?php echo esc_attr( $args['label_for'] ); ?>"><?php _e('Pop Up', $WCPC->text_domain ) ?></label>
    			<input type="radio" id="page_<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="page" <?php if(isset($WCPC->settings[$args['label_for']]) && $WCPC->settings[$args['label_for']] == 'page') echo 'checked'; ?>>
    			<label for="page_<?php echo esc_attr( $args['label_for'] ); ?>"><?php _e('Page', $WCPC->text_domain ) ?></label>
        	</div>
		</div>
		<?php 
	}
	
	function wcpc_settings_compare_text_mapping( $args ) {
		global $WCPC;
		?>
		<div class="wcpc_field">
			<div class="wcpc_text">
        		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php if(isset($WCPC->settings[$args['label_for']]) ) echo $WCPC->settings[$args['label_for']]; ?>" placeholder="<?php _e('Add to Compare', $WCPC->text_domain ) ?>">
        	</div>
		</div>
		<?php 
	}

	function wcpc_settings_view_compare_text_mapping( $args ) {
		global $WCPC;
		?>
		<div class="wcpc_field">
			<div class="wcpc_text">
        		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php if(isset($WCPC->settings[$args['label_for']])) echo $WCPC->settings[$args['label_for']]; ?>" placeholder="<?php _e('View Compare', $WCPC->text_domain ) ?>">
        	</div>
		</div>
		<?php 
	}

	function wcpc_settings_compare_product_fields_mapping( $args ) {
		global $WCPC;
		?>
		<div class="wcpc_field">
			<div class="wcpc_sort">
	        	<select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="wcpc_options[<?php echo esc_attr( $args['label_for'] ); ?>][]" multiple="multiple" style="width: 100%;">
	        	<?php foreach(wcpc_get_product_comparision_fields() as $key => $value){
	        		$selected = '';
	        		if(isset($WCPC->settings[$args['label_for']]) && is_array($WCPC->settings[$args['label_for']]) && in_array($key, $WCPC->settings[$args['label_for']])) $selected = 'selected';
	        			echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
	        		} 
	        	?>
				</select>
				<p class="description">
				 	<?php esc_html_e( 'You can set and order product fields ( Except product image ) which display in product comparison table according to settings order.', $WCPC->text_domain ); ?>
				</p>
        	</div>
		</div>
		<?php 
	}

	/**
	 * top level menu
	 */
	function wcpc_options_page() {
		global $WCPC;
		 // add top level menu page
		 add_menu_page(
		 __( 'WC Product Compare', $WCPC->text_domain ),
		 __( 'WC Product Compare', $WCPC->text_domain ),
		 'manage_options',
		 'wcpc_settings',
		 array( &$this, 'wcpc_options_page_html' ),
		 'dashicons-randomize',
		 58
		 );
	}

	/**
	 * top level menu:
	 * callback functions
	 */
	function wcpc_options_page_html() {
		global $WCPC;
		 // check user capabilities
		 if ( ! current_user_can( 'manage_options' ) ) {
			 return;
		}
	 
		// add error/update messages
		 
		// check if the user have submitted the settings
		// wordpress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wcpc_messages', 'wcpc_message', __( 'Settings Saved', $WCPC->text_domain ), 'updated' );
		}
	 
		// show error/update messages
		settings_errors( 'wcpc_messages' );
		?>
		<div class="wrap wcpc_settings_wrap">
		 	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
		 	<?php
		 	// output security fields for the registered setting "wcpc"
		 	settings_fields( 'wcpc' );
		 	// output setting sections and their fields
		 	// (sections are registered for "wcpc", each field is registered to a specific section)
		 	do_settings_sections( 'wcpc' );
		 	// output save settings button
		 	submit_button( 'Save Settings' );
		 	?>
		 	</form>
			<?php do_action('wcpc_ks_settings_footer'); ?>
		</div>
		<?php
	}

    /**
     * Save settings of WC Product Compare Settings
     *
     * @param new_input	input     WC Product Compare Settings inputs
     * @return WC Product Compare settings inputs are merged
     */
    function wcpc_compare_settings_sanitize($new_input, $input) {
        global $WCMp, $WCPC;

        if (isset($input['is_enable_compare']))
            $new_input['is_enable_compare'] = sanitize_text_field($input['is_enable_compare']);

        if (isset($input['is_enable_compare_single_page']))
            $new_input['is_enable_compare_single_page'] = sanitize_text_field($input['is_enable_compare_single_page']);

        if (isset($input['show_compare_item']))
            $new_input['show_compare_item'] = sanitize_text_field($input['show_compare_item']);

        if (isset($input['custom_add_compare_text']))
            $new_input['custom_add_compare_text'] = sanitize_text_field($input['custom_add_compare_text']);

        if (isset($input['custom_view_compare_text']))
            $new_input['custom_view_compare_text'] = sanitize_text_field($input['custom_view_compare_text']);
        
        if (isset($input['compare_product_fields']))
            $new_input['compare_product_fields'] = sanitize_text_field($input['compare_product_fields']);
        
        return $new_input;
    }


	
	function ks_admin_footer_for_wcpc() {
    global $WCPC;
    ?>
    <div style="clear: both"></div>
    <div id="ks_admin_footer">
      <?php _e('Powered by', $WCPC->text_domain); ?> <?php _e('itzmekhokan', $WCPC->text_domain); ?> &copy; <?php echo date('Y');?>
    </div>
    <?php
	}
	

	/**
	 * Admin Scripts
	 */

	public function enqueue_admin_script() {
		global $WCPC;
		$screen = get_current_screen();
		// Enqueue admin script and stylesheet from here
		if (isset($screen->id) && in_array( $screen->id, array( 'toplevel_page_wcpc_settings' ))) : 
			if ( class_exists( 'woocommerce' ) ) {
        		wp_dequeue_style( 'select2' );
        		wp_deregister_style( 'select2' );

        		wp_dequeue_script( 'select2');
        		wp_deregister_script('select2');
    		}  
		  	wp_enqueue_script('wcpc_admin_js', $WCPC->plugin_url.'assets/admin/js/admin.js', array('jquery'), $WCPC->version, true);
		  	wp_enqueue_script('wcpc_select2_js', $WCPC->plugin_url.'assets/admin/js/select2.js', array('jquery'), $WCPC->version, true);
		 	wp_enqueue_style('wcpc_admin_css',  $WCPC->plugin_url.'assets/admin/css/admin.css', array(), $WCPC->version);
		 	wp_enqueue_style('wcpc_select2_css',  $WCPC->plugin_url.'assets/admin/css/select2.min.css', array(), $WCPC->version);

		endif;
	}
}