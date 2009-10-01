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
       var object = document.createElement("object");
       $(object).attr({
         type: "application/x-shockwave-flash",
         data: self.options.movie,
         width: width,
         height: .75 * width
       });
       var value = "tcolor=" + self.options.tcolor + "&tcolor2=" + self.options.tcolor2 +
         "&hicolor=" + self.options.hicolor + "&tspeed=" + self.options.tspeed +
         "&distr=" + self.options.distr + "&mode=" + self.options.mode +
         "&tagcloud=" + escape("<tags>" + $(tags).html() + "</tags>");
       $(object).append("<param name=\"movie\" value=\"" + self.options.movie +"\" />")
         .append("<param name=\"wmode\" value=\"" + self.options.transparent + "\" />")
         .append("<param name=\"bgcolor\" value=\"" + self.options.bgColor + "\" />")
         .append("<param name=\"allowScriptAccess\"value=\"" + self.options.scriptAccess + "\" />")
         .append("<param name=\"flashvars\" value=\"" + value + "\" />");
       $("#gTagCloud3D").html(object);
     }
  });

  $.extend($.ui.gallery_tag_cloud,  {
    defaults: {
      bgColor: false,
      wmode: "transparent",
      scriptAccess: "always",
      tcolor: "0x333333",
      tcolor2: "0x009900",
      hicolor: "0x000000",
      tspeed: 100,
      distr: "true",
      mode: "tag"
    }
  });

})(jQuery);
