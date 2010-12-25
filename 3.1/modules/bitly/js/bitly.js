$(document).ready(function() {
  $(".g-bitly-shorten").click(function(e) {
    e.preventDefault();
    return window.location = ($(this).attr("href"));
  });
});
