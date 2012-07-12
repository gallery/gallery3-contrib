<?php defined("SYSPATH") or die("No direct script access.") ?>
<style type="text/css">
  ul.treealbumnav {
    height: 225px;
    width: 190px;
    overflow: auto;
    border: 1px solid #666;
    padding: 2px;
  }
</style>

<ul class="treealbumnav">
<?
function makelist($album,$level){
//print out the list item
?>
  <li>
    <a href="<?= item::root()->url() ?><?= $album->relative_url() ?>"><?= str_repeat("&nbsp;&nbsp;", $level) ?><?= html::purify($album->title) ?></a>
  </li>
<?
  //recurse over the children, and print their list items as well
  foreach ($album->viewable()->children(null, null, array(array("type", "=", "album"))) as $child){
    makelist($child,$level+1);
  }
}
makelist($root,0);
?>
  </ul>


