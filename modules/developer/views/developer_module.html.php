<?php defined("SYSPATH") or die("No direct script access.") ?>

<?= form::open($action, array("method" => "post"), $hidden) ?>
  <fieldset>
    <ul>
      <li <? if (!empty($errors["name"])): ?> class="g-error"<? endif ?>>
        <?= form::label("name", t("Name")) ?>
        <?= form::input("name", $form["name"]) ?>
        <? if (!empty($errors["name"]) && $errors["name"] == "required"): ?>
          <p class="g-error"><?= t("Module name is required") ?></p>
        <? endif ?>
        <? if (!empty($errors["name"]) && $errors["name"] == "module_exists"): ?>
          <p class="g-error"><?= t("Module is already implemented") ?></p>
        <? endif ?>
      </li>
      <li <? if (!empty($errors["description"])): ?> class="g-error"<? endif ?>>
        <?= form::label("description", t("Description")) ?>
        <?= form::input("description", $form["description"]) ?>
        <? if (!empty($errors["description"]) && $errors["description"] == "required"): ?>
          <p class="g-error"><?= t("Module description is required")?></p>
        <? endif ?>
      </li>
      <li>
        <ul>
          <li>
            <?= form::label("theme[]", t("Theme Callbacks")) ?>
            <?= form::dropdown(array("name" => "theme[]", "multiple" => true, "size" => 6), $theme, $form["theme[]"]) ?>
          </li>
          <li>
            <?= form::label("menu[]", t("Menu Callback")) ?>
            <?= form::dropdown(array("name" => "menu[]", "multiple" => true, "size" => 6), $menu, $form["menu[]"]) ?>
          </li>
          <li>
            <?= form::label("event[]", t("Gallery Event Handlers")) ?>
            <?= form::dropdown(array("name" => "event[]", "multiple" => true, "size" => 6), $event, $form["event[]"]) ?>
          </li>
        </ul>
      </li>
      <li>
        <?= form::submit(array("id" => "g-generate-module", "name" => "generate", "class" => "submit", "style" => "clear:both!important"), t("Generate")) ?>
      </li>
    </ul>
  </fieldset>
<?= form::close() ?>
