/*
* Grey Dragon Theme: JS support 
*/

jQuery.fn.extend({

  scrollTo: function(a, b) { },

  myAjaxLoginSubmit: function() {
    $('form#g-login-form').one('submit', function() {
      $(this).ajaxSubmit({
        dataType: 'json', 
        success: function(data) {
          if (data.result == 'error') {
            $('#g-login').html(data.form);
            $().myAjaxLoginSubmit();
          } else {
            // object
            alert(typeof(data));
            Shadowbox.close(); 
            window.location.reload(); 
          }
        }
      }); 
      return false;
    });
  },

  myAjaxSubmit: function() {
    $('form').one('submit', function() {
      try {
        $(this).ajaxSubmit({
          success: function(data) {
            // object
            // alert(typeof(data));
            if (data.result == 'error') {
              $('#sb-content form').html(data.form);
              $().myAjaxSubmit();
            } else {
              Shadowbox.close();
              if (data.reload) {
                window.location.reload();
              }
            }
          }
        });
      } catch (e) { 
        window.location.reload();
      }

      return false;
    });
  },

  theme_ready: function() {
    try {
      if (typeof Shadowbox != 'undefined') {
        Shadowbox.setup("a.g-fullsize-link", {player: 'img'});
        Shadowbox.setup("a.g-sb-preview", {player: 'img', gallery: "preview", animate: true, continuous: true, counterType: "skip", animSequence: "wh", slideshowDelay: 7 });
      }
    } catch (e) { }

    // Initialize dialogs
    $(".g-dialog-link").gallery_dialog();

    // Initialize short forms
    $(".g-short-form").gallery_short_form();

    try {
      $(".g-message-block").fadeOut(10000);
      $(".g-ajax-link").gallery_ajax();
    } catch (e) { }

    $("#g-site-menu>ul>li>ul").show();
    $("#g-login-menu").show();
    $(".g-context-menu").show();
  }

});

function onMiniSlideShowReady() {
  $("#g-rootpage-link").css("background-image", "none");
}

$(document).ready(function() {
  $().theme_ready();
});
