
jQuery(function($) {

    // Images
    var file_frame;
    var file_frame_field_id;

    // limit to one image only
    file_frame = wp.media.frames.file_frame = wp.media({
        title: texts.select_image,
        multiple: false
    });

    file_frame.on("select", function() {
        var image = file_frame.state().get("selection").first().toJSON();
        $("#"+file_frame_field_id).val(image.url);
    });

    // open media library
    $('#sunflower_open_graph_fallback_image_button').on('click', function(event) {
        event.preventDefault();
        file_frame_field_id='sunflower_open_graph_fallback_image';
        if (file_frame) {
            file_frame.open();
        }
    });

});
