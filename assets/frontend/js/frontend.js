jQuery( function( $ ) {
  $(window).on("load", function() {
    $('#wcpc_compare_popup').load();
    var t = $("[data-compare]", "#wcpc-compare");
        t.length && t.each(function() {
            var t = $('[data-compare="' + $(this).data("compare") + '"]', "#wcpc-compare");
            t.matchHeight();
        });
        closePopup();
  });

  $(document).on( 'click' ,'.added_to_compare', function(e){
  		e.preventDefault();
  		var link = $(this).attr('href');
  		if (link.indexOf('wcpc_compare_popup') > -1){

        data_info = 'action=wcpc_show_compare_popup_data_action';
        
        $.ajax({
            type   : 'POST',
            url    : wcpc_frontend.ajaxurl,
            dataType: 'json',
            data   : data_info,
            beforeSend: function(){

            },
            complete: function(){
              showPopup();
              owlC();
              var t = $("[data-compare]", "#wcpc-compare");
              t.length && t.each(function() {
                  var t = $('[data-compare="' + $(this).data("compare") + '"]', "#wcpc-compare");
                  t.matchHeight();
              });
            },

            success: function (response) {
                if( response.status === true){
                    $('#wcpc_compare_popup .modal-body').html('');
                    $('#wcpc_compare_popup .modal-body').html(response.data);
                }
            }
        });
  		}else{
  			window.location.href = link;
  		}
	});

    /*Clear compare list*/
    $(document).on( 'click','#wcpc_compare_popup .wcpc-compare-actions .wcpc_clear_compare', function(e){
        e.preventDefault();
        var $this = $(this),
            key = $this.data('remove_item'),
            product_id = $this.data('product_id'),
     
        remove_info = 'action=wcpc_clear_compare_action';
        
        $.ajax({
            type   : 'POST',
            url    : wcpc_frontend.ajaxurl,
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


  function showPopup(){
      $("#wcpc_compare_popup").show();
      $("#wcpc_compare_popup").css('opacity', 1);
  }

	function closePopup(){
      $("#wcpc_compare_popup").hide();
      $("#wcpc_compare_popup").css('opacity', 0);
	}

  function owlC() {
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
              //A();
          });
      t.on("click", ".wcpc-compare-trash", function() {
            var $this = $(this),
              key = $this.data('remove_item'),
              product_id = $this.data('product_id'),
       
          remove_info = 'action=wcpc_remove_from_compare_action&key='+key+'&product_id='+product_id;
          
          $.ajax({
              type   : 'POST',
              url    : wcpc_frontend.ajaxurl,
              dataType: 'json',
              data   : remove_info,
              beforeSend: function(){
    
              },
              complete: function(){

              },

              success: function (response) {
                  if( response === 1){
                      //$this.parents('owl-item').remove();
                      var t = $(this).parents(".owl-item").index();
                      return n.trigger("remove.owl.carousel", t), !1
                  }
              }
          });
          
      })
  }

	$("#wcpc_compare_popup .close").click(function(){
  		closePopup();
	});

	$(document).keyup(function(e) {
  		if (e.keyCode == 27) {  
    		closePopup();     
  		}   // esc
	});
});
// When the user clicks anywhere outside of the modal, close it
var wcpc_modal = document.getElementById('wcpc_compare_popup');
window.onclick = function(event) {
    if (event.target == wcpc_modal) {
        wcpc_modal.style.display = "none"; 
    }
}