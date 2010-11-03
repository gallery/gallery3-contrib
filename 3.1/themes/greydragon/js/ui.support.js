/*
* Grey Dragon Theme: JS support 
*/

jQuery.fn.extend({
  myAjaxLoginSubmit: function() {

    var myAjaxLoginSubmitOps = {
      dataType: 'json', 
      success: function(data) {
        if (data.result == 'error') {
          $('#g-login').html(data.form);
          $().myAjaxLoginSubmit();
        } else {
          Shadowbox.close(); 
          window.location.reload(); 
        }
      }
    };

    $('form#g-login-form').one('submit', function() {
      $(this).ajaxSubmit(myAjaxLoginSubmitOps); 
      return false;
    });
  },

  myAjaxSubmit: function() {

    var myAjaxSubmitOps = {
      dataType: 'json',
      success: function(data) {
        if (data.result == 'error') {
          $('#sb-content form').html(data.form);
          $().myAjaxSubmit();
        } else {
          Shadowbox.close();
          window.location.reload();
        }
      }
    };

    $('form').one('submit', function() {
      $(this).ajaxSubmit(myAjaxSubmitOps);
      return false;
    });
  },

/*
     _ajaxify_dialog: function() {
       var self = this;
       $("#g-dialog form").ajaxForm({
         dataType: "json",
         beforeSubmit: function(formData, form, options) {
           form.find(":submit")
             .addClass("ui-state-disabled")
             .attr("disabled", "disabled");
           return true;
         },
         success: function(data) {
           if (data.form) {
             var formData = unescape(data.form);
             $("#g-dialog form").replaceWith(formData);
             $("#g-dialog form :submit").removeClass("ui-state-disabled")
               .attr("disabled", null);
             self._ajaxify_dialog();
             self.form_loaded(null, $("#g-dialog form"));
             if (typeof data.reset == 'function') {
               eval(data.reset + '()');
             }
           }
           if (data.result == "success") {
             if (data.location) {
               window.location = data.location;
             } else {
               window.location.reload();
             }
           }
         }
       });
*/
  theme_ready: function() {
    try {
      Shadowbox.setup("a.g-fullsize-link", {player: 'img'});
      Shadowbox.setup("a.g-sb-preview", {player: 'img', gallery: "preview", animate: false, continuous: true, counterType: "skip", animSequence: "wh", slideshowDelay: 5 });

      Shadowbox.setup(".g-dialog-link",    {player: 'ajax', width: 500, height: 420, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup("a#g-login-link",    {player: 'ajax', width: 340, height: 190, enableKeys: false, animate: false, onFinish: $().myAjaxLoginSubmit});
      Shadowbox.setup("a#g-exifdata-link", {player: 'ajax', width: 600, height: 420, animate: false});
      Shadowbox.setup("a#g-disclaimer",    {player: 'ajax', width: 600, height: 420});

      Shadowbox.setup("#g-site-menu .ui-icon-pencil",    {player: 'ajax', width: 500, height: 420, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup(".g-context-menu .ui-icon-pencil", {player: 'ajax', width: 500, height: 420, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      
      Shadowbox.setup("#g-site-menu .ui-icon-plus",      {player: 'ajax', width: 500, height: 390, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup(".g-context-menu .ui-icon-plus",   {player: 'ajax', width: 500, height: 390, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});

      Shadowbox.setup("#g-site-menu .ui-icon-note",      {player: 'ajax', width: 500, height: 370, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup(".g-context-menu .ui-icon-note",   {player: 'ajax', width: 500, height: 370, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});

      Shadowbox.setup("#g-site-menu .ui-icon-key",       {player: 'ajax', width: 700, height: 300, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup(".g-context-menu .ui-icon-key",    {player: 'ajax', width: 700, height: 300, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});

      Shadowbox.setup("#g-site-menu #g-menu-organize-link",   {player: 'ajax', width: 710, height: 460, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup(".g-context-menu #g-menu-organize-link",{player: 'ajax', width: 710, height: 460, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});

      Shadowbox.setup(".g-context-menu .ui-icon-folder-open", {player: 'ajax', width: 400, height: 380, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup("#g-site-menu .g-quick-delete",   {player: 'ajax', width: 400, height: 150, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
      Shadowbox.setup(".g-context-menu .ui-icon-trash", {player: 'ajax', width: 400, height: 150, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});

      Shadowbox.setup("#g-user-profile .g-dialog-link", {player: 'ajax', width: 500, height: 280, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});

      Shadowbox.setup("#add_to_basket .g-dialog-link",  {player: 'ajax', width: 500, height: 360, enableKeys: false, animate: false, onFinish: $().myAjaxSubmit});
    } catch (e) { }

    try {
      $(".g-message-block").fadeOut(10000);
      $(".g-context-menu .g-ajax-link").gallery_ajax();
    } catch (e) { }

    $("#g-site-menu>ul>li>ul").show();
    $("#g-login-menu").show();
    $(".g-context-menu").show();
  },

//  gallery_dialog_postprocess: function(href, title) {
//    Shadowbox.open({player: 'ajax', content: href, width: 500, height: 420, enableKeys: false, animate: false, title: title, onFinish: myAjaxSubmit});
//  }
});

/*
(function($) {

  $.widget("ui.gallery_dialog",  {
    _init: function() {
      var self = this;
      if (!self.options.immediate) {
        this.element.click(function(event) {
          event.preventDefault();
          var href = $(event.currentTarget).attr("href");
          var title = $(event.currentTarget).attr("title");
          setTimeout(function() { $().gallery_dialog_postprocess(href, title); }, 1000);
          return false;
        });
      } else {
        var href = this.element.attr("href");
        var title = this.element.attr("title");
        setTimeout(function() { $().gallery_dialog_postprocess(href, title); }, 1000);
      }
    }
  });
})(jQuery);
*/

$(document).ready(function() {
  $().theme_ready();
});
