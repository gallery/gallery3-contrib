<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $item = $theme->item;
  $metaTags = "";
  if (count($tags) > 0) {
    for ($counter=0; $counter<count($tags); $counter++) {
      if ($counter < count($tags)-1) {
        $metaTags = $metaTags . html::clean($tags[$counter]->name) . ",";
      } else {
        $metaTags = $metaTags . html::clean($tags[$counter]->name);
      }
    }
  }

  // If $metaTags is empty, use the item's title instead.
  if ($metaTags == "") {
    $metaTags = html::clean($item->title);
  }

  $metaDescription = "";
  $metaDescription = trim(nl2br(html::purify($item->description)));
  // If description is empty, use title instead.
  if ($metaDescription == "") {
    $metaDescription = html::clean($item->title);
  }
  // If this page belongs to a tag, use the description of the first item instead.
  if ($theme->tag()) {
    if (count($children) > 0) {
      $metaDescription = trim(nl2br(html::purify($children[0]->description)));
    }
  }
  // If it's still empty, use $metaTags.
  if ($metaDescription == "") {
    $metaDescription = $metaTags;
  }

  // Strip HTML
  $metaDescription = strip_tags($metaDescription);
  // Strip Line Breaks
  $metaDescription = str_replace("\n", " ", $metaDescription);
  // Limit Description to 150 characters.
  $metaDescription = substr($metaDescription, 0,150);
?>
<meta name="keywords" content="<?= $metaTags ?>" />
<meta name="description" content="<?= $metaDescription ?>" />
