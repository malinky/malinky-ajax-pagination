(function( $ ) {

	$( '#theme_defaults' ).change( function( event ) {		
		// Get chosen theme name using bracket notation as value can contain a space 'Twenty Fifteen'.
		var mapDefaultTheme = malinkyAjaxPagingThemeDefaults[ $( this ).val() ];
		
		for ( formField in mapDefaultTheme ) {
			if( mapDefaultTheme.hasOwnProperty( formField ) ) {
				$( '#' + formField ).val( mapDefaultTheme[ formField ] );
      		}
		}
	});

})(jQuery);