<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-pdf-admin" class="g-block ui-helper-clearfix">
  <h1> <?= t("PDF settings") ?> </h1>
  <p>
    <?= t("This module adds PDF support to Gallery, namely by:") ?>
  </p>
  <p>
    <b><?= t("Enabling PDF as a valid file type") ?></b>: <?= t("PDF is included as a \"movie\" type, allowing uploads as normal with the uploader, server_add, etc.  This requires that movie uploads be allowed (see <a href=\"%admin_movies_url\">here</a>).",
                                                                array("admin_movies_url" => "movies")) ?><br/>
    <b><?= t("Showing PDF in resize view") ?></b>: <?= t("Instead of showing the resize image (or movie), an embedded view of the PDF is shown.") ?><br/>
    <b><?= t("Generating thumbnails") ?></b>: <?= t("JPG thumbnails are created from the first page of the PDF.  This requires Ghostscript to be installed.  If Ghostscript is unavailable or disabled below, a PDF icon is used instead.") ?><br/>
  </p>
  <p>
    <?= t("Although popular, Ghostscript is not installed on all Linux systems.") ?>
    <?= t("To use Ghostscript without fully installing it, download a pre-compiled version from one of the links <a href=\"%url\">here</a>.", array("url" => "http://www.ghostscript.com/download/gsdnld.html")) ?>
    <?= t("Then, rename the binary file to \"gs\" and put it in Gallery's \"bin\" directory (e.g. \"/gallery3/bin\"), where Gallery will auto-detect it.") ?>
  </p>

  <div class="g-available">
    <h2> <?= t("Current Ghostscript configuration") ?> </h2>
    <div id="g-gs" class="g-block">
      <img class="logo" width="100" height="100" src="<?= url::file("modules/pdf/images/ghostscript.png") ?>" alt="<? t("Visit the Ghostscript project site") ?>" />
      <p>
        <?= t("Ghostscript is an interpreter for the PostScript language and for PDF.") ?><br/>
        <?= t("Please refer to the <a href=\"%url\">Ghostscript website</a> for more information.", array("url" => "http://www.ghostscript.com/")) ?>
      </p>
      <div class="g-module-status g-info">
        <? if ($gs_dir): ?>
          <? if ($gs_version): ?>
            <p><?= t("%version was found in %dir", array("version" => $gs_version, "dir" => $gs_dir)) ?></p>
          <? else: ?>
            <p><?= t("Ghostscript (of unknown version) was found in %dir", array("dir" => $gs_dir)) ?></p>
          <? endif ?>
        <? else: ?>
          <p><?= t("We could not locate Ghostscript on your system.") ?></p>
        <? endif ?>
      </div>
    </div>
  </div>

  <?= $form ?>
</div>
