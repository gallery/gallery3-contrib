<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="wc-edit">
  <?= form::open("edit_{$type}?path=$path") ?>
  <fieldset>
    <legend style="display: none"><?= $title ?></legend>
    <ul>
      <li>
        <?= form::label("title", "{$form->title->label}:") ?><br/>
        <?= form::input("title", $form->title->value,
            empty($form->title->readonly) ? "" : "readonly={$form->title->readonly}") ?>
        <?= empty($errors->title) ? "" : "<span class=\"error\">{$errors->title}</span>" ?>
      </li>
      <li>
        <?= form::label("name", "{$form->name->label}:") ?><br/>
        <?= form::input("name", $form->name->value,
            empty($form->name->readonly) ? "" : "readonly={$form->name->readonly}") ?>
        <?= empty($errors->name) ? "" : "<span class=\"error\">{$errors->name}</span>" ?>
      </li>
      <li>
        <?= form::label("description", "{$form->description->label}:") ?><br/>
        <?= form::textarea("description", $form->description->value) ?>
        <?= empty($errors->description) ? "" : "<span class=\"error\">{$errors->description}</span>" ?>
      </li>
      <li>
        <?= form::label("slug", "{$form->slug->label}:") ?><br/>
        <?= form::input("slug", $form->slug->value,
            empty($form->slug->readonly) ? "" : "readonly={$form->slug->readonly}") ?>
        <?= empty($errors->slug) ? "" : "<span class=\"error\">{$errors->slug}</span>" ?>
      </li>
      <? if (!empty($form->image_file)): ?>
      <li>
        <?= form::label("image_file", "{$form->image_file->label}:") ?><br/>
        <?= form::upload("image_file") ?>
        <?= empty($errors->image_file) ? "" : "<span class=\"error\">{$errors->image_file}</span>" ?>
      </li>
      <? endif ?>
      <? if (!empty($errors->form_error)): ?>
      <li>
        <span class="error"><?= $errors->form_error ?></span>
      </li>
      <? endif ?>
      <li style="text-align: center">
         <?= form::submit("submit", "Update") ?>
         <?= form::input(array('type'=>'reset','name'=>'reset'), "Reset") ?>
      </li>
    </ul>
  </fieldset>
  </form>
</div>

