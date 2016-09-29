function renderMediaUploader( $ ) {

    var mapFileFrame;

    // If mapFileFrame exists then open it.
    if ( mapFileFrame !== undefined ) {
        mapFileFrame.open();
        return;
    }

    // Use the wp.media library to define the settings for the media uploader.
    // https://github.com/thomasgriffin/New-Media-Image-Uploader/blob/master/js/media.js
    // includes/js/media-views.js
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
        $( '#_malinky_ajax_pagination_settings_ajax_loader' ).val( mapAjaxLoader.id );
        $( '#ajax_loader_custom img' ).attr( 'src', mapAjaxLoader.url );
        $( '#ajax_loader_button').addClass('active');
        $( '#ajax_loader_remove').removeClass('active');
        $( '#ajax_loader_default_container').removeClass('active');
        $( '#ajax_loader_custom_container').addClass('active');
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
        $( '#ajax_loader_default_container').addClass('active');
        $( '#ajax_loader_custom_container').removeClass('active');
        $( '#ajax_loader' ).val( 'default' );
        $( '#ajax_loader_button').removeClass('active');
        $( '#ajax_loader_remove').addClass('active');
    });    
    
})(jQuery);