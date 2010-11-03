var pick_theme = function(name) {
  $.get(PICK_THEME_URL,
        {name: name},
	function() {
	  window.location.reload();
	});
};
