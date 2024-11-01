jQuery(document).ready(function($) {
	$('#compare_product_fields').select2();
	$("#compare_product_fields").on("select2:select", function (evt) {
  		var element = evt.params.data.element;
  		var $element = $(element);
  
  		$element.detach();
  		$(this).append($element);
  		$(this).trigger("change");
	});
});

