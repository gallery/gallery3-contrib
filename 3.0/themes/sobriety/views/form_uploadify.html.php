<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript" src="<?= url::file("lib/swfobject.js") ?>"></script>
<script type="text/javascript" src="<?= url::file("lib/uploadify/jquery.uploadify.min.js") ?>"></script>
<script type="text/javascript">
  var allBytesTotal = 0;

  function humanSize(size) {
    if( size > 1000000000000 ) {
      size = Math.round(size / 10000000000) / 100;
      unit = "TB";

    } else if( size > 1000000000 ) {
      size = Math.round(size / 10000000) / 100;
      unit = "GB";

    } else if( size > 1000000 ) {
      size = Math.round(size / 10000) / 100;
      unit = "MB";

    } else if( size > 1000 ) {
      size = Math.round(size / 1000);
      unit = "KB";
    } else {
      unit = "B";
    }

    return size+" "+unit;
  }
  function humanTime(time) {
    if( time >= 60*60 ) {
      time = Math.round(time/60/60);
      unit = "heure";
    } else if( time >= 60 ) {
      time = Math.round(time/60);
      unit = "minute";
    } else {
      unit = "seconde"
    }
    
    if( time > 1 ) unit += "s";

    return time+" "+unit;
  }

  $("#g-add-photos-canvas-sd").ready(function () {
    $("#g-uploadify-sd").uploadify({
      width: 150,
      height: 33,
      uploader: "<?= url::file("lib/uploadify/uploadify.swf") ?>",
      script: "<?= url::site("simple_uploader/add_photo/{$album->id}") ?>",
      scriptData: <?= json_encode($script_data) ?>,
      fileExt: "*.gif;*.jpg;*.jpeg;*.png;*.flv;*.mp4;*.m4v;*.GIF;*.JPG;*.JPEG;*.PNG;*.FLV;*.MP4;*.M4V",
      fileDesc: <?= t("Photos and movies")->for_js() ?>,
      cancelImg: "<?= url::file("lib/uploadify/cancel.png") ?>",
      simUploadLimit: <?= $simultaneous_upload_limit ?>,
      wmode: "transparent",
      hideButton: true, /* should be true */
      auto: true,
      multi: true,

      /*onClearQueue: function(event) {
        $("#g-upload-cancel-all")
          .addClass("ui-state-disabled")
          .attr("disabled", "disabled");
        return true;
      },

      onError: function(event, queueID, fileObj, errorObj) {
        var msg = " - ";
        if (errorObj.type == "HTTP") {
          if (errorObj.info == "500") {
            msg += <?= t("Unable to process this file")->for_js() ?>;
            // Server error - check server logs
          } else if (errorObj.info == "404") {
            msg += <?= t("The upload script was not found.")->for_js() ?>;
            // Server script not found
          } else {
            // Server Error: status: errorObj.info
            msg += (<?= t("Server error: __INFO__")->for_js() ?>.replace("__INFO__", errorObj.info));
          }
        } else if (errorObj.type == "File Size") {
          var sizelimit = $("#g-uploadify").uploadifySettings(sizeLimit);
          msg += fileObj.name+' '+errorObj.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB';
        } else {
          msg += (<?= t("Server error: __INFO__ (__TYPE__)")->for_js() ?>
            .replace("__INFO__", errorObj.info)
            .replace("__TYPE__", errorObj.type));
        }
        $("#g-add-photos-status ul").append(
          "<li class=\"g-error\">" + fileObj.name + msg + "</li>");
        $("#g-uploadify" + queueID).remove();
      },*/

      onSelectOnce: function(event, data) {
        allBytesTotal = data.allBytesTotal;
        $("#g-add-photos-allBytes").text(humanSize(allBytesTotal));
        $("#g-add-photos-current-eta").text( "<?= t("calculating...") ?>" );

        $("#g-add-photos-progressbar").removeClass("stop");
        $("#g-upload-cancel-all").removeClass("ui-state-disabled").attr("disabled", null);
        return true;
      },

      onProgress: function(event, queueID, fileObj, data) {
        $("#g-add-photos-allBytesLoaded").text(humanSize(data.allBytesLoaded));
        $("#g-add-photos-progressbar").css("width", Math.floor(data.allBytesLoaded/allBytesTotal * 100)+"%");
        $("#g-add-photos-current-upload").text(fileObj.name);
        $("#g-add-photos-current-rate").text(humanSize(data.speed*1000)+"/s");
        $("#g-add-photos-current-eta").text( humanTime(Math.round((allBytesTotal - data.allBytesLoaded) / (data.speed * 1000))) );
      },

      onAllComplete: function(event, data) {
        $("#g-add-photos-progressbar").addClass("stop");
        $("#g-upload-cancel-all").addClass("ui-state-disabled").attr("disabled", "disabled");

        $("#g-add-photos-current-rate").text(humanSize(data.speed*1000)+"/s");
      },

    });
  });
</script>

<?php
/*
<? if (ini_get("suhosin.session.encrypt")): ?>
<ul id="g-action-status" class="g-message-block">
  <li class="g-error">
    <?= t("Error: your server is configured to use the <a href=\"%encrypt_url\"><code>suhosin.session.encrypt</code></a> setting from <a href=\"%suhosin_url\">Suhosin</a>.  You must disable this setting to upload photos.",
        array("encrypt_url" => "http://www.hardened-php.net/suhosin/configuration.html#suhosin.session.encrypt",
    "suhosin_url" => "http://www.hardened-php.net/suhosin/")) ?>
  </li>
</ul>
<? endif ?>

<div>
  <p>
    <?= t("Photos will be uploaded to album: ") ?>
  </p>
  <ul class="g-breadcrumbs ui-helper-clearfix">
    <? foreach ($album->parents() as $i => $parent): ?>
    <li<? if ($i == 0) print " class=\"g-first\"" ?>> <?= html::clean($parent->title) ?> </li>
    <? endforeach ?>
    <li class="g-active"> <?= html::purify($album->title) ?> </li>
  </ul>
</div>
*/
?>

<div id="g-add-photos-canvas-sd">
  <button id="g-add-photos-button-sd" onclick="return false;"><?= t("Select photos...") ?></button>
  <span id="g-uploadify-sd"></span>
</div>

<div id="g-add-photos-progress">
  <ul>
    <li id="g-add-photos-progress-text"><?= t("Uploaded:") ?> <span id="g-add-photos-allBytesLoaded">0 KB</span> <?= t("of") ?> <span id="g-add-photos-allBytes">0 KB</span></li>
    <li id="g-add-photos-progressbar-frame"><span id="g-add-photos-progressbar"></span></li>
    <li><?= t("Uploading:") ?> <span id="g-add-photos-current-upload">n/a</span></li>
    <li><?= t("Upload rate:") ?> <span id="g-add-photos-current-rate">n/a</span>, <?= t("Estimated time remaining:") ?> <span id="g-add-photos-current-eta">n/a</span></li>
  </ul>
</div>
<?php
/*
<div id="g-add-photos-status">
  <ul id="g-action-status" class="g-message-block">
  </ul>
</div>
*/
?>
