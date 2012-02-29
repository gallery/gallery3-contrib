<?php defined("SYSPATH") or die("No direct script access.") ?>
<select onchange="window.location=this.value">
<?
function makeselect($album, $level){
//print out the list item as a select option
?>
  <option value="<?= item::root()->url() ?><?= $album->relative_url() ?>"><?= str_repeat("&nbsp;&nbsp;", $level) ?><?= html::purify($album->title) ?></option>
<?
  //recurse over the children, and print their list items as well
  foreach ($album->viewable()->children(null, null, array(array("type", "=", "album"))) as $child){
    makeselect($child,$level+1);
  }
}
makeselect($root,0);
?>
</select>
