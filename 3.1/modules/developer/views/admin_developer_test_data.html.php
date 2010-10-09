<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("#g-generate-test-data").ready(function() {
    $(".g-generate-checkbox").click(function() {
      var buttons = $(this).val();
      $(buttons).attr("disabled", !this.checked);
    });
    <? if (!empty($form["generate_albums"])): ?>
      $("#g-generate-albums").click();
    <? endif ?>
    <? if (!empty($form["generate_photos"])): ?>
      $("#g-generate-photos").click();
    <? endif ?>
    <? if (!empty($form["generate_comments"])): ?>
      $("#g-generate-comments").click();
    <? endif ?>
    <? if (!empty($form["generate_tags"])): ?>
      $("#g-generate-tags").click();
    <? endif ?>
  });
</script>
<?= form::open($action, array("method" => "post", "id" => "g-generate-test-data")) ?>
  <? if (!empty($album_count)): ?>
    <p><?= t("Currently:") ?><br />

    <i>(<?= $album_count ?>, <?= $photo_count ?>, <?= $comment_count ?>, <?= $tag_count ?>)</i>
    </p>
  <? endif ?>

<fieldset>
  <ul>
    <li><?= access::csrf_form_field() ?></li>
    <li <? if (!empty($errors["albums"])): ?> class="g-error"<? endif ?>>
      <fieldset>
        <?= form::label("g-generate-albums", t("Generate Albums")) ?>
        <?= form::checkbox(array("id" => "g-generate-albums", "name" => "generate_albums", "class" => "g-generate-checkbox", "style" => "display:inline", "checked" => !empty($form["generate_albums"])), ".g-radio-album") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("album_$number", "$number") ?>
          <?= form::radio(array("id" => "album_$number", "name" => "albums", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "g-radio-album"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["albums"]) && $errors["albums"] == "numeric"): ?>
        <p class="g-error"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <li <? if (!empty($errors["photos"])): ?> class="g-error"<? endif ?>>
      <fieldset>
        <?= form::label("g-generate-photos", t("Generate Photos and Albums")) ?>
        <?= form::checkbox(array("id" => "g-generate-photos", "name" => "generate_photos", "class" => "g-generate-checkbox", "style" => "display:inline", "checked" => !empty($form["generate_photos"])), ".g-radio-photo") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("photo_$number", "$number") ?>
          <?= form::radio(array("id" => "photo_$number", "name" => "photos", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "g-radio-photo"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["photos"]) && $errors["photos"] == "numeric"): ?>
        <p class="g-error"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <? if(!empty($comment_installed)): ?>
    <li <? if (!empty($errors["comments"])): ?> class="g-error"<? endif ?>>
      <fieldset>
        <?= form::label("g-generate-comments", t("Generate Comments")) ?>
        <?= form::checkbox(array("id" => "g-generate-comments", "name" => "generate_comments", "class" => "g-generate-checkbox", "style" => "display:inline", "checked" => !empty($form["generate_comments"])), ".g-radio-comment") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("comment_$number", "$number") ?>
          <?= form::radio(array("id" => "comment_$number", "name" => "comments", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "g-radio-comment"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["comments"]) && $errors["comments"] == "numeric"): ?>
        <p class="g-error"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <? endif ?>
    <? if(!empty($tag_installed)): ?>
    <li <? if (!empty($errors["tags"])): ?> class="g-error"<? endif ?>>
      <fieldset>
        <?= form::label("g-generate-tags", t("Generate Tags")) ?>
        <?= form::checkbox(array("id" => "g-generate-tags", "name" => "generate_tags", "class" => "g-generate-checkbox", "style" => "display:inline", "checked" => !empty($form["generate_tags"])), ".g-radio-tag") ?>
        <? foreach (array(1, 10, 50, 100, 500, 1000) as $number): ?>
          <span style="float:left;padding-right: .5em;"><?= form::label("tag_$number", "$number") ?>
          <?= form::radio(array("id" => "tag_$number", "name" => "tags", "style" => "display:inline", "checked" => $number == 10, "disabled" => true, "class" => "g-radio-tag"), $number) ?></span>
        <? endforeach ?>
      </fieldset>
      <? if (!empty($errors["tags"]) && $errors["tags"] == "numeric"): ?>
        <p class="g-error"><?= t("Number to create must be numeric") ?></p>
      <? endif ?>
    </li>
    <? endif ?>
    <li>
      <?= form::submit(array("id" => "g-generate-data", "name" => "generate", "class" => "submit", "style" => "clear:both!important"), t("Generate")) ?>
    </li>
  </ul>
</fieldset>
