<?php defined("SYSPATH") or die("No direct script access.") ?>

<? $tags_per_column = $tags->count()/5 ?>
<? $column_tag_count = 0 ?>

<div class="g-block">
  <h1> <?= t("All Tags in the Gallery") ?> </h1>

  <div class="g-block-content">
    <table id="g-tag-admin">
      <caption>
        <?= t2("There is one tag", "There are %count tags", $tags->count()) ?>
      </caption>
      <tr>
        <td>
        <? foreach ($tags as $i => $tag): ?>
          <? $current_letter = strtoupper(mb_substr($tag->name, 0, 1)) ?>

          <? if ($i == 0): /* first letter */ ?>
          <strong><?= html::clean($current_letter) ?></strong>
          <ul>
          <? elseif ($last_letter != $current_letter): /* new letter */ ?>
          </ul>
            <? if ($column_tag_count > $tags_per_column): /* new column */ ?>
              <? $column_tag_count = 0 ?>
        </td>
        <td>
            <? endif ?>
          <strong><?= html::clean($current_letter) ?></strong>
          <ul>
          <? endif ?>
              <li>
                <span class="g-editable g-tag-name" rel="<?= $tag->id ?>"><a href="<?= $tag->url() ?>"><?= html::clean($tag->name) ?></a></span>
                <span class="g-understate">(<?= $tag->count ?>)</span>
              </li>
          <? $column_tag_count++ ?>
          <? $last_letter = $current_letter ?>
        <? endforeach ?>
          </ul>
        </td>
      </tr>
    </table>
  </div>
</div>
