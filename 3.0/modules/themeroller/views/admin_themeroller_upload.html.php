<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript" src="<?= url::file("lib/swfobject.js") ?>"></script>
<script type="text/javascript" src="<?= url::file("lib/uploadify/jquery.uploadify.min.js") ?>"></script>
<script type="text/javascript">
  $("#g-admin-themeroller").ready(function() {
     $("#g-themeroller-zip").uploadify({
       'uploader'       : '<?= url::file("lib/uploadify/uploadify.swf") ?>',
       'script'         : '<?= url::site("admin/themeroller/upload") ?>',
       'cancelImg'      : '<?= url::file("lib/uploadify/cancel.png") ?>',
       'fileExt'        : '*.zip',
        scriptData      : <?= json_encode($script_data) ?>,
       'fileDesc'       : <?= t("Archive file")->for_js() ?>,
       'auto'           : true,
       'multi'          : false,
       fileDataName     : 'zip_file',
       'wmode'    : 'transparent',
       hideButton: true, /* should be true */
       onSelectOnce: function(event, queueID, fileObj) {
         $("#g-themeroller-form").find(":submit")
           .addClass("ui-state-disabled")
           .attr("disabled", "disabled");
       },
       onComplete       : function(event, queueID, fileObj, response, data) {
         $("#g-themeroller-form").submit();
         return false;
       }
     });
     $("#g-themeroller-is-admin").change(function(event) {
       var scriptData = $("#g-themeroller-zip").uploadifySettings("scriptData");
       scriptData.is_admin = $(this).is(":checked") ? 1 : 0;
       $("#g-themeroller-zip").uploadifySettings("scriptData", scriptData);
     });

     $("#g-themeroller-zipUploader").css({height: '40px', width: '70px', position: 'absolute'});
  });
</script>
<div id="g-admin-themeroller">
  <h1><?= t("Upload themeroller archive") ?></h1>
  <?= form::open($action, array("method" => "get", "id" => "g-themeroller-form")) ?>
    <fieldset>
      <ul>
        <li><?= access::csrf_form_field() ?></li>
        <? if (!$is_writable): ?>
        <li class="g-error">
           <?= t("The theme directory is not writable. Please ensure that it is writable by the web server") ?>
        </li>
        <? endif ?>
        <li><span><?= t("Upload and generate theme") ?></span></li>
        <li>
          <?= form::checkbox(array("name" => "is_admin",
                                   "id" => "g-themeroller-is-admin")) ?>
          <?= form::label("is_admin", t("Generate an admin theme")) ?>
        </li>
        <li>
          <?= form::upload(array("name" => "zip_file",
                                 "id" => "g-themeroller-zip",
                                 "accept" => "application/zip, multipart/x-zip")) ?>
          <span style="z-index: 1">
          <button type="submit"
               id="g-generate-theme"
               class="<?= $submit_class ?>"
               <? if ($not_writable): ?> disabled<? endif ?>>
            <?= t("Upload") ?>
          </button>
          </span>
        </li>
      </ul>
    </fieldset>
  </form>
</div>
