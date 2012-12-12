<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>   
  @import "<?= url::file("modules/tag_cloud_html5/css/admin_tag_cloud_html5.css"); ?>";
</style>
<div id="g-tag-cloud-html5-admin">
  <h2>
    <?= t("Tag cloud HTML5 settings") ?>
  </h2>
  <p>
    <b><?= t("Underlying JS libraries:") ?></b><br/>
    <?= "1. <a href='http://www.goat1000.com/tagcanvas.php'>TagCanvas</a>: ".t("a non-flash, HTML5-compliant jQuery plugin.") ?><br/>
    <?= "2. <a href='http://excanvas.sourceforge.net'>excanvas</a>: ".t("maintains canvas compatibility with pre-9.0 Internet Explorer, and does not load if not needed.") ?><br/>
    <?= t("The module author, Shad Laws, has modified TagCanvas to add a physics-based model for motion and some extra parameters.") ?>
    <?= t("Although this module loads a minified version of the JS library, the full-sized one is included in the JS directory for reference.") ?>
  </p>
  <p>
    <b><?= t("How sizing works in TagCanvas:") ?></b><br/>
    <?= "1. ".t("make a square the size of the minimum of width and height (as determined by width and height parameters)") ?><br/>
    <?= "2. ".t("scale result by the stretch factors, possibility resulting in a non-square shape") ?><br/>
    <?= "3. ".t("set text into result at defined text height") ?><br/>
    <?= "4. ".t("scale result by the zoom, scaling both cloud and text height (e.g. text height 12 and zoom 1.25 results in 15pt font)") ?>
  </p>
  <p>
    <b><?= t("Legend:") ?></b><br/>
    <?= t("&nbsp;&nbsp;&nbsp;option name (more info on option) {related TagCanvas parameters}") ?><br/>
    <?= t("More details on TagCanvas parameters given at TagCanvas's homepage or in the above-mentioned JS library.") ?>
  </p>
  <?= $form ?>
</div>
