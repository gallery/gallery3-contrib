<?php defined("SYSPATH") or die("No direct script access.") ?>
<? $item = ORM::factory("item", $item_id); ?>

<style>
.jcrop-holder { text-align: left; }

.jcrop-vline, .jcrop-hline
{
  font-size: 0;
  position: absolute;
  background: white url('<?= url::file("modules/tagfaces/images/Jcrop.gif") ?>') top left repeat;
}
.jcrop-vline { height: 100%; width: 1px !important; }
.jcrop-hline { width: 100%; height: 1px !important; }
.jcrop-handle {
  font-size: 1px;
  width: 7px !important;
  height: 7px !important;
  border: 1px #eee solid;
  background-color: #333;
  *width: 9px;
  *height: 9px;
}

.jcrop-tracker { width: 100%; height: 100%; }

.custom .jcrop-vline,
.custom .jcrop-hline
{
  background: yellow;
}
.custom .jcrop-handle
{
  border-color: black;
  background-color: #C7BB00;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
}
</style>

<?= html::script("modules/tagfaces/js/jquery.Jcrop.js") ?>

<script language="Javascript">
  // Remember to invoke within jQuery(window).load(...)
  // If you don't, Jcrop may not initialize properly
  jQuery(document).ready(function(){
    jQuery('#g-item-id-<?=$item->id ?>').Jcrop({
      onChange: showCoords,
      onSelect: showCoords
    });
  });

  // Our simple event handler, called from onChange and onSelect
  // event handlers, as per the Jcrop invocation above
  function showCoords(c) {
    jQuery('#x1').val(c.x);
    jQuery('#y1').val(c.y);
    jQuery('#x2').val(c.x2);
    jQuery('#y2').val(c.y2);
  };
</script>


<?= html::script("modules/tagfaces/js/jquery.Jcrop.js") ?>

<script language="Javascript">
  // Remember to invoke within jQuery(window).load(...)
  // If you don't, Jcrop may not initialize properly
  jQuery(document).ready(function(){
    jQuery('#g-select-photo-id-<?=$item->id ?>').Jcrop({
      onChange: showCoords,
      onSelect: showCoords
    });
  });

  // Our simple event handler, called from onChange and onSelect
  // event handlers, as per the Jcrop invocation above
  function showCoords(c) {
    jQuery('#x1').val(c.x);
    jQuery('#y1').val(c.y);
    jQuery('#x2').val(c.x2);
    jQuery('#y2').val(c.y2);
  };
</script>

<div id="g-select-item">
  <?= $theme->dynamic_top() ?>
  <div id="g-select-photo" align="center">
  <h1><?= html::clean($title) ?></h1>
  <p><?=t("Use the mouse to select a face on the image below."); ?></p>
      <?= $item->resize_img(array("id" => "g-select-photo-id-{$item->id}", "class" => "g-select-resize", "style" => "position: fixed;")) ?>
  </div>
</div>

<style>
#face_title {
  width: 200px;
}
#face_description {
  width: 400px;
}
#x1 {
  width: 40px;
}
#y1 {
  width: 40px;
}
#x2 {
  width: 40px;
}
#y2 {
  width: 40px;
}

li {
  display: inline;
  list-style-type: none;
  float:left;
}
</style>

<div id="g-coordinates">
  <?=$form ?>
</div>

<br/><br/><br/>

<fieldset>
<div id="g-delete-faces">
  <h2><?= t("Delete Existing Faces and Notes") ?></h2>
  <?= $delete_form ?>
</div>
</fieldset>

<br/>

<div id="g-exit-faces">
<p><a href="<?= url::abs_site("{$item->type}s/{$item->id}") ?>"><?= t("Return to photo") ?></a></p>
</div>

<?= $theme->dynamic_bottom() ?>
