/**
*
* Copyright (c) 2010 Serguei Dosyukov, http://blog.dragonsoft.us
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
* modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
* LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
* IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
*/

$.fn.KbdNavigation = function(options, callback) {
  
  this.options = options || {};
  var opt = this.options;
  this.callback = callback || null;
  var clbk = this.callback;

  $(this).bind("keydown", function(event) {
    if ($('#sb-body-inner>img#sb-content').is(':visible')) {
      return false;
    }
    // ignore shortcuts when inside a jQuery dialog; otherwise it becomes impossible
    // to navigate the cursor inside an input box
    if ($('.ui-widget-overlay').is(':visible')) {
      return true;
    }

    var direction = "ltr";
    if (document.body) {
      if (window.getComputedStyle) {
        direction = window.getComputedStyle(document.body, null).direction;
      } else if (document.body.currentStyle) {
        direction = document.body.currentStyle.direction;
      }
    }

    var lnk = "";
    var lnk_first, lnk_prev, lnk_parent, lnk_next, lnk_last;

    if(opt.first)  { lnk_first  = opt.first;  } else { lnk_first  = $("#g-navi-first").attr("href");  }
    if(opt.prev)   { lnk_prev   = opt.prev;   } else { lnk_prev   = $("#g-navi-prev").attr("href");   }
    if(opt.parent) { lnk_parent = opt.parent; } else { lnk_parent = $("#g-navi-parent").attr("href"); }
    if(opt.next)   { lnk_next   = opt.next;   } else { lnk_next   = $("#g-navi-next").attr("href");   }
    if(opt.last)   { lnk_last   = opt.last;   } else { lnk_last   = $("#g-navi-last").attr("href");   }

    // Support for standard Wind Theme tags
    if(!lnk_first) { lnk_first = $(".g-paginator .ui-icon-seek-first").parent().attr("href"); }
    if(!lnk_prev)  { lnk_prev  = $(".g-paginator .ui-icon-seek-prev").parent().attr("href"); }
    if(!lnk_next)  { lnk_next  = $(".g-paginator .ui-icon-seek-next").parent().attr("href");  }
    if(!lnk_last)  { lnk_last  = $(".g-paginator .ui-icon-seek-end").parent().attr("href");  }

    var keyCode = event.keyCode;

    if (direction == "rtl") {
      switch(keyCode) {
        case 0x25: // Left
          keyCode = 0x27;
          break;
        case 0x27: // Right
          keyCode = 0x25;
          break;
      }
    }
    
    switch(keyCode) {
      case 0x25: // Ctr+Left/Left
        if(event.ctrlKey) { lnk = lnk_first; } else { lnk = lnk_prev; }
        break;  
      case 0x26: // Ctrl+Up
        if(event.ctrlKey) { lnk = lnk_parent; }
        break; 
      case 0x27: // Ctrl+Right/Right
        if(event.ctrlKey) { lnk = lnk_last; } else { lnk = lnk_next; }
        break;  
    }
    
    if(lnk) {
      if(typeof clbk == 'function') {
        clbk();
        return false;
      } else {
        window.location = lnk;
        return true;
      }
    }
    
    return true;
  });
}

$(document).ready( function() {
  $(document).KbdNavigation({});
  if ($('#sb-content').is(':visible')) { return true; }
});
