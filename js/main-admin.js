(function( $ ) {

	$( document ).on( 'change', 'select[id^=theme_defaults]', function( event ) {
		var themeType 	= event.target.id;
		var themeCount 	= themeType.lastIndexOf( '_' );
			themeCount 	= themeType.substring( themeCount+1 );
		
		// Get chosen theme name using bracket notation as value can contain a space 'Twenty Fifteen'.
		var mapDefaultTheme = malinkyAjaxPagingThemeDefaults[ $( this ).val() ];
		for ( formField in mapDefaultTheme ) {
			if( mapDefaultTheme.hasOwnProperty( formField ) ) {
				$( '#' + formField + '_' + themeCount ).val( mapDefaultTheme[ formField ] );
      		}
		}
	});

	$( '.malinky-ajax-paging-add-button' ).click( function( event ) {
		event.preventDefault();

		// Clone the first block.
		var cloned = $( '#clone-1' )
			.clone()
			.attr( 'id', 'clone-' + parseInt( $( '.malinky-ajax-paging-clone' ).length+1 ) )
			.insertAfter( '.malinky-ajax-paging-clone:last' );

		// Replace each name attribute with the new number.
		cloned.find( 'input, select, option' ).each( function() {
			$( this ).attr( 'name', $( this ).attr( 'name' ).replace( /\[(\d)+\]/, '[' + parseInt( $( '.malinky-ajax-paging-clone' ).length-1 ) + ']' ) );
		});

		// Replace each id attribute with the new number.
		cloned.find( 'input, select, option' ).each( function() {
			$( this ).attr( 'id', $( this ).attr( 'id' ).replace( /(\d)+/, parseInt( $( '.malinky-ajax-paging-clone' ).length-1 ) ) );
		});		
	});
	
})(jQuery);