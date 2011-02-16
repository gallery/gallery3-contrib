<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  // Create an array to store the tag names and urls in.
  $display_tags = array();  
  
  // Loop through all tags in the album, copying their
  //   names and urls into the array and skipping duplicates.
  $last_tagid = "";
  foreach ($all_tags as $one_tag) {
    if ($last_tagid != $one_tag->id) {
      $tag = ORM::factory("tag", $one_tag->id);
      $display_tags[] = array(html::clean($tag->name), $tag->url());
      $last_tagid = $one_tag->id;
    }
  }
  
  // Sort the array.
  asort($display_tags);
  
  // Print out the list of tags as clickable links.
  $not_first = 0;
  foreach ($display_tags as $one_tag) {
    if ($not_first++ > 0) {
      print ", ";
    }
    print "<a href=\"" . $one_tag[1] . "\">" . $one_tag[0] . "</a>";
  }
?>
