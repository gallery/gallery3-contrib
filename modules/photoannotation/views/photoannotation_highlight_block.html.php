<?php defined("SYSPATH") or die("No direct script access.");
  // Check and see if the current photo has any faces or notes associated with it.
  $existingUsers = ORM::factory("items_user")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  $existingFaces = ORM::factory("items_face")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  $existingNotes = ORM::factory("items_note")
                        ->where("item_id", "=", $item->id)
                        ->find_all();
  $fullname = module::get_var("photoannotation", "fullname", false);
  $showusers = module::get_var("photoannotation", "showusers", false);
  $showfaces = module::get_var("photoannotation", "showfaces", false);
  $shownotes = module::get_var("photoannotation", "shownotes", false);
  if (locales::is_rtl()) {
    $rtl_support = "image-annotate-rtl";
  } else {
    $rtl_support = "";
  }
  $tags_arraystring = "";
  $jscode = "";
  $legend_faces = "";
  $legend_notes = "";
  $legend_users = "";
  if (module::get_var("gallery", "active_site_theme") == "greydragon") {
    $css_item_id = "#g-photo-id-". $item->id;
    $css_a_class = ".g-sb-preview";
  } else {
    $css_item_id = "#g-item-id-". $item->id;
    $css_a_class = ".g-fullsize-link";
  }
  // If it does, then insert some javascript and display an image map
  //   to show where the faces are at.
  if ((count($existingFaces) > 0) || (count($existingNotes) > 0) || (count($existingUsers) > 0)) {
    $jscode = "notes: [ ";
    foreach ($existingUsers as $oneUser) {
      $oneTag =  ORM::factory("user", $oneUser->user_id);
      if ($oneTag->loaded()) {
        if ($fullname) {
          $user_text = $oneTag->display_name();
        } else {
          $user_text = $oneTag->name;
        }
        if ($showusers) {
          $legend_users .= "<span id=\"photoannotation-legend-user-". $oneUser->id . "\"><a href=\"". user_profile::url($oneUser->user_id) ."\">". html::clean($user_text) ."</a></span>, ";
        }
        $jscode .= "{ \"top\": ". $oneUser->y1 .",\n";
        $jscode .= "\"left\": ". $oneUser->x1 .",\n";
        $jscode .= "\"width\": ". ($oneUser->x2 - $oneUser->x1) .",\n";
        $jscode .= "\"height\": ". ($oneUser->y2 - $oneUser->y1) .",\n";
        $jscode .= "\"text\": \"". html::clean($user_text) ."\",\n";
        $jscode .= "\"internaltext\": \"". $oneTag->display_name() ." (". $oneTag->name .")\",\n";
        $jscode .= "\"description\": \"". html::clean($oneUser->description) ."\",\n";
        $jscode .= "\"noteid\": ". $oneUser->id .",\n";
        $jscode .= "\"notetype\": \"user\",\n";
        $jscode .= "\"editable\": true,\n";
        $jscode .= "\"url\": \"". user_profile::url($oneUser->user_id) ."\" },\n";
      }
    }
    if ($legend_users != "") {
      $legend_users = trim($legend_users, ", ");
      $legend_users = t("People on this photo: ") . $legend_users ."<br />";
    }
    foreach ($existingFaces as $oneFace) {
      $oneTag = ORM::factory("tag", $oneFace->tag_id);
      if ($oneTag->loaded()) {
        if ($showfaces) {
          $legend_faces .= "<span id=\"photoannotation-legend-face-". $oneFace->id . "\"><a href=\"". $oneTag->url() ."\">". html::clean($oneTag->name) ."</a></span>, ";
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
    }
    if ($legend_faces != "") {
      $legend_faces = trim($legend_faces, ", ");
      $legend_faces = t("Faces on this photo: ") . $legend_faces ."<br />";
    }
    foreach ($existingNotes as $oneNote) {
      if ($shownotes) {
        $legend_notes .= "<span id=\"photoannotation-legend-note-". $oneNote->id . "\">". html::clean($oneNote->title) ."</span>, ";
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
      $legend_notes = t("Notes on this photo: ") . $legend_notes ."<br />";
    }
  }
  $legend_display = $legend_users . $legend_faces . $legend_notes;
  $labels_arraystring = "labels: [ '". t("Tag:") ."','". t("Note Title:") ."','". t("Description (optional)") ."','". t("Are you sure you want to delete this annotation?") ."','". t("or") ."','". t("Yes") ."','". t("No") ."','". t("Confirm deletion") ."','". t("Save") ."','". t("Cancel") ."','". t("Person:") ."','". t("No user selected") ."','". t("Select one of the following") ."' ],";
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
          users: '<?= url::site("photoannotation/autocomplete") ?>',
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
