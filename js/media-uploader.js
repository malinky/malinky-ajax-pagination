function renderMediaUploader( $, loaderCount ) {

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
        $( '#ajax_loader_' + loaderCount ).val( mapAjaxLoader.id );
    });

    // Open the media uploader.
    mapFileFrame.open();

}

(function( $ ) {

    $( 'a[id^=ajax_loader_button]' ).on( 'click', function( event ) {
        event.preventDefault();

        // Get the clicked loader count.
        var loader      = event.target.id;
        var loaderCount = loader.lastIndexOf( '_' );
            loaderCount = loader.substring( loaderCount+1 );

        // Display the media uploader and pass in the count of it.
        renderMediaUploader( $, loaderCount );
    });
    
})(jQuery);