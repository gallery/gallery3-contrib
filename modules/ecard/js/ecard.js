$("document").ready(function() {
  $("#g-add-ecard").click(function(event) {
    event.preventDefault();
    if (!$("#g-ecard-form").length) {
      $.get($(this).attr("href"),
	    {},
	    function(data) {
	      $("#g-ecard-detail").append(data);
	      ajaxify_ecard_form();
        $.scrollTo("#g-ecard-form-anchor", 800);
	    });
    }
  });
  $(".g-no-ecards a").click(function(event) {
    event.preventDefault();
    if (!$("#g-ecard-form").length) {
      $.get($(this).attr("href"),
	    {},
	    function(data) {
	      $("#g-ecard-detail").append(data);
	      ajaxify_ecard_form();
	    });
      $(".g-no-ecards").remove();
    }
  });
});

function ajaxify_ecard_form() {
  $("#g-ecards form").ajaxForm({
    dataType: "json",
    success: function(data) {
      if (data.result == "success") {
        $("#g-ecards #g-ecard-detail ul").append(data.view);
        $("#g-ecards #g-ecard-detail ul li:last").effect("highlight", {color: "#cfc"}, 8000);
        $("#g-ecard-form").hide(2000).remove();
        $("#g-no-ecards").hide(2000);
      } else {
        if (data.form) {
          $("#g-ecards form").replaceWith(data.form);
          ajaxify_ecard_form();
        }
      }
    }
  });
}
