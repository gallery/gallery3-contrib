var module_success = function(data) {
  $("#g-developer-admin").append('<div id="g-module-progress" style="margin-top: 1em;"></div>');
  $("#g-module-progress").progressbar();

  var task = data.task;
  var url = data.url;
  var done = false;
  var counter = 0;
  var max_iterations = data.max_iterations;
  while (!done) {
    $.ajax({async: false,
      success: function(data, textStatus) {
        $("#g-module-progress").progressbar("value", data.task.percent_complete);
        done = data.task.done;
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        done = true;
      },
      dataType: "json",
      type: "POST",
      url: url
    });
    // Leave this in as insurance that we never run away
    done = done || ++counter > max_iterations;
  }
  document.location.reload();
};

function ajaxify_developer_form(selector, success) {
  $(selector).ajaxForm({
    dataType: "json",
    success: function(data) {
      if (data.form && data.result != "started") {
        $(selector).replaceWith(data.form);
        ajaxify_developer_form(selector, success);
      }
      if (data.result == "started") {
        success(data);
      }
    }
  });
}
