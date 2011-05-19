/**
 * rWatcher Edit:  This file used to be server_add.js from server_add module.
 * All occurences of server-add have been replaced with videos
 *
 */
(function($) {
   $.widget("ui.gallery_videos",  {
     _init: function() {
       var self = this;
       $("#g-videos-add-button", this.element).click(function(event) {
         event.preventDefault();
         $(".g-progress-bar", this.element).
           progressbar().
           progressbar("value", 0);
         $("#g-videos-progress", this.element).slideDown("fast", function() { self.start_add(); });
       });
       $("#g-videos-pause-button", this.element).click(function(event) {
         self.pause = true;
         $("#g-videos-pause-button", this.element).hide();
         $("#g-videos-continue-button", this.element).show();
       });
       $("#g-videos-continue-button", this.element).click(function(event) {
         self.pause = false;
         $("#g-videos-pause-button", this.element).show();
         $("#g-videos-continue-button", this.element).hide();
         self.run_add();
       });
       $("#g-videos-close-button", this.element).click(function(event) {
         $("#g-dialog").dialog("close");
         window.location.reload();
       });
       $("#g-videos-tree span.g-directory", this.element).dblclick(function(event) {
         self.open_dir(event);
       });
       $("#g-videos-tree span.g-file, #g-videos-tree span.g-directory", this.element).click(function(event) {
         self.select_file(event);
       });
       $("#g-videos-tree span.g-directory", this.element).dblclick(function(event) {
         self.open_dir(event);
       });
       $("#g-dialog").bind("dialogclose", function(event, ui) {
         window.location.reload();
       });
     },

     taskURL: null,
     pause: false,

     start_add: function() {
       var self = this;
       var paths = [];
       $.each($("span.selected", self.element), function () {
	 paths.push($(this).attr("ref"));
       });

       $("#g-videos-add-button", this.element).hide();
       $("#g-videos-pause-button", this.element).show();

       $.ajax({
         url: START_URL,
         type: "POST",
         async: false,
         data: { "paths[]": paths },
         dataType: "json",
         success: function(data, textStatus) {
           $("#g-status").html(data.status);
           $(".g-progress-bar", self.element).progressbar("value", data.percent_complete);
           self.taskURL = data.url;
           setTimeout(function() { self.run_add(); }, 25);
         }
       });
       return false;
     },

     run_add: function () {
       var self = this;
       $.ajax({
         url: self.taskURL,
         async: false,
         dataType: "json",
         success: function(data, textStatus) {
           $("#g-status").html(data.status);
           $(".g-progress-bar", self.element).progressbar("value", data.percent_complete);
           if (data.done) {
         $("#g-videos-progress", this.element).slideUp();
             $("#g-videos-add-button", this.element).show();
             $("#g-videos-pause-button", this.element).hide();
             $("#g-videos-continue-button", this.element).hide();
           } else {
             if (!self.pause) {
               setTimeout(function() { self.run_add(); }, 25);
             }
           }
         }
       });
     },

     /**
      * Load a new directory
      */
     open_dir: function(event) {
       var self = this;
       var path = $(event.target).attr("ref");
       $.ajax({
         url: GET_CHILDREN_URL.replace("__PATH__", path),
         success: function(data, textStatus) {
           $("#g-videos-tree", self.element).html(data);
           $("#g-videos-tree span.g-directory", self.element).dblclick(function(event) {
             self.open_dir(event);
           });
           $("#g-videos-tree span.g-file, #g-videos-tree span.g-directory", this.element).click(function(event) {
             self.select_file(event);
           });
         }
       });
     },

     /**
      * Manage file selection state.
      */
     select_file:  function (event) {
       $(event.target).toggleClass("selected");
       if ($("#g-videos span.selected").length) {
         $("#g-videos-add-button").enable(true).removeClass("ui-state-disabled");
       } else {
         $("#g-videos-add-button").enable(false).addClass("ui-state-disabled");
       }
     }
  });
})(jQuery);
