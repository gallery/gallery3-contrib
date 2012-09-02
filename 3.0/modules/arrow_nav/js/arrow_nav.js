$(document).keydown(function(e) {
  var url;
  
  if (e.keyCode == 37) {
    // Left key pressed
    url = $('.g-paginator .g-first a').attr("href");
    // Right key pressed
  } else if (e.keyCode == 39) {
    url = $('.g-paginator .g-text-right a').attr("href");
  }
  
  if(url != undefined) {
      window.location = url;
      return false;
  }
});
