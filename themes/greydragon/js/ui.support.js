/**
 * JS support functions for the theme
 */

var myAjaxLoginSubmitOps =
{ dataType: 'json',
  success: function(data) {
    if (data.result == 'error') {
      $('#g-login').html(data.form);
      myAjaxLoginSubmit();
    } else {
      Shadowbox.close();
      window.location.reload();
    }
  }
};

myAjaxLoginSubmit = function() {
  $('form#g-login-form').one('submit', function() {
    $(this).ajaxSubmit(myAjaxLoginSubmitOps);
    return false;
  } )
};
 
var myAjaxSubmitOps =
{ dataType: 'json',
  success: function(data) {
    if (data.result == 'error') {
      $('#sb-content form').html(data.form);
      myAjaxSubmit();
    } else {
      Shadowbox.close();
      window.location.reload();
    }
  }
};

myAjaxSubmit = function() {
  $('form').one('submit', function() {
    $(this).ajaxSubmit(myAjaxSubmitOps);
    return false;
  } )
};


$(document).ready( function() { 
  Shadowbox.setup("a.g-dialog-link", {player: 'ajax', width: 340, height: 316, enableKeys: false, onFinish: myAjaxSubmit});
  Shadowbox.setup("a.g-fullsize-link", {player: 'img'});
  Shadowbox.setup("a#g-login-link", {player: 'ajax', width: 340, height: 230, enableKeys: false, onFinish: myAjaxLoginSubmit});
  Shadowbox.setup("a#g-exifdata-link", {player: 'ajax', width: 420, height: 400});
  Shadowbox.setup(".g-context-menu .ui-icon-pencil", {player: 'ajax', width: 340, height: 370, enableKeys: false});

  $('.g-message-block').fadeOut(10000);
});
