<?php defined("SYSPATH") or die("No direct script access.") ?>

<?= form::open($action, array("method" => "post")) ?>
  <fieldset>
    <ul>
      <li><?= access::csrf_form_field() ?></li>
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
      <li <? if (!empty($errors["display_name"])): ?> class="g-error"<? endif ?>>
        <?= form::label("display_name", t("Display name")) ?>
        <?= form::input("display_name", $form["display_name"]) ?>
        <? if (!empty($errors["display_name"]) && $errors["display_name"] == "required"): ?>
          <p class="g-error"><?= t("Module display_name is required")?></p>
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
            <?= form::label("theme[]", t("Theme callbacks")) ?>
            <?= form::dropdown(array("name" => "theme[]", "multiple" => true, "size" => 6), $theme, $form["theme[]"]) ?>
          </li>
          <li style="padding-left: 1em" >
            <?= form::label("event[]", t("Gallery event handlers")) ?>
            <?= form::dropdown(array("name" => "event[]", "multiple" => true, "size" => 6), $event, $form["event[]"]) ?>
          </li>
        </ul>
      </li>
      <li>
         <?= form::submit($submit_attributes, t("Generate")) ?>
      </li>
    </ul>
  </fieldset>
</form>

