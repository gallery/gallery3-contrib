<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
input[type="text"] {
  width: 95%;
}
</style>
      
<? if (module::get_var("embedlinks", "HTMLCode")) { ?>
<h3 align="center"><?= t("HTML Links")?></h3>
<table class="gEmbedLink">
  <tbody>
    <tr>
      <th colspan="2"><?= t("Link To This Page:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= url::abs_site("{$item->type}s/{$item->id}") ?>&quot;>Click Here</a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= url::abs_site("{$item->type}s/{$item->id}") ?>&quot;><img src=&quot;<?= $item->thumb_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= url::abs_site("{$item->type}s/{$item->id}") ?>&quot;><img src=&quot;<?= $item->resize_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

<?  if (access::can("view_full", $item)) { ?>
    <tr>
      <th colspan="2"><br/><?= t("Link To The Fullsize Image:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->file_url(true) ?>&quot;>Click Here</a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->file_url(true) ?>&quot;><img src=&quot;<?= $item->thumb_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->file_url(true) ?>&quot;><img src=&quot;<?= $item->resize_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
<? } ?>

    <tr>
      <th colspan="2"><br/><?= t("Link To The Resized Image:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->resize_url(true) ?>&quot;>Click Here</a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->resize_url(true) ?>&quot;><img src=&quot;<?= $item->thumb_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Image:") ?></th>
      <td><input type="text" value="<img src=&quot;<?= $item->resize_url(true) ?>&quot;>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

  </tbody>
</table>
<? } ?>

<? if (module::get_var("embedlinks", "BBCode")) { ?>
<h3 align="center"><?= t("BBCode Links")?></h3>
<table class="gEmbedLinks">
  <tbody>
    <tr>
      <th colspan="2"><?= t("Link To This Page:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="[url=<?= url::abs_site("{$item->type}s/{$item->id}") ?>]Click Here[/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="[url=<?= url::abs_site("{$item->type}s/{$item->id}") ?>][img]<?= $item->thumb_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="[url=<?= url::abs_site("{$item->type}s/{$item->id}") ?>][img]<?= $item->resize_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

<?  if (access::can("view_full", $item)) { ?>
    <tr>
      <th colspan="2"><br/><?= t("Link To The Fullsize Image:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="[url=<?= $item->file_url(true) ?>]Click Here[/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="[url=<?= $item->file_url(true) ?>][img]<?= $item->thumb_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="[url=<?= $item->file_url(true) ?>][img]<?= $item->resize_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>
<? } ?>

    <tr>
      <th colspan="2"><br/><?= t("Link To The Resized Image:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="[url=<?= $item->resize_url(true) ?>]Click Here[/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="[url=<?= $item->resize_url(true) ?>][img]<?= $item->thumb_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Image:") ?></th>
      <td><input type="text" value="[img]<?= $item->resize_url(true) ?>[/img]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

  </tbody>
</table>
<? } ?>











<? if (module::get_var("embedlinks", "FullURL")) { ?>
<h3 align="center"><?= t("URLs")?></h3>
<table class="gEmbedLinks">
  <tbody>
    <tr>
      <th><?= t("This Page:") ?></th>
      <td><input type="text" value="<?= url::abs_site("{$item->type}s/{$item->id}") ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="<?= $item->thumb_url(true) ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="<?= $item->resize_url(true) ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

<?  if (access::can("view_full", $item)) { ?>
    <tr>
      <th><?= t("Fullsize:") ?></th>
      <td><input type="text" value="<?= $item->file_url(true) ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
<? } ?>

  </tbody>
</table>
<? } ?>