/**
 * rWatcher Edit:  This file is one of mine.
 *
 */
$("document").ready(function() {
  var original_url = document.getElementById('g-videos-full-url');
  $("#g-movie").replaceWith("<div id=\"g-movie\" class=\"ui-helper-clearfix\"><center><a href=\"" + original_url.href + "\">Click Here to Download Video.</a></center></div>");
});
