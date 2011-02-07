(function($) {
   $.widget("ui.gallery_twitter", {
     
     _init: function() {
       this._set_count();
       $(this.element).bind("keyup", this._set_count);
     },

     _set_count: function() {
       var character_array = $("#g-tweet").val().split("");
       var count = character_array.length;
       var remaining = 140 - count; //self.options.max_count - count;
       var count_container = $("#g-twitter-character-count");
       var color = "#000000";
       var warn_color = "#7F0005"; //this.options.warn_color;
       var error_color = "#FF0000"; //this.options.error_color;
       if (remaining < 10) {
         color = error_color;
       } else if (remaining < 20) {
         color = warn_color;
       }
       $(count_container).css("color", color);
       $(count_container).html(remaining);
     }
     
  });

  $.extend($.ui.gallery_twitter, {
    defaults: {
      max_count: 140,
      warn_color: "#7F0005",
      error_color: "#FF0000"
    }
  });

})(jQuery);
