<?php defined("SYSPATH") or die("No direct script access.") ?>
<? date_default_timezone_set('Australia/ACT'); ?> 
<div class="g-metadata">
<span class="g-about-this">
<table cellspacing="0" cellpadding="0" border="0">
<? if ($album_count > 0): ?>
  <tr>
    <td><strong class="caption"><?= t("Albums:&nbsp;") ?></strong></td>
    <td><?= $album_count ?></td>
  </tr>
<? endif ?>
  <tr>
    <td><strong class="caption"><?= t("Images:&nbsp;") ?></strong></td>
    <td><?= $photo_count ?></td>
  </tr>
  <tr>
    <td><strong class="caption"><?= t("Views:&nbsp;") ?></strong></td>
    <td><?= $vcount ?></td>
  </tr>
</table>
  <span >
  
  <!--This Div will insert a margin either side of the desciption if there are tags to display-->
  <? if (count($all_tags) > 0): ?>
	<div style="margin-top: 10px; margin-bottom: 10px;">
  <? endif ?>

  <? if ($description <> ""): ?>
	<strong class="caption"><?= t("Details:&nbsp;") ?></strong>
    <?= $description ?>
  </span ><br>
  <? endif ?>
  
    <? if (count($all_tags) > 0): ?>
	</div>
  <span >
      <strong class=="caption"><?= t("Tags:&nbsp;") ?></strong>
  </span >
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
<? endif ?>
</span>
</div>
