<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="wc-edit">
      <?= form::open($path) ?>
      <fieldset>
      <legend style="display: none">Add Resource</legend>
        <ul>
          <li>
            <?= form::label("title", "Title:") ?><br/>
            <?= form::input("title", $form["title"]) ?>
            <?= empty($errors["title"]) ? "" : "<span class=\"error\">{$errors["title"]}</span>" ?>
          </li>
          <li>
            <?= form::label("description", "Description:") ?><br/>
            <?= form::textarea("description", $form["description"]) ?>
            <?= empty($errors["description"]) ? "" : "<span class=\"error\">{$errors["description"]}</span>" ?>
          </li>
          <li>
            <?= form::label("name", "Name:") ?><br/>
            <?= form::input("name", $form["name"]) ?>
            <?= empty($errors["name"]) ? "" : "<span class=\"error\">{$errors["name"]}</span>" ?>
          </li>
          <li>
            <?= form::label("slug", "Internet Address:") ?><br/>
  <?= form::input("slug", $form["slug"]) ?>
            <?= empty($errors["slug"]) ? "" : "<span class=\"error\">{$errors["slug"]}</span>" ?>
          </li>
          <? if ($function == "add_photo"): ?>
          <li>
            <?= form::label("image", "Image File:") ?><br/>
            <?= form::upload("image", $form["image_file"]) ?>
            <?= empty($errors["image"]) ? "" : "<span class=\"error\">{$errors["image"]}</span>" ?>
          </li>
          <? endif ?>
          <li style="text-align: center">
            <?= form::submit("submit", "Add") ?>
            <?= form::input(array('type'=>'reset','name'=>'reset'), "Reset") ?>
          </li>
        </ul>
        </fieldset>
      </form>
</div>

