(function($) {
  $.widget("ui.gallery_ajax",  {
    _init: function() {
      this.element.click(function(event) {
        eval("var ajax_handler = " + $(event.currentTarget).attr("ajax_handler"));
        $.getJSON($(event.currentTarget).attr("href"), function(data) {
          ajax_handler(data);
        });
        event.preventDefault();
        return false;
      });
    }
  });
})(jQuery);
