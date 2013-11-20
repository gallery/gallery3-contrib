/**
 * @todo Add shorten/expand urls toggle button
 */
(function($) {
   $.widget("ui.gallery_twitter", {
     
     _init: function() {
       var self = this;
       this._set_count();
       this.element.keyup(function(event) {
         self._set_count(event.currentTarget);
         return false;
       });
       $("#g-attach_image").click( function(){
         self._set_count();
       });
     },

     _set_count: function() {
       var self = this;
       var character_array = $("#g-tweet").val().split("");
       var count = character_array.length;
       var remaining;
       if( $("#g-attach_image").is(':checked') ) { 
       	       remaining = self.options.max_pic_count - count;
       }
       else {
       	       remaining = self.options.max_count - count;
       }
       var count_container = $("#g-twitter-character-count");
       var color = "#000000";
       if (remaining < 10) {
         color = self.options.error_color;
       } else if (remaining < 20) {
         color = self.options.warn_color;
       }
       if (remaining < 0) {
          $("#g-dialog form :submit").addClass("ui-state-disabled")
               .attr("disabled", "disabled");
       } else {
          $("#g-dialog form :submit").removeClass("ui-state-disabled")
               .attr("disabled", null);
       }
       $(count_container).css("color", color);
       $(count_container).html(remaining);
     }
     
  });

  $.extend($.ui.gallery_twitter, {
    defaults: {
      max_count: 140,
      max_pic_count: 115,
      warn_color: "#7F0005",
      error_color: "#FF0000"
    }
  });

})(jQuery);
