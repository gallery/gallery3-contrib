(function($) {
  var _paths = [];

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

    $("#center a.wc-child-link").live("dblclick", function(event) {
      event.preventDefault();
      event.stopPropagation();
      var path = $(this).parents("li:first").attr("ref");
      var url = $(this).attr("href");
      get_detail(path, _set_active_album);
      return false;
    });

    $("#center a.wc-child-link").live("click", function(event) {
      $(".wc-thumb-grid-cell.ui-selected").removeClass("ui-selected");
      $(this).parents("li:first").addClass("ui-selected");
      _set_navigation_buttons($(".wc-thumb-grid-cell.ui-selected").attr("ref"));
      return false;
    });

    $(".wc-button").live("click", function() {
      if ($(this).hasClass("ui-state-disabled")) {
        return false;
      }
      switch ($(this).attr("ref")) {
      case "parent":
        get_detail($("span", this).attr("ref"), _set_active_album);
        break;
      case "first":
      case "previous":
      case "next":
      case "last":
        if (resource_type != "album") {
          get_detail($("span", this).attr("ref"));
        } else {
          var selected = $(".wc-thumb-grid-cell.ui-selected");
          selected.removeClass("ui-selected");
          switch ($(this).attr("ref")) {
          case "first":
            $(".wc-thumb-grid-cell:first").addClass("ui-selected");
            break;
          case "previous":
            selected.prev().addClass("ui-selected");
            break;
          case "next":
            selected.next().addClass("ui-selected");
            break;
          case "last":
            $(".wc-thumb-grid-cell:last").addClass("ui-selected");
            break;
          }
          _set_navigation_buttons();
        }
        break;
      default:
        console.group("process toolbar button click: " + $(this).attr("ref"));
        console.log(($("span", this).attr("ref")));
        console.groupEnd();
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
    save_paths();
  }

  function _set_active_album(path) {
    var album = $("#album_tree [ref='" + path + "']");
    if (album.length > 0) {
      $(".tree-title.ui-selected").removeClass("ui-selected");
      $(".tree-title:first", album).addClass("ui-selected");
    }
  }

  function set_selected_thumb() {
     if ($(".wc-thumb-grid-cell.ui-selected").length == 0) {
       $(".wc-thumb-grid-cell:first").addClass("ui-selected");
     }
  }

  function get_detail(path, callback) {
    $.get("/g3_client/index.php/g3_client/detail", {path: path}, function(data, textStatus) {
      $("#wc-detail").html(data);
      set_selected_thumb();
      save_paths();
      if (callback) {
        callback(path);
      }
    });
  }

  function save_paths() {
    _paths[current_path] = [];
    $(".wc-thumb-grid-cell").each(function(i) {
      _paths[current_path][i] = $(this).attr("ref");
    });

    _set_navigation_buttons();
  }

  function _set_navigation_buttons() {
    if (current_path != "") {
      $(".wc-toolbar .ui-icon-eject").parent("a").removeClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-trash").parent("a").removeClass("ui-state-disabled");
    } else {
      $(".wc-toolbar .ui-icon-eject").parent("a").addClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-trash").parent("a").addClass("ui-state-disabled");
    }
    $(".wc-toolbar .ui-icon-eject").attr("ref", parent_path);
    $(".wc-toolbar .ui-icon-pencil").attr("ref", current_path);
    $(".wc-toolbar .ui-icon-trash").attr("ref", current_path);
    $(".wc-toolbar #add-resource span")
      .attr("ref", resource_type == "album" ? current_path : parent_path);

    var paths = _paths[resource_type == "album" ?  current_path : parent_path];

    $(".wc-toolbar .ui-icon-pencil").attr("ref", current_path);
    if (paths.length > 0) {
      $(".wc-toolbar .ui-icon-seek-first").parent("a").removeClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-end").parent("a").removeClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-first").attr("ref", paths[0]);
      $(".wc-toolbar .ui-icon-seek-end").attr("ref", paths[paths.length - 1]);
    } else {
      $(".wc-toolbar .ui-icon-seek-first").parent("a").addClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-end").parent("a").addClass("ui-state-disabled");
    }

    var selected_path =
      resource_type == "album" ? $(".wc-thumb-grid-cell.ui-selected").attr("ref") : current_path;
    var i = 0;
    for (; i < paths.length; i++) {
      if (paths[i] == selected_path) {
        break;
      }
    }

    if (i > 0) {
      $(".wc-toolbar .ui-icon-seek-prev").parent("a").removeClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-prev").attr("ref", paths[i - 1]);
    } else {
      $(".wc-toolbar .ui-icon-seek-first").parent("a").addClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-prev").parent("a").addClass("ui-state-disabled");
    }
    if (i < paths.length - 1) {
      $(".wc-toolbar .ui-icon-seek-next").parent("a").removeClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-next").attr("ref", paths[i + 1]);
    } else {
      $(".wc-toolbar .ui-icon-seek-next").parent("a").addClass("ui-state-disabled");
      $(".wc-toolbar .ui-icon-seek-end").parent("a").addClass("ui-state-disabled");
    }
  }
})(jQuery);
