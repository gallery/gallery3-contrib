<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-captionator-dialog">
  <form action="<?= url::site("captionator/save/{$album->id}") ?>" method="post" id="g-captionator-form">
    <?= access::csrf_form_field() ?>
    <fieldset>
      <legend>
        <?= t("Add captions for photos in <b>%album_title</b>", array("album_title" => $album->title)) ?>
      </legend>

      <? foreach ($album->viewable()->children() as $child): ?>
      <table>
        <tr>
          <td style="width: 140px">
            <?= $child->thumb_img(array(), 140, true) ?>
          </td>
          <td>
            <ul>
              <li>
                <label for="title[<?= $child->id ?>]"> <?= t("Title") ?> </label>
                <input type="text" name="title[<?= $child->id ?>]" value="<?= $child->title ?>"/>
              </li>
              <li>
                <label for="description[<?= $child->id ?>]"> <?= t("Description") ?> </label>
                <textarea style="height: 5em" name="description[<?= $child->id ?>]"><?= $child->description ?></textarea>
              </li>
            </ul>
          </td>
        </tr>
      </table>
      <? endforeach ?>
    </fieldset>
    <fieldset>
      <input type="submit" name="cancel" value="<?= t("Cancel") ?>"/>
      <input type="submit" name="save" value="<?= t("Save") ?>"/>
    </fieldset>
  </form>
</div>
