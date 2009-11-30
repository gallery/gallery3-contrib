function setupLoginForm() {
  setupAjaxForm('#gLoginForm');
}

function setupAjaxForm($form_id) {
  var options = {
    dataType: "json",
    success: function(data) {
      if (data.result == "success") {
        if (data.location) { window.location = data.location; }
        else { window.parent.Shadowbox.close(); }
      }
    }
  };

  $($form_id).ajaxForm(options);
};
