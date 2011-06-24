<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript" src="<?= url::file("lib/swfobject.js") ?>"></script>
<script type="text/javascript" src="<?= url::file("lib/uploadify/jquery.uploadify.min.js") ?>"></script>
<script type="text/javascript">
  $("#g-admin-themeroller").ready(function() {
     $("#g-themeroller-zip").uploadify({
       'uploader'       : '<?= url::file("lib/uploadify/uploadify.swf") ?>',
       'script'         : '<?= url::site($action) ?>',
       'cancelImg'      : '<?= url::file("lib/uploadify/cancel.png") ?>',
       'fileExt'        : '*.zip',
         scriptData: <?= json_encode($script_data) ?>,
       'fileDesc'       : <?= t("Archive file")->for_js() ?>,
       'auto'           : true,
       'multi'          : false,
         'wmode'    : 'transparent',
        hideButton: true, /* should be true */
     });

     $("#g-themeroller-zipUploader").css({height: '25px', width: '70px', position: 'absolute'});
  });
</script>
<div id="g-admin-themeroller">
  <h1><?= t("Generate theme") ?></h1>
  <?= form::open($action, array("method" => "post", "id" => "g-themeroller-form")) ?>
    <fieldset>
      <ul>
        <li><?= access::csrf_form_field() ?></li>
        <? if (!$is_writable): ?>
        <li class="g-error">
           <?= t("The theme directory is not writable. Please ensure that it is writable by the web server") ?>
        </li>
        <? endif ?>
        <li <? if (!empty($errors["name"])): ?> class="g-error"<? endif ?>>
          <?= form::label("name", t("Name")) ?>
          <?= form::input("name", $form["name"]) ?>
          <? if (!empty($errors["name"]) && $errors["name"] == "required"): ?>
            <p class="g-error"><?= t("Theme name is required") ?></p>
          <? endif ?>
          <? if (!empty($errors["name"]) && $errors["name"] == "module_exists"): ?>
            <p class="g-error"><?= t("Theme exists") ?></p>
          <? endif ?>
        </li>
        <li <? if (!empty($errors["display_name"])): ?> class="g-error"<? endif ?>>
          <?= form::label("display_name", t("Display name")) ?>
          <?= form::input("display_name", $form["display_name"]) ?>
          <? if (!empty($errors["display_name"]) && $errors["display_name"] == "required"): ?>
            <p class="g-error"><?= t("Theme display_name is required")?></p>
          <? endif ?>
        </li>
        <li <? if (!empty($errors["description"])): ?> class="g-error"<? endif ?>>
          <?= form::label("description", t("Description")) ?>
          <?= form::textarea("description", $form["description"]) ?>
          <? if (!empty($errors["description"]) && $errors["description"] == "required"): ?>
            <p class="g-error"><?= t("Theme description is required")?></p>
          <? endif ?>
        </li>
        <li>
          <?= form::label("is_admin", t("Generate an admin theme")) ?>
          <?= form::checkbox("is_admin", "", !empty($form["is_admin"])) ?>
        </li>
        <li>
          <?= form::label("zip_file", t("Upload and generate theme")) ?>
          <br />
          <?= form::upload(array("name" => "zip_file",
                                 "id" => "g-themeroller-zip",
                                 "accept" => "application/zip, multipart/x-zip")) ?>
          <span style="z-index: 1">
          <button type="submit"
               id="g-generate-theme"
               class="<?= $submit_class ?>"
               <? if ($not_writable): ?> disabled<? endif ?>>
            <?= t("Generate") ?>
          </button>
          </span>
        </li>
      </ul>
    </fieldset>
  </form>
</div>
