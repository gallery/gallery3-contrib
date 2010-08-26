<?php defined("SYSPATH") or die("No direct script access.");

  // Check and see if the current photo has any faces or notes associated with it.
  $existingFaces = ORM::factory("items_face")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  $existingNotes = ORM::factory("items_note")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  $tags_arraystring = "";
  $jscode = "";
  // If it does, then insert some javascript and display an image map
  //   to show where the faces are at.
  if ((count($existingFaces) > 0) || (count($existingNotes) > 0)) {
    $jscode = "notes: [ ";
    foreach ($existingFaces as $oneFace) {
      $oneTag = ORM::factory("tag", $oneFace->tag_id);
      $jscode .= "{ \"top\": ". $oneFace->y1 .",\n";
      $jscode .= "\"left\": ". $oneFace->x1 .",\n";
      $jscode .= "\"width\": ". ($oneFace->x2 - $oneFace->x1) .",\n";
      $jscode .= "\"height\": ". ($oneFace->y2 - $oneFace->y1) .",\n";
      $jscode .= "\"text\": \"". html::clean($oneTag->name) ."\",\n";
      $jscode .= "\"noteid\": ". $oneNote->id .",\n";
      $jscode .= "\"editable\": true,\n";
      $jscode .= "\"url\": \"". $oneTag->url() ."\" },\n";
    }
    
    foreach ($existingNotes as $oneNote) {
      $tagdesc = "";
      if ($oneNote->description) {
        $tagdesc = "<br />". html::clean($oneNote->description);
      }
      $jscode .= "{ \"top\": ". $oneNote->y1 .",\n";
      $jscode .= "\"left\": ". $oneNote->x1 .",\n";
      $jscode .= "\"width\": ". ($oneNote->x2 - $oneNote->x1) .",\n";
      $jscode .= "\"height\": ". ($oneNote->y2 - $oneNote->y1) .",\n";
      $jscode .= "\"text\": \"". html::clean($oneNote->title) . $tagdesc ."\",\n";
      $jscode .= "\"noteid\": ". $oneNote->id .",\n";
      $jscode .= "\"editable\": false,\n";
      $jscode .= "\"url\": \"\" },\n";
    }
    $jscode = trim($jscode, ",\n");
    $jscode .= " ],";
  }
  $item_tags = ORM::factory("tag")
    ->join("items_tags", "tags.id", "items_tags.tag_id")
    ->where("items_tags.item_id", "=", $item->id)
    ->find_all();
  $tags_arraystring = "tags: [ ";
  foreach ($item_tags as $current_tag) {
    $tags_arraystring .= "{'name':'". html::clean($current_tag->name) ."','id':'". $current_tag->id ."'},";
  }
  $tags_arraystring = trim($tags_arraystring, ",");
  $tags_arraystring .= " ],";
  $labels_arraystring = "labels: [ '". t("Tag:") ."','". t("Note Title:") ."','". t("Description (optional):") ."','". t("Are you sure you want to delete this annotation?") ."' ],";
?>

		<script language="javascript">
			$(document).ready(function() {
				$("#g-item-id-<?= $item->id ?>").annotateImage({
          <? if ((access::can("view", $item)) && (access::can("edit", $item))): ?>
					editable: true,
          <? else: ?>
          editable: false,
          <? endif ?>
          saveUrl: '<?= url::site("photoannotation/save/". $item->id) ?>',
          deleteUrl: '<?= url::site("photoannotation/delete/". $item->id) ?>',
          currentUrl: '<?= url::site(Router::$complete_uri, $protocol); ?>',
          <?= $tags_arraystring ?>
          <?= $labels_arraystring ?>
					<?= $jscode ?>
					useAjax: false,
          csrf: '<?= $csrf ?>'
				});
			});
		</script>

<?php
?>
