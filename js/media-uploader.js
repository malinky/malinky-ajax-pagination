function renderMediaUploader( $ ) {

    var mapFileFrame;

    // If mapFileFrame exists then open it.
    if ( mapFileFrame !== undefined ) {
        mapFileFrame.open();
        return;
    }

    // Use the wp.media library to define the settings for the media uploader.
    // https://github.com/thomasgriffin/New-Media-Image-Uploader/blob/master/js/media.js
    // wp-includes/js/media-views.js
    mapFileFrame = wp.media.frames.mapFileFrame = wp.media({
        frame:    'select',
        multiple: false,
        library: { type: 'image' },
        button: { text: 'Select AJAX Loader' }
    });

    // Setup an event handler for a selected image.
    mapFileFrame.on( 'select', function() {
        // Grab attachment selection and construct a JSON representation.
        var mapAjaxLoader = mapFileFrame.state().get( 'selection' ).first().toJSON();
        $( '#ajax_loader' ).val( mapAjaxLoader.id );
        $( '.malinky-ajax-paging-ajax-loader' ).attr( 'src', mapAjaxLoader.url );
    });

    // Open the media uploader.
    mapFileFrame.open();

}

(function( $ ) {

    $( '#ajax_loader_button' ).click( function( event ) {
        event.preventDefault();
        renderMediaUploader( $ );
    });

    // Revert to original preloader.
    $( '#ajax_loader_remove' ).click( function( event ) {
        event.preventDefault();
        $( '.malinky-ajax-paging-ajax-loader' ).attr( 'src', $( this ).attr( 'href' ) );
        $( '#ajax_loader' ).val( 'default' );
    });    
    
})(jQuery);