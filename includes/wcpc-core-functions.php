<?php
if(!function_exists('woocommerce_required_alert_notice')) {
    function woocommerce_required_alert_notice() {
    ?>
    <div id="message" class="error settings-error notice is-dismissible">
      <p><?php printf( __( '%sWC Product Compare is inactive.%s The %sWooCommerce plugin%s require to work with WC Product Compare. Please %sinstall & activate WooCommerce%s', WCPC_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
      <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
        <?php
    }
}

if(!function_exists('wcpc_get_product_comparision_fields')) {
    function wcpc_get_product_comparision_fields() {
      global $WCPC;
      $fields = apply_filters( 'wcpc_product_comparision_fields', 
                  array(
                    'title' => __('Product Title', $WCPC->text_domain ),
                    'rating' => __('Rating', $WCPC->text_domain ),
                    'price' => __('Product Price', $WCPC->text_domain ),
                    'type' => __('Product Type', $WCPC->text_domain ),
                    'sale' => __('Sale', $WCPC->text_domain ),
                    'availability' => __('Availability', $WCPC->text_domain ),
                    'description' => __('Description', $WCPC->text_domain ),
                    'categories' => __('Categories', $WCPC->text_domain ),
                    'variations' => __('Variations', $WCPC->text_domain ),
                    'tags' => __('Tags', $WCPC->text_domain ),
                    'sku' => __('SKU', $WCPC->text_domain )
                    )
                );
      return $fields;
    }
}

if(!function_exists('wcpc_wc_add_to_cart_button')) {
  function wcpc_wc_add_to_cart_button($product, $args = array()) {
    global $WCPC;
    
    if ( $product ) {
      $defaults = array(
        'quantity' => 1,
        'class'    => implode( ' ', array_filter( array(
                 'wcpc-product-item-cart',
                 'button',
                 'product_type_' . $product->get_type(),
                 $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                 $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
        ) ) ),
      );

      $args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

      echo apply_filters( 'woocommerce_loop_add_to_cart_link',
        sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
            esc_attr( $product->get_id() ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $args['class'] ) ? $args['class'] : ' button' ),
            esc_html( $product->add_to_cart_text() )
        ),
      $product );
    }
  }
}

?>
