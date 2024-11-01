! function($) {
    "use strict";

        var  A = function() {
            var t = $("[data-compare]", "#wcpc-compare");
            t.length && t.each(function() {
                var t = $('[data-compare="' + $(this).data("compare") + '"]', "#wcpc-compare");
                t.matchHeight({
                    byRow: !1
                })
            })
        },
        B = function() {
            var t = $("#wcpc-compare-carousel", "#wcpc-compare"),
                n = t.owlCarousel({
                    items: 3,
                    loop: !1,
                    center: !1,
                    margin: 0,
                    autoWidth: !1,
                    rtl: !1,
                    responsive: {
                        0: {
                            items: 2
                        },
                        479: {
                            items: 2
                        },
                        768: {
                            items: 2
                        },
                        992: {
                            items: 3
                        }
                    },
                    autoHeight: !1,
                    autoplay: !1,
                    autoplayTimeout: 5e3,
                    autoplayHoverPause: !0,
                    nav: !0,
                    navText: "",
                    navElement: "button",
                    navClass: ["owl-prev wcpc_prev", "owl-next wcpc_next"],
                    dots: !1
                }).on("resized.owl.carousel", function() {
                    A()
                });
            t.on("click", ".wcpc-compare-trash", function() {
                var t = $(this).parents(".owl-item").index();
                return n.trigger("remove.owl.carousel", t), !1
            })
        };
    
    $(document).on("ready", function() {

        var add_to_cart_click,
            redirect_to_cart = false;

        $(document).on('click', 'wcpc-product-item-cart', function(){

            add_to_cart_click = $(this);
            add_to_cart_click.block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
            exit;
        });

        $('body').on( 'adding_to_cart', function ( $thisbutton, data ) {
            if( wc_add_to_cart_params.cart_redirect_after_add == 'yes' ) {
                wc_add_to_cart_params.cart_redirect_after_add = 'no';
                redirect_to_cart = true;
            }
        });

        $('body').on('added_to_cart', function( ev, fragments, cart_hash, button ){

            if( redirect_to_cart == true ) {
                // redirect
                parent.window.location = wc_add_to_cart_params.cart_url;
                return;
            }

           // add_to_cart_click.hide();

            // Replace fragments
            if ( fragments ) {
                $.each(fragments, function(key, value) {
                    $(key, window.parent.document).replaceWith(value);
                });
            }
        });
        
//        $(document).on('click', '.added_to_compare', function(){ 
//            
////            A();
//            //$("#wcpc_compare_popup").matchHeight(true);
//        });

        
    }), $(window).on("resize", function() {
        A();
    }), $(window).on("load", function() {
        B(), A();
    }), $(window).on("click", ".added_to_compare", function() { 
        A();
    })
}(jQuery);
