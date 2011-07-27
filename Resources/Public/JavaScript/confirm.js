jQuery(document).ready(function() {
	jQuery('a[href="confirmDelete"]').each(function() {
		var id = '#'+jQuery(this).attr('rel');
		jQuery(this).fancybox({
			showCloseButton: false,
			content: jQuery(id).html()
		});
		
		jQuery('.Custom_FancyBox_Close').live('click',function(){
		    $.fancybox.close();
		    return false;
		});
	});
});