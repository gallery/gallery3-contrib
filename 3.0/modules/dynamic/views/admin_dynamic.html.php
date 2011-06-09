<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  $(function() {
    $( "#tabs" ).tabs();
  });
</script>
<div id="g-dyanmic-block" class="g-block ui-helper-clearfix">
  <h1> <?= t("Dynamic Albums") ?> </h1>

  <?= form::open("admin/dynamic/handler", array("id" => "g-admin-form")) ?>
  <?= access::csrf_form_field() ?>
  <div  id="tabs">
    <ul>
    <? foreach ($tabs as $album => $label): ?>
      <li><a href="#<?= $album ?>"><?= $label ?></a></li>
    <? endforeach ?>
    </ul>
    <? foreach (array("updates", "popular") as $album): ?>
    <div id="<?= $album ?>">
      <ul>
        <li>
          <?= form::label("{$album}_enabled", t("Enable")) ?>
          <?= form::checkbox("{$album}_enabled", 1, $form["{$album}_enabled"], "style='float: none'") ?>
        </li>
        <li>
          <?= form::label("{$album}_limit", t("Limit (leave empty for unlimited)")) ?>
          <?= form::input("{$album}_limit", $form["{$album}_limit"]) ?>
          <?= empty($errors["{$album}_limit"]) ? "" : "<span class='g-error'>" . t("Limit must be numeric") ?>
        </li>
        <li>
          <?= form::label("{$album}_description", t("Description")) ?>
          <?= form::textarea("{$album}_description", $form["{$album}_description"]) ?>
          <?= empty($errors["{$album}_description"]) ? "" : "<span class='g-error'>" . t("Description must be less than 2048 bytes") ?>
       </li>
      <ul>
    </div>
    <? endforeach ?>
  </div>
  <?= form::submit("submit", t("Submit")) ?>
</div>
