(function($) {
	console.log(malinky_ajax_paging_theme_defaults);
	$('#theme_defaults, #woo_theme_defaults').change(function(event) {
		var theme_type = event.target.id;
		//Get chosen theme name using brcket notation as value contains a space 'Twenty Fifteen'.
		var mapDefaultTheme = malinky_ajax_paging_theme_defaults[theme_type][$(this).val()];
		for (formField in mapDefaultTheme) {
			if(mapDefaultTheme.hasOwnProperty(formField)) {
				$('#' + formField).val(mapDefaultTheme[formField]);
      		}
		}
	});
})(jQuery);