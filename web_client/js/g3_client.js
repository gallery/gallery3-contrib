(function($) {
  var _paths = [];
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
            save_paths(".tree-title.ui-selected");
          } else {
            ajaxifyLoginForm(obj);
          }
        }
      });
    } else {
      console.group("ajaxifyLoginForm");
      initializeDetail(obj);
      save_paths($(".tree-title.ui-selected").parents("li:first").attr("ref"));
      console.groupEnd();
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
        var parent = $(this).parents("li:first");
        $.get("/g3_client/index.php/g3_client/albums",
              {path: $(parent).attr("ref")},
              function(data, textStatus) {
          $(parent).replaceWith(data);
        });
      }
      return false;
    });

    $(".ui-icon-minus", obj).live("click", function (event) {
      $("~ ul", this).hide();
      $(this).removeClass("ui-icon-minus");
      $(this).addClass("ui-icon-plus");
      return false;
    });

    $("#center a.child-link").live("dblclick", function(event) {
      event.preventDefault();
      event.stopPropagation();
      var path = $(this).parents("li:first").attr("ref");
      var url = $(this).attr("href");
      get_detail(path, function(path) {
        var album = $("#album_tree [ref='" + path + "']");
        if (album.length > 0) {
          $(".ui-selected").removeClass("ui-selected");
          $(".tree-title", album).addClass("ui-selected");
        }
      });
      return false;
    });

    $("#center a.child-link").live("click", function(event) {
      $(".thumb-grid-cell.ui-selected").removeClass("ui-selected");
      $(this).parents("li:first").addClass("ui-selected");
      return false;
    });

    $(".wc-button").live("click", function() {
      if ($(this).parent("a").hasClass("ui-state-disabled")) {
        return false;
      }
      if ($("span", this).hasClass("ui-icon-eject")) {
        get_detail($("span", this).attr("ref"));
      }
      return false;
    });

    $(".tree-title", obj).live("click", function (event) {
      get_detail($(this).parents("li:first").attr("ref"));
      $(".ui-selected").removeClass("ui-selected");
        $(this).addClass("ui-selected");
        return false;
      });

    $("#album_tree [ref=''] .tree-title:first").addClass("ui-selected");
    set_selected_thumb();
  };

  function set_selected_thumb() {
     if ($(".thumb-grid-cell.ui-selected").length == 0) {
       $(".thumb-grid-cell:first").addClass("ui-selected");
     }
  }

  function get_detail(path, callback) {
    $.get("/g3_client/index.php/g3_client/detail", {path: path}, function(data, textStatus) {
      console.group("get_detail.get callback");
      $("#wc-detail").html(data);
      set_selected_thumb();
      save_paths(path);
      if (callback != undefined) {
        callback(path);
      }
      _current_path = path;
      console.groupEnd();
    });
  }

  function save_paths(path) {
    _paths[path] = [];
    $(".thumb-grid-cell").each(function(i) {
      var item_path = $(this).attr("ref");
      var idx = item_path.lastIndexOf("/");
      _paths[path][i] = idx != -1 ? item_path.substr(idx + 1) : item_path;
    });

    console.dir(_paths);
    enable_toolbar_buttons(path);
  }

  function enable_toolbar_buttons(path) {
    var idx = path.lastIndexOf("/");
    var parent_path = "";
    if (idx != -1) {
      parent_path = path.substring (0, idx);
    }
    console.log("path: " + path + "; parent_path: " + parent_path);
    if (path != "") {
      $(".wc-toolbar .ui-icon-eject").parent("a").removeClass("ui-state-disabled");
    } else {
      $(".wc-toolbar .ui-icon-eject").parent("a").addClass("ui-state-disabled");
    }
    $(".wc-toolbar .ui-icon-eject").attr("ref", parent_path);
  }
 })(jQuery);
