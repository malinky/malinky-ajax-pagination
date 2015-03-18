function renderMediaUploader($) {

    var mapFileFrame;

    //If mapFileFrame exists then open it.
    if (mapFileFrame !== undefined) {
        mapFileFrame.open();
        return;
    }


    /**
     * Use the wp.media library to define the settings for the media uploader.
     * https://github.com/thomasgriffin/New-Media-Image-Uploader/blob/master/js/media.js
     * wp-includes/js/media-views.js
     */
    mapFileFrame = wp.media.frames.mapFileFrame = wp.media({
        frame:    'select',
        multiple: false,
        library: { type: 'image' },
        button: { text: 'Select AJAX Loader' }
    });


    /**
     * Setup an event handler for what to do when an image has been selected.
     */
    mapFileFrame.on('select', function() {
        //Grab our attachment selection and construct a JSON representation of the model.
        var mapAjaxLoader = mapFileFrame.state().get('selection').first().toJSON();
        $('#ajax_loader').val(mapAjaxLoader.id);
    });


    //Open the media uploader.
    mapFileFrame.open();

}

(function($) {
    $('#ajax_loader_button').on('click', function(event) {
        event.preventDefault();
        //Display the media uploader.
        renderMediaUploader($);
    });
})(jQuery);