(function($) {
   $.widget("ui.gallery_tag_cloud",  {
     _init: function() {
       var self = this;
       self._set_tag_cloud();
       this._ajax_form();
       this._autocomplete();
     },

     _autocomplete: function() {
       var url = $("#gTagCloud").attr("title") + "/autocomplete";
       $("#gAddTagForm input:text").autocomplete(
         url, {
           max: 30,
           multiple: true,
           multipleSeparator: ',',
           cacheLength: 1}
       );
     },

     _ajax_form: function() {
       var self = this;
       var form = $("#gAddTagForm");
       form.ajaxForm({
         dataType: "json",
         success: function(data) {
           if (data.result == "success") {
             $.get($("#gTagCloud").attr("title"), function(data, textStatus) {
               $("#gTagCloud").html(data);
               self._set_tag_cloud();
	     });
           }
           form.resetForm();
         }
       });
     },

     _set_tag_cloud: function() {
       var self = this;
       var width = $("#gTagCloud").width();
       var tags = document.createElement("tags");
       $("#gTagCloud a").each(function(i) {
         var addr = $(this).clone();
         $(addr).attr("style", "font-size: 14pt;");
         $(tags).append(addr);
       });

       var flashvars = {
         tcolor: self.options.tcolor,
         tcolor2: self.options.tcolor2,
         mode: "tags",
         distr: self.options.distr,
         tspeed: self.options.tspeed,
         hicolor: self.options.hicolor,
         tagcloud: escape("<tags>" + $(tags).html() + "</tags>").toLowerCase()
       };
       var params = {
         bgcolor: self.options.bgColor,
         wmode: self.options.wmode,
         allowscriptaccess: self.options.scriptAccess
       };

       swfobject.embedSWF(self.options.movie, "gTagCloud", width, .75 * width, "9", false,
                          flashvars, params);
     }
  });

  $.extend($.ui.gallery_tag_cloud,  {
    defaults: {
      bgColor: "0xFFFFFF",
      wmode: "transparent",
      scriptAccess: "always",
      tcolor: "0x333333",
      tcolor2: "0x009900",
      hicolor: "0x000000",
      tspeed: "100",
      distr: "true",
      mode: "tag"
    }
  });

})(jQuery);
