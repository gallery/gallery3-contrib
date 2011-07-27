<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  // Loop through each tag in $all_tags, and display it as a link.
  $not_first = 0;
  foreach ($all_tags as $one_tag) {
    if ($not_first++ > 0) {
      print ", ";
    }
    print "<a href=\"" . $one_tag->url() . "\">" . html::clean($one_tag->name) . "</a>";
  }
?>
