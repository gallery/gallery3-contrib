// Javascript originally by Patrick Griffiths and Dan Webb.
// http://htmldog.com/articles/suckerfish/dropdowns/

sfHover = function() {
  var sfEls = document.getElementById("gSiteMenu").getElementsByTagName("ul")[0].getElementsByTagName("li");
  if (!sfEls) { return; }

  for (var i=0; i<sfEls.length; i++) {
    sfEls[i].onmouseover=function() { this.className+=" hover"; }
    sfEls[i].onmouseout=function()  { this.className=this.className.replace(new RegExp(" hover\\b"), ""); }
  }
}

if (window.attachEvent) window.attachEvent("onload", sfHover);
