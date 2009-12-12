(function($) {
  var _current_path = "";

  $.fn.gallery3_client = function() {
    return this.each(function(i) {
      ajaxifyLoginForm(this);
    });
  };

  function ajaxifyLoginForm(obj) {
    var login = $(obj).find("#g-login-form form");
    if (login.length > 0) {
      $(login).ajaxForm({
        dataType: "json",
        beforeSubmit: function(formData, form, options) {
          form.find(":submit")
            .addClass("ui-state-disabled")
            .attr("disabled", "disabled");
          return true;
        },
        success: function(data) {
          $(obj).html(data.content);
          if (data.status == "ok") {
            initializeDetail(obj);
          } else {
            ajaxifyLoginForm(obj);
          }
        }
      });
    } else {
      initializeDetail(obj);
    }
  };

  function initializeDetail(obj) {
    $(".ui-icon-plus", obj).live("click", function (event) {
      var siblings = $("~ ul", this);
      if (siblings.length > 0) {
        siblings.show();
        $(this).removeClass("ui-icon-plus");
        $(this).addClass("ui-icon-minus");
      } else {
        var parent = $(this).parent("li");
        $.get("/g3_client/index.php/g3_client/albums",
              {path: $(parent).attr("ref")},
              function(data, textStatus) {
          $(parent).replaceWith(data);
        });
      }
    });
    $(".ui-icon-minus", obj).live("click", function (event) {
      $("~ ul", this).hide();
      $(this).removeClass("ui-icon-minus");
      $(this).addClass("ui-icon-plus");
    });
    $(".tree-title", obj).click(function (event) {
        $.get("/g3_client/index.php/g3_client/detail",
              {path:  $(this).parent("li").attr("ref")},
              function(data, textStatus) {
          $("#center").html(data);
        });
      $(".ui-selected").removeClass("ui-selected");
      $(this).addClass("ui-selected");
    });
    $("#album_tree [ref=''] .tree-title:first").addClass("ui-selected");
  };
 })(jQuery);
