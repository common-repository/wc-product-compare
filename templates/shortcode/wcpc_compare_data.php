<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WCPC, $WCPC_Product;
$wcpc_compare_data = $WCPC_Product->get_compare_data();
//set default product comapre fields
if(!isset($WCPC->settings['compare_product_fields']))
    $WCPC->settings['compare_product_fields'] = apply_filters( 'wcpc_product_comparision_default_fields', array('title','rating','price','sale','availability','categories','description'));
?>

<div class="wcpc-compare" id="wcpc-compare">
    <div class="container">
        <?php do_action( 'wcpc_before_compare_list_content' ); ?>
        <div class="wcpc-compare-actions"><button class="wcpc_clear_compare"><?php echo apply_filters( 'wcpc_claer_compare_list_btn_label', __('Clear List', $WCPC->text_domain ) ); ?></button></div>
        <div class="wcpc-compare-responsive">
            <div class="wcpc-compare-inner">
                <div class="wcpc-compare-sidebar">
                    <div class="wcpc-compare-table">
                        <div class="wcpc-compare-row">
                            <div class="wcpc-compare-cell" data-compare="1"><?php _e('Product Image', $WCPC->text_domain )?></div>
                        </div>
                        <?php 
                        if(isset($WCPC->settings['compare_product_fields']) && is_array($WCPC->settings['compare_product_fields']) && count($WCPC->settings['compare_product_fields']) > 0 ){ $k=2;
                            foreach ($WCPC->settings['compare_product_fields'] as $key => $value) { ?>
                            <div class="wcpc-compare-row">
                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>"><?php echo $value; ?></div>
                            </div>
                        <?php $k++; }
                        }
                        ?>
                        <div class="wcpc-compare-row">
                            <div class="wcpc-compare-cell" data-compare="11"><?php _e('Action', $WCPC->text_domain )?></div>
                        </div>
                    </div>
                </div>
                <div class="wcpc-compare-content">
                    <div class="wcpc-compare-carousel owl-carousel" id="wcpc-compare-carousel">
                    <?php 
                    if(is_array($wcpc_compare_data) && count($wcpc_compare_data) > 0 ){
                        foreach($wcpc_compare_data as $key => $value){
                            $product = wc_get_product( $value['product_id'] ); ?>
                        <div class="wcpc-compare-item" data-id="<?php echo $product->get_id();?>">
                            <div class="wcpc-compare-row">
                                <div class="wcpc-compare-cell" data-compare="1">
                                    <a href="#" class="wcpc-compare-trash" title="Remove this from Compare List" data-remove_item="<?php echo $key; ?>" data-product_id="<?php echo $product->get_id();?>">&times;</a>
                                    <a href="#"><?php echo $product->get_image('shop_thumbnail'); ?></a>
                                </div>
                            </div>
                            <?php 
                            if(isset($WCPC->settings['compare_product_fields']) && is_array($WCPC->settings['compare_product_fields']) && count($WCPC->settings['compare_product_fields']) > 0 ){ $k=2; 
                                foreach ($WCPC->settings['compare_product_fields'] as $index => $field) { 
                                    switch ($field) {
                                        case 'title': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <h3 class="wcpc-compare-title"><a href="#"><?php echo $product->get_title();?></a></h3>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'rating': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-rating"><?php echo wc_get_rating_html( $product->get_average_rating() ); ?></div>
                                                </div>
                                            </div>
                                        <?php break;
                                            
                                        case 'price': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-price"><?php echo $product->get_price_html(); ?></div>
                                                </div>
                                            </div>
                                        <?php break;
                                        
                                        case 'type': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-type"><?php echo ucfirst($product->get_type()); ?></div>
                                                </div>
                                            </div>
                                        <?php break;
                                        
                                        case 'sale': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-sale"><?php if ( $product->is_on_sale() ) : ?>
                                                    <?php echo '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>'; ?>
                                                    <?php endif; ?></div>
                                                </div>
                                            </div>
                                        <?php break;
                                        
                                        case 'availability': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-availability">
                                                    <?php if ( $product->is_in_stock() )
                                                        echo '<span class="stock in-stock">'.__('In Stock', $WCPC->text_domain ).'</span>';
                                                    else
                                                        echo '<span class="stock out-of-stock">'.__('Out of Stock', $WCPC->text_domain ).'</span>';
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;

                                        case 'description': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-description">
                                                    <?php $post = get_post($product->get_id()); echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;

                                        case 'variations': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-variations">
                                                    <?php 
                                                    if($product->get_type() == 'variable' && count($product->get_attributes()) > 0){ 
                                                        foreach($product->get_attributes() as $key => $value){ 
                                                            if(is_object($value)){
                                                                echo get_taxonomy( $key )->label.' : '.get_the_term_list( $product->get_id(), $key, $before='', $sep=', ', $after='' ).'<br/>';
                                                            }else{}
                                                        }
                                                    }else if( $product->get_type() == 'variation' && count($product->get_attributes()) > 0){ 
                                                        foreach($product->get_attributes() as $key => $value){ 
                                                            echo get_taxonomy( $key )->label.' : '.$value.'<br/>';
                                                        }
                                                    }
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        
                                        case 'categories': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>"><div class="wcpc-compare-category"><?php echo wc_get_product_category_list( $product->get_id(), ', '); ?></div></div>
                                            </div>
                                        <?php break;
                                        
                                        case 'tags': ?>
                                            <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>"><div class="wcpc-compare-tag"><?php echo wc_get_product_tag_list( $product->get_id(), ', '); ?></div></div>
                                            </div>
                                        <?php break;
                                        
                                        case 'sku': ?>
                                             <div class="wcpc-compare-row">
                                                <div class="wcpc-compare-cell" data-compare="<?php echo $k; ?>">
                                                    <div class="wcpc-compare-sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></div>
                                                </div>
                                            </div>
                                        <?php break;
                                        
                                        default:
                                         
                                            break;
                                    }
                            $k++; }
                            }
                            ?>
                            <div class="wcpc-compare-row">
                                <div class="wcpc-compare-cell" data-compare="11">
                                    <div class="wcpc-compare-action">
                                    <?php wcpc_wc_add_to_cart_button($product); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php   }
                        } 
                    ?>
                    </div>
                </div>
            </div>
        </div>
        <?php do_action( 'wcpc_after_compare_list_content' ); ?>
    </div>
</div>