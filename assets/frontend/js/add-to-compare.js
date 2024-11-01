jQuery( function( $ ) { 

    $(window).bind('found_variation', function(event, variation) {
        if (variation == null) {
        }else{
            $('.wcpc_add_to_compare_btn').attr('data-product_id', variation.id);
        }
    });
    $('.variations_form').trigger( 'found_variation' );
	
    $(document).on( 'click' ,'.wcpc_add_to_compare_btn', function(e){
        e.preventDefault();
        var $this = $(this),
        $this_wrap = $this.parents('.wcpc_compare_btn_wrap'),
        add_to_compare_info = '';
        add_to_compare_info = 'action=wcpc_add_to_compare_action&product_id='+$this.data('product_id')+'&quantity=1';

        $.ajax({
            type   : 'POST',
            url    : wcpc_compare.ajaxurl,
            dataType: 'json',
            data   : add_to_compare_info,
            beforeSend: function(){
                //$this.addClass( 'wcpc_loader' );
            },
            complete: function(){
                //$this.removeClass( 'wcpc_loader' );
            },

            success: function (response) {
                if( response.result == 'true' || response.result == 'exists'){
                    $this.parent().hide().removeClass('show').addClass('addedd');
    
                    $this_wrap.append( '<div class="wcpc_add_item_view_compare_message_list-'+$this.data('product_id')+' wcpc_add_item_view_compare_message"><a class="added_to_compare wc-forward button" href="'+response.wcpc_compare_page_url+'">' + response.label_view_compare + '</a></div>');
                    
                }else if( response.result == 'false' ){
                    $this_wrap.append( '<div class="wcpc_add_item_response-'+$product_id_item.val()+'">' + response.message + '</div>');
                }
            }
        });
    });

    /*Remove product from compare list*/
    $('.wcpc-compare-trash').on( 'click', function(e){
        e.preventDefault();
        var $this = $(this),
            key = $this.data('remove_item'),
            product_id = $this.data('product_id'),
     
        remove_info = 'action=wcpc_remove_from_compare_action&key='+key+'&product_id='+product_id;
        
        $.ajax({
            type   : 'POST',
            url    : wcpc_compare.ajaxurl,
            dataType: 'json',
            data   : remove_info,
            beforeSend: function(){
  
            },
            complete: function(){

            },

            success: function (response) {
                if( response === 1){
                    $this.parents('owl-item').remove();
                }
            }
        });
    });

    /*Clear compare list*/
    $('.wcpc-compare-actions .wcpc_clear_compare').on( 'click', function(e){
        e.preventDefault();
        var $this = $(this),
            key = $this.data('remove_item'),
            product_id = $this.data('product_id'),
     
        remove_info = 'action=wcpc_clear_compare_action';
        
        $.ajax({
            type   : 'POST',
            url    : wcpc_compare.ajaxurl,
            dataType: 'json',
            data   : remove_info,
            beforeSend: function(){
                //$this.addClass( 'wcpc_loader' );
            },
            complete: function(){
                //$this.removeClass( 'wcpc_loader' );
            },

            success: function (response) {
                if( response.status === true){
                    $('.wcpc-compare-responsive .wcpc-compare-inner').html('');
                    $('.wcpc-compare-responsive .wcpc-compare-inner').html('<p style="text-align:center;font-size: 16px;">'+response.empty_msg+'</p>');
                }
            }
        });
    });

});