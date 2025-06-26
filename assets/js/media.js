jQuery(document).ready(function($){
    $('body').on('click', '.media-button-select', function(e){
        var creatorField = $('.attachment-details [name="attachments[' + wp.media.frame.state().get('selection').first().id + '][media_creator]"]');
        if ( creatorField.length && creatorField.val().trim() === '' ) {
            alert('Bitte das Creator-Feld ausfüllen.');
            e.preventDefault();
            return false;
        }
    });
});
