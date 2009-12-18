<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="wc-edit">
      <?= form::open("<?= $path ?>") ?>
      <fieldset>
      <legend style="display: none">Update Resource</legend>
        <ul>
          <li>
            <?= form::label("title", "Title:") ?><br/>
            <?= form::input("title", $form["title"]["value"], "readonly={$form["title"]["readonly"]}") ?>
            <?= empty($errors["title"]) ? "" : "<span class=\"error\">{$errors["title"]}</span>" ?>
          </li>
          <li>
            <?= form::label("description", "Description:") ?><br/>
            <?= form::textarea("description", $form["description"]["value"]) ?>
            <?= empty($errors["description"]) ? "" : "<span class=\"error\">{$errors["description"]}</span>" ?>
          </li>
          <li>
            <?= form::label("name", "Name:") ?><br/>
            <?= form::input("name", $form["name"]["value"], "readonly={$form["name"]["readonly"]}") ?>
            <?= empty($errors["name"]) ? "" : "<span class=\"error\">{$errors["name"]}</span>" ?>
          </li>
          <li>
            <?= form::label("slug", "Internet Address:") ?><br/>
            <?= form::input("slug", $form["slug"]["value"], "readonly={$form["slug"]["readonly"]}") ?>
            <?= empty($errors["slug"]) ? "" : "<span class=\"error\">{$errors["slug"]}</span>" ?>
          </li>
          <li style="text-align: center">
            <?= form::submit("submit", "Update") ?>
            <?= form::input(array('type'=>'reset','name'=>'reset'), "Reset") ?>
          </li>
        </ul>
        </fieldset>
      </form>
</div>

