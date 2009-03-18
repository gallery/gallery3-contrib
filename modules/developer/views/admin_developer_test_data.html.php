<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  $("#gGenerateTestData").ready(function() {
    $(".gGenerateCheckbox").click(function() {
      var buttons = $(this).val();
      $(buttons).attr("disabled", !this.checked);
    });
    <? if (!empty($form["generate_albums"])): ?>
      $("#gGenerateAlbums").click();
    <? endif ?>
    <? if (!empty($form["generate_photos"])): ?>
      $("#gGeneratePhotos").click();
    <? endif ?>
    <? if (!empty($form["generate_comments"])): ?>
      $("#gGenerateCommentss").click();
    <? endif ?>
    <? if (!empty($form["generate_tags"])): ?>
      $("#gGenerateTags").click();
    <? endif ?>
  });
</script>
<?= form::open($action, array("method" => "post", "id" => "gGenerateTestData"), $hidden) ?>
  <? if (!empty($album_count)): ?>
    <p><?= t("Currently:") ?><br />
       
    <i>(<?= $album_count ?>, <?= $photo_count ?>, <?= $comment_count ?>, <?= $tag_count ?>)</i>
    </p>
  <? endif ?>

<fieldset>
  <ul>
    <li <? if (!empty($errors["albums"])): ?> class="gError"<? endif ?>>
      <fieldset>
        <?= form::label("gGenerateAlbums", t("Generate Albums")) ?>
        <?= form::checkbox(array("id" => "gGenerateAlbums", "name" => "generate_albums", "class" => "gGenerateCheckbox", "style" => "display:inline", "checked" => !empty($form["generate_albums"])), ".gRadioAlbum") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("album_$number", "$number") ?>
          <?= form::radio(array("id" => "album_$number", "name" => "albums", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "gRadioAlbum"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["albums"]) && $errors["albums"] == "numeric"): ?>
        <p class="gError"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <li <? if (!empty($errors["photos"])): ?> class="gError"<? endif ?>>
      <fieldset>
        <?= form::label("gGeneratePhotos", t("Generate Photos and Albums")) ?>
        <?= form::checkbox(array("id" => "gGeneratePhotos", "name" => "generate_photos", "class" => "gGenerateCheckbox", "style" => "display:inline", "checked" => !empty($form["generate_photos"])), ".gRadioPhoto") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("photo_$number", "$number") ?>
          <?= form::radio(array("id" => "photo_$number", "name" => "photos", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "gRadioPhoto"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["photos"]) && $errors["photos"] == "numeric"): ?>
        <p class="gError"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li
    <? if(!empty($comment_installed)): ?>
    <li <? if (!empty($errors["comments"])): ?> class="gError"<? endif ?>>
      <fieldset>
        <?= form::label("gGenerateComments", t("Generate Comments")) ?>
        <?= form::checkbox(array("id" => "gGenerateComments", "name" => "generate_comments", "class" => "gGenerateCheckbox", "style" => "display:inline", "checked" => !empty($form["generate_comments"])), ".gRadioComment") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("comment_$number", "$number") ?>
          <?= form::radio(array("id" => "comment_$number", "name" => "comments", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "gRadioComment"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["comments"]) && $errors["comments"] == "numeric"): ?>
        <p class="gError"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <? endif ?>
    <? if(!empty($tag_installed)): ?>
    <li <? if (!empty($errors["tags"])): ?> class="gError"<? endif ?>>
      <fieldset>
        <?= form::label("gGenerateTags", t("Generate Tags")) ?>
        <?= form::checkbox(array("id" => "gGenerateTags", "name" => "generate_tags", "class" => "gGenerateCheckbox", "style" => "display:inline", "checked" => !empty($form["generate_tags"])), ".gRadioTag") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("tag_$number", "$number") ?>
          <?= form::radio(array("id" => "tag_$number", "name" => "tags", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "gRadioTag"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["tags"]) && $errors["tags"] == "numeric"): ?>
        <p class="gError"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <? endif ?>
    <li>
      <?= form::submit(array("id" => "gGenerateData", "name" => "generate", "class" => "submit", "style" => "clear:both!important"), t("Generate")) ?>
    </li>
  </ul>
</fieldset>
