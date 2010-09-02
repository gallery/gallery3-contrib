<?php defined("SYSPATH") or die("No direct script access.");

  // Check and see if the current photo has any faces or notes associated with it.
  $existingFaces = ORM::factory("items_face")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  $existingNotes = ORM::factory("items_note")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  if (locales::is_rtl()) {
    $rtl_support = "image-annotate-rtl";
  } else {
    $rtl_support = "";
  }
  $tags_arraystring = "";
  $jscode = "";
  $legend_faces = "";
  $legend_notes = "";
  if (module::get_var("gallery", "active_site_theme") == "greydragon") {
    $css_item_id = "#g-photo-id-". $item->id;
    $css_a_class = ".g-sb-preview";
  } else {
    $css_item_id = "#g-item-id-". $item->id;
    $css_a_class = ".g-fullsize-link";
  }
  // If it does, then insert some javascript and display an image map
  //   to show where the faces are at.
  if ((count($existingFaces) > 0) || (count($existingNotes) > 0)) {
    $jscode = "notes: [ ";
    foreach ($existingFaces as $oneFace) {
      $oneTag = ORM::factory("tag", $oneFace->tag_id);
      if (module::get_var("photoannotation", "showfaces", false)) {
        $legend_faces .= "<a href=\"". $oneTag->url() ."\">". html::clean($oneTag->name) ."</a>, ";
      }
      $jscode .= "{ \"top\": ". $oneFace->y1 .",\n";
      $jscode .= "\"left\": ". $oneFace->x1 .",\n";
      $jscode .= "\"width\": ". ($oneFace->x2 - $oneFace->x1) .",\n";
      $jscode .= "\"height\": ". ($oneFace->y2 - $oneFace->y1) .",\n";
      $jscode .= "\"text\": \"". html::clean($oneTag->name) ."\",\n";
      $jscode .= "\"description\": \"". html::clean($oneFace->description) ."\",\n";
      $jscode .= "\"noteid\": ". $oneFace->id .",\n";
      $jscode .= "\"notetype\": \"face\",\n";
      $jscode .= "\"editable\": true,\n";
      $jscode .= "\"url\": \"". $oneTag->url() ."\" },\n";
    }
    if ($legend_faces != "") {
      $legend_faces = trim($legend_faces, ", ");
      $legend_faces = t("Faces on this photo: ") . $legend_faces;
    }
    foreach ($existingNotes as $oneNote) {
      if (module::get_var("photoannotation", "shownotes", false)) {
        $legend_notes .= html::clean($oneNote->title) .", ";
      }
      $jscode .= "{ \"top\": ". $oneNote->y1 .",\n";
      $jscode .= "\"left\": ". $oneNote->x1 .",\n";
      $jscode .= "\"width\": ". ($oneNote->x2 - $oneNote->x1) .",\n";
      $jscode .= "\"height\": ". ($oneNote->y2 - $oneNote->y1) .",\n";
      $jscode .= "\"text\": \"". html::clean($oneNote->title) ."\",\n";
      $jscode .= "\"description\": \"". html::clean($oneNote->description) ."\",\n";
      $jscode .= "\"noteid\": ". $oneNote->id .",\n";
      $jscode .= "\"notetype\": \"note\",\n";
      $jscode .= "\"editable\": false,\n";
      $jscode .= "\"url\": \"\" },\n";
    }
    $jscode = trim($jscode, ",\n");
    $jscode .= " ],";
    if ($legend_notes != "") {
      $legend_notes = trim($legend_notes, ", ");
      $legend_notes = t("Notes on this photo: ") . $legend_notes;
    }
  }
  $legend_display = $legend_faces;
  if ($legend_display == "") {
    $legend_display = $legend_notes;
  } else {
    if ($legend_notes != "") {
      $legend_display = $legend_display ."<br />". $legend_notes;
    }
  }
  $labels_arraystring = "labels: [ '". t("Tag:") ."','". t("Note Title:") ."','". t("Description (optional):") ."','". t("Are you sure you want to delete this annotation?") ."','". t("or") ."','". t("Yes") ."','". t("No") ."','". t("Confirm deletion") ."' ],";
?>
<script type="text/javascript">
      $(document).ready(function() {
				$("<?= $css_item_id ?>").annotateImage({
          <? if ((access::can("view", $item)) && (access::can("edit", $item))): ?>
					editable: true,
          <? else: ?>
          editable: false,
          <? endif ?>
          saveUrl: '<?= url::site("photoannotation/save/". $item->id) ?>',
          deleteUrl: '<?= url::site("photoannotation/delete/". $item->id) ?>',
          tags: '<?= url::site("tags/autocomplete") ?>',
          <?= $labels_arraystring ?>
					<?= $jscode ?>
          rtlsupport: '<?= $rtl_support ?>',
					useAjax: false,
          cssaclass: '<?= $css_a_class ?>',
          csrf: '<?= $csrf ?>'
				});
			});
		</script>
    <? if ($legend_display != ""): ?>
    <?= "<div style=\"text-align: center\">". $legend_display ."</div>" ?>
    <? endif ?>
