<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
input[type="text"] {
  width: 95%;
}
</style>
<? if (module::get_var("embedlinks", "HTMLCode")) { ?>
<table class="g-embed-links">
  <tbody>
    <tr>
      <th colspan="2"><?= t("HTML Links:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input onclick="this.focus(); this.select();" name="forum" type="text" readonly="true" value="<a href=&quot;<?= url::abs_site("{$item->type}s/{$item->id}") ?>&quot;>Click Here</a>" /></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input onclick="this.focus(); this.select();" name="forum" type="text" readonly="true" value="<a href=&quot;<?= url::abs_site("{$item->type}s/{$item->id}") ?>&quot;><img src=&quot;<?= $item->thumb_url(true) ?>&quot;></a>" /></td>
    </tr>
  </tbody>
</table>
<? } ?>

<? if (module::get_var("embedlinks", "BBCode")) { ?>
<table class="g-embed-links">
  <tbody>
    <tr>
      <th colspan="2"><?= t("BBCode Links:") ?></th>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input onclick="this.focus(); this.select();" name="forum" type="text" readonly="true" value="[url=<?= url::abs_site("{$item->type}s/{$item->id}") ?>]Click Here[/url]" /></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input onclick="this.focus(); this.select();" name="forum" type="text" readonly="true" size="85" value="[url=<?= url::abs_site("{$item->type}s/{$item->id}") ?>][img]<?= $item->thumb_url(true) ?>[/img][/url]" /></td>
    </tr>
  </tbody>
</table>
<? } ?>

<? if (module::get_var("embedlinks", "FullURL")) { ?>
<table class="g-embed-links">
  <tbody>
    <tr>
      <th colspan="2"><?= t("URLs:") ?></th>
    </tr>

    <tr>
      <th><?= t("Album URL:") ?></th>
      <td><input onclick="this.focus(); this.select();" name="forum" type="text" readonly="true" value="<?= url::abs_site("{$item->type}s/{$item->id}") ?>" /></td>
    </tr>
    
    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input onclick="this.focus(); this.select();" name="forum" type="text" readonly="true" size="85" value="<?= $item->thumb_url(true) ?>" /></td>
    </tr>

  </tbody>
</table>
<? } ?>
