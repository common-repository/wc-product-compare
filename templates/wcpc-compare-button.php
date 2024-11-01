<?php
/**
 * WCPC Compare Button
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCPC,$post;
$view_compare_link='';
if(isset($WCPC->settings['show_compare_item']) && $WCPC->settings['show_compare_item'] == 'popup'){
	$view_compare_link = '#wcpc_compare_popup';
}else{
	$view_compare_link = $wcpc_compare_url;
}
?>

<div class="wcpc_compare_btn_wrap add-compare-<?php echo $product_id ?>">
    <div class="wcpc_compare <?php echo ( $exists ) ? 'hide': 'show' ?>" style="display:<?php echo ( $exists ) ? 'none': 'block' ?>">
        <a href="#" class="wcpc_add_to_compare_btn <?php echo $class ?>" data-product_id="<?php echo $product_id ?>" data-wp_nonce="<?php echo $wpnonce ?>">
		    <?php echo $label ?>
		</a>
    </div>
    <?php if( $exists ): ?>
        <div class="wcpc_add_item_view_compare_list-<?php echo $product_id ?> wcpc_add_item_view_compare_message"><a class="added_to_compare wc-forward <?php echo $class ?>" href="<?php echo $view_compare_link; ?>"><?php echo $label_view_compare ?></a></div>
    <?php endif ?>
</div>

<div class="clear"></div>