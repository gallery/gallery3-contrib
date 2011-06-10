<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-rawphoto-admin">
  <h2> <?= t("Raw Photos") ?> </h2>
  <?= form::open("admin/rawphoto/saveprefs") ?>
  <?= access::csrf_form_field() ?>
  <p><?= t('<a href="%raw_url">Raw photo</a> processing depends on the ' .
           '<a href="%dcraw_url"><em>dcraw</em> tool</a>, which must be installed separately. ' .
           'It also depends on either the ImageMagick or GraphicsMagick graphics toolkits.',
           array("raw_url" =>
                 "http://www.adamcoupe.com/whitepapers/photography_technique_benefits_of_shooting_in_raw.htm",
                 "dcraw_url" => "http://www.cybercom.net/~dcoffin/dcraw/")) ?></p>
  <? if (!$is_keeporiginal_active): ?>
      <p class="g-module-status g-warning g-block">
        <?= t('The photos will not be preserved in their raw format unless you ' .
              '<a href="%modules_url">activate the <em>Keep Original</em> module</a>.',
              array("modules_url" => url::site("admin/modules"))) ?>
      </p>
  <? endif; ?>
  <fieldset>
    <legend><?= t("Paths") ?></legend>
    <? if ($dcraw->installed): ?>
      <p><?= t("The <em>dcraw</em> tool was detected at <code>%path</code> " .
               "with version <code>%version</code>.",
               array("path" => $dcraw->path, "version" => $dcraw->version)) ?></p>
    <? else: ?>
      <p class="g-module-status g-error g-block"><?= $dcraw->error ?></p>
    <? endif; ?>
    <? if ($toolkit_name == "none"): ?>
      <p class="g-module-status g-error g-block">
        <?= t('No suitable graphics toolkit was detected. ' .
              '<a href="%activate_url">Activate</a> either ImageMagick or GraphicsMagick.',
              array("activate_url" => url::site("admin/graphics"))) ?>
      </p>
    <? else: ?>
      <p><?= t("The %toolkit_name graphics toolkit was detected.",
               array("toolkit_name" => $toolkit_name)) ?></p>
    <? endif; ?>
    <? if (!empty($errors["IccPath"])): ?>
      <p class="g-module-status g-error g-block"><?= $errors["IccPath"] ?></p>
    <? endif; ?>
    <?= form::label("IccPath", t('Path to <a href="%icc_url">ICC profile</a>', array("icc_url" =>
          "http://www.permajet.com/30/Downloads/76/What_are_ICC_Profiles,_and_why_do_I_need_them.html"))) ?>
    <?= form::input(array("name" => "IccPath", "id" => "IccPath"), $icc_path) ?>
    <?if (empty($icc_path) || !empty($errors["IccPath"])): ?>
      <em>An ICC profile is optional. If you don't know what it is, then you don't need it.</em>
    <? endif; ?>
  </fieldset>
  <?= form::submit("SavePrefs", "Save") ?>
  </form>
</div>
