<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>   
  @import "<?= url::file("modules/image_optimizer/css/admin_image_optimizer.css"); ?>";
</style>
<div id="g-image-optimizer-admin">
  <h2>
    <?= t("Image optimizer settings") ?>
  </h2>
  <p>
    <?= t("This module uses three underlying toolkits, all performing <b>lossless</b> transformations.")." ".
        t("You can set the paths to each of the three toolkits below.")." ".
        t("By default, this module uses it's own pre-compiled versions, included as libraries.")." ".
        t("Additionally, your server may have copies installed elsewhere (see table below).") ?><br/>
    <table>
      <tr>
        <td></td>
        <td><b><?= t("Name and author") ?></b></td>
        <td><b><?= t("Server-installed path") ?></b></td>
        <td><b><?= t("Configuration status") ?></b></td>
      </tr>
      <tr>
        <td>JPG</td>
        <td><a href='http://jpegclub.org/droppatch.v09.tar.gz'>Jpegtran</a> <?= t("by") ?> <a href='http://jpegclub.org/jpegtran'>Jpegclub</a></td>
        <td><?= $installed_path_jpg ?></td>
        <td><?= $version_jpg ?></td>
      </tr>
      <tr>
        <td>PNG</td>
        <td><a href='http://shanebishop.net/uploads/optipng.tar.gz'>OptiPNG</a> <?= t("by") ?> <a href='http://optipng.sourceforge.net'>Cosmin Truta et al.</a></td>
        <td><?= $installed_path_png ?></td>
        <td><?= $version_png ?></td>
      </tr>
      <tr>
        <td>GIF</td>
        <td><a href='http://shanebishop.net/uploads/gifsicle.tar.gz'>GIFsicle</a> <?= t("by") ?> <a href='http://www.lcdf.org/gifsicle'>LCDF</a></td>
        <td><?= $installed_path_gif ?></td>
        <td><?= $version_gif ?></td>
      </tr>
    </table>
    <?= t("This module was inspired by the WordPress module")." <a href='http://wordpress.org/extend/plugins/ewww-image-optimizer'>EWWW Image Optimizer</a> ".t("and the Gallery3 module")." <a href='http://codex.gallery2.org/Gallery3:Modules:jpegtran'>Jpegtran</a>." ?>
  </p>
  <p>
    <b><?= t("Notes:") ?></b><br/>
    <b>1. <?= t("Remove all meta data") ?></b>: <?= t("recommended for thumb images - 80% size reduction typical, which drastically changes page load speed") ?><br/>
    <b>2. <?= t("Make images progressive/interlaced") ?></b>: <?= t("recommended for resize images - provides preview of photo during download") ?><br/>
    <b>3. <?= t("Conversion") ?></b>: <?= t("recommended for thumb images - converting PNG/GIF to JPG can reduce the size by a huge amount, again helping page load speed") ?><br/>
    <b>4. <?= t("Update mode") ?></b>: <?= t("used to speed up initial rebuild to optimize existing images by deactivating all other graphics rules - MUST ensure that no new images are created while this is enabled (use maintenance mode if needed), and MUST disable once initial rebuild is done.") ?><br/>
    <b>5. <?= t("Windows / Linux") ?></b>: <?= t("the module's lib directory includes both Windows and Linux versions of the toolkits.  For security reasons, Windows should not be used on production sites.   Both versions are provided here to enable development copies to still run Windows without issue.") ?><br/>
  </p>
  <?= $form ?>
</div>