<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-captionator-dialog">
  <script type="text/javascript">
    $(document).ready(function() {
      $('form input[name^=tags]').ready(function() {
          $('form input[name^=tags]').gallery_autocomplete(
            "<?= url::site("/tags/autocomplete") ?>",
            {max: 30, multiple: true, multipleSeparator: ',', cacheLength: 1});
        });
      $('form input[name^=title]').change(function() {
        var title = $(this).val();
        slug = title.replace(/^\'/, "");
        var slug = title.replace(/[^A-Za-z0-9-_]+/g, "-");
        slug = slug.replace(/^-/, "");
        slug = slug.replace(/-$/, "");
        $(this).parent().parent().find("input[name^=internetaddress]").val(slug);
      });
    });
  </script>
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
                <input required type="text" name="title[<?= $child->id ?>]" value="<?= html::chars($child->title) ?>"/>
              </li>
              <li>
                <label for="description[<?= $child->id ?>]"> <?= t("Description") ?> </label>
                <textarea style="height: 5em" name="description[<?= $child->id ?>]"><?= $child->description ?></textarea>
              </li>
              <? if ($enable_tags): ?>
              <li>
                <label for="tags[<?= $child->id ?>]"> <?= t("Tags (comma separated)") ?> </label>
                <input type="text" name="tags[<?= $child->id ?>]" class="ac_input" autocomplete="off" value="<?= html::chars($tags[$child->id]) ?>"/>
              </li>
              <? endif ?>
              <li>
                <label for="filename[<?= $child->id ?>]"> <?= t("Filename") ?> </label>
                <input type="text" name="filename[<?= $child->id ?>]" class="ac_input" autocomplete="off" value="<?= html::chars($child->name) ?>"/>
              </li>
              <li>
                <label for="internetaddress[<?= $child->id ?>]"> <?= t("Internet Address") ?> </label>
                <input type="text" name="internetaddress[<?= $child->id ?>]" class="ac_input" autocomplete="off" value="<?= html::chars($child->slug) ?>"/>
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
