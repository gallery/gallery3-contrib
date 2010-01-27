/**
 * Initialize jQuery UI and Gallery Plugin elements
 */

$(document).ready(function() {
    $(".g-dialog-link").gallery_dialog('option', 'position', 'top');
    $(".g-dialog-link").gallery_dialog('option', 'draggable', false);
    $(".g-ajax-link").gallery_ajax();
});
