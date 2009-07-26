<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $item = $theme->item;
  $metaTags = "";
  if (count($tags) > 0) {
    for ($counter=0; $counter<count($tags); $counter++) {
      if ($counter < count($tags)-1) {
        $metaTags = $metaTags . p::clean($tags[$counter]->name) . ",";
      } else {
        $metaTags = $metaTags . p::clean($tags[$counter]->name);
      }
    }  
  }
?>
<META NAME="KEYWORDS" CONTENT="<?= $metaTags ?>">
<META NAME="DESCRIPTION" CONTENT="<?= nl2br(p::purify($item->description)) ?>">
