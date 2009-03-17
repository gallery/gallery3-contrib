var module_success = function(data) {
  $("#gDeveloperAdmin").append('<div id="gModuleProgress" style="margin-top: 1em;"></div>');
  $("#gModuleProgress").progressbar();

  var task = data.task;
  var url = data.url;
  var done = false;
  var counter = 0;
  while (!done) {
    $.ajax({async: false,
      success: function(data, textStatus) {
        $("#gModuleProgress").progressbar("value", data.task.percent_complete);
        done = data.task.done;
      },
      dataType: "json",
      type: "POST",
      url: url
    });
    // Leave this in as insurance that we never run away
    done = done || ++counter > 12;
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
