(function($) {
   $.widget("ui.gallery_tag_cloud",  {
     _init: function() {
       var self = this;
       self._set_tag_cloud();
       this._ajax_form();
       this._autocomplete();
     },

     _autocomplete: function() {
       var url = $("#gTagCloud3D").attr("title") + "/autocomplete";
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
             $.get($("#gTagCloud3D").attr("title"), function(data, textStatus) {
               $("#gTagCloud3D").html(data);
               self._set_tag_cloud();
	     });
           }
           form.resetForm();
         }
       });
     },

     _set_tag_cloud: function() {
       var self = this;
       var width = $("#gTagCloud3D").width();
       var tags = document.createElement("tags");
       $("#gTagCloud3D a").each(function(i) {
         var addr = $(this).clone();
         $(addr).attr("style", "font-size:" + $(this).css("fontSize") + ";");
         $(tags).append(addr);
       });

       var so = new SWFObject(self.options.movie, "gTagCloud3D", width, .75 * width, "7", self.options.bgColor);
       so.addParam("wmode", self.options.wmode);
       so.addVariable("mode", "tags");
       so.addVariable("distr", self.options.distr);
       so.addVariable("tcolor", self.options.tcolor);
       so.addVariable("tcolor2", self.options.tcolor2);
       so.addVariable("hicolor", self.options.hicolor);
       so.addVariable("tagcloud", escape("<tags>" + $(tags).html() + "</tags>"));
       so.write("gTagCloud3D");
     }
  });

  $.extend($.ui.gallery_tag_cloud,  {
    defaults: {
      bgColor: 0xFFFFFF,
      wmode: "transparent",
      scriptAccess: "always",
      tcolor: 0x333333,
      tcolor2: 0x009900,
      hicolor: 0x000000,
      tspeed: 100,
      distr: "true",
      mode: "tag"
    }
  });

})(jQuery);
