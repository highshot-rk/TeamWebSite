(function(){
	jQuery(".patterns a").click(function(){
		var id = jQuery(this).attr('id');
		jQuery(".patterns a").removeClass("image");
		jQuery(this).addClass("image");
		jQuery("#fbm-pattern").val(id);
	});
	jQuery("#clear-pattern").click(function(){
		jQuery(".patterns a").removeClass("image");
		jQuery("#fbm-pattern").val('');
	});
	
	jQuery(".cb-enable").click(function(){
		jQuery('.switch .cb-disable').removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery('.checkbox',parent).attr('checked', true);

		jQuery("#background-image").hide();
		jQuery("#layout").show();
	});
	jQuery(".cb-disable").click(function(){
		jQuery('.switch .cb-enable').removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery('.checkbox').attr('checked', false);

		jQuery("#layout").hide();
		jQuery("#background-image").show();
	});	
	
})(jQuery);
