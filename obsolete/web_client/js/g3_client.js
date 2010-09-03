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

    $("#wc-choose-resource").live("click", function(event){
      event.preventDefault();
      event.stopPropagation();
      if ($("#wc-resource-list:visible").length) {
        $("#wc-resource-list").hide();
      } else {
        var parent = $(this).parent("div");
        var width = parent.width();
        var height = parent.height();
        var top = parent.position().top;
        var current_path = $("#wc-add-resource").attr("ref");
        $("#wc-resource-list li[ref='" + current_path + "']").addClass("ui-selected");
        $("#wc-resource-list")
          .css({"top": (top + height - 5) + "px", "width": width + "px"})
          .show();
      }
      return false;
    });

    $("#wc-resource-list").live("click", function(event) {
      var ref = $(event.originalTarget).attr("ref");
      var text = $(event.originalTarget).text();
      $("#wc-add-resource span").text(text);
      $("#wc-add-resource").attr("ref", ref);
      $("#wc-resource-list").hide();
      $("#wc-resource-list li.ui-selected").removeClass("ui-selected");
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

      var action = $(this).attr("ref");
      switch (action) {
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
      case "edit":
      case "delete":
        _open_dialog(action + "_" + resource_type, $("span", this).attr("ref"));
        break;
      default:
        if (/^add_.*/.test(action)) {
          _open_dialog(action, $("span", this).attr("ref"));
        } else {
          console.group("process toolbar button click: " + $(this).attr("ref"));
          console.log(($("span", this).attr("ref")));
          console.groupEnd();
        }
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

    $("#wc-tag-cloud li a", obj).live("click", function (event) {
      $.get($(this).attr("href"), function(data, textStatus) {
        $("#wc-detail").html(data);
        set_selected_thumb();
        save_paths();
      });
      return false;
    });

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

  function _open_dialog(dialog, resource_path) {
    $("body").append('<div id="g-dialog"></div>');
    $("#g-dialog").dialog({
      model: true,
      resizable: false,
      position: "center",
      close: function() {
        $("#g-dialog").dialog("destroy").remove();
      }
    });
    $.getJSON("/g3_client/index.php/" + dialog, {path: resource_path}, function(data) {
      $("#g-dialog").html(data.form);

      if ($("#g-dialog fieldset legend").length) {
        $("#g-dialog").dialog('option', 'title', $("#g-dialog fieldset legend:eq(0)").html());
      }
      _ajaxifyDialog();

      $("#g-dialog").dialog("open");

    });
  }

  function _ajaxifyDialog() {
    if ($("#g-dialog form").length) {
      $("#g-dialog form").ajaxForm({
        dataType: "json",
        beforeSubmit: function(formData, form, options) {
          form.find(":submit, :reset")
            .addClass("ui-state-disabled")
            .attr("disabled", "disabled");
          return true;
        },
        success: function(data) {
          if (data.form) {
            $("#g-dialog form").replaceWith(data.form);
            $("#g-dialog form :submit").removeClass("ui-state-disabled")
              .attr("disabled", null);
            _ajaxifyDialog();
          }
          if (data.result == "success") {
            $("#g-dialog").dialog('close');
            get_detail(data.path, _set_active_album);
            if (data.type == "album") {
              var path = data.path;
              $.get("/g3_client/index.php/g3_client/albums",
                {path: path},
                 function(data, textStatus) {
                   var selector = "#album_tree li[ref=" + path + "]";
                   $(selector).replaceWith(data);
                   $(selector + " .tree-title:first").addClass("ui-selected");
                 });
            }
          } else if (data.result == "fail") {
            $("#g-dialog").dialog('close');
            alert(data.message);
          }
        }
      });
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
      //$(".wc-toolbar .ui-icon-trash").parent("a").removeClass("ui-state-disabled");
    } else {
      $(".wc-toolbar .ui-icon-eject").parent("a").addClass("ui-state-disabled");
      //$(".wc-toolbar .ui-icon-trash").parent("a").addClass("ui-state-disabled");
    }
    $(".wc-toolbar .ui-icon-eject").attr("ref", parent_path);
    $(".wc-toolbar .ui-icon-pencil").attr("ref", current_path);
    $(".wc-toolbar #wc-add-resource span")
      .attr("ref", resource_type == "album" ? current_path : parent_path);

    var paths = _paths[resource_type == "album" ? current_path : parent_path];

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

    $(".wc-toolbar .ui-icon-trash").attr("ref", selected_path);

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
