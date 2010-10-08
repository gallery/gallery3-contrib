<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
input[type="text"] {
  width: 95%;
}
</style>
      
<? if (module::get_var("embedlinks", "HTMLCode")) { ?>
<h3 align="center"><?= t("HTML Links")?></h3>
<table class="g-embed-links">
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

<? if ($item->is_photo()) { ?>
    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= url::abs_site("{$item->type}s/{$item->id}") ?>&quot;><img src=&quot;<?= $item->resize_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
<? } ?>

<?  if (access::can("view_full", $item)) { ?>
    <tr>
      <? if ($item->is_movie()) { ?>
      <th colspan="2"><br/><?= t("Link To The Video File:") ?></th>
      <? } else { ?>
      <th colspan="2"><br/><?= t("Link To The Full Size Image:") ?></th>
      <? }?>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->file_url(true) ?>&quot;>Click Here</a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->file_url(true) ?>&quot;><img src=&quot;<?= $item->thumb_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
    
  <? if ($item->is_photo()) { ?>
    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="<a href=&quot;<?= $item->file_url(true) ?>&quot;><img src=&quot;<?= $item->resize_url(true) ?>&quot;></a>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
  <? } ?>
  
  <? if ($item->is_movie()) { ?>
    <tr>
      <th><?= t("Embed:") ?></th>
      <td><input type="text" value="<object width=&quot;<?= $item->width ?>&quot; height=&quot;<?= $item->height ?>&quot; data=&quot;<?= url::abs_file("lib/flowplayer.swf") ?>&quot; type=&quot;application/x-shockwave-flash&quot;><param name=&quot;movie&quot; value=&quot;<?= url::abs_file("lib/flowplayer.swf") ?>&quot; /><param name=&quot;allowfullscreen&quot; value=&quot;true&quot; /><param name=&quot;allowscriptaccess&quot; value=&quot;always&quot; /><param name=&quot;flashvars&quot; value='config={&quot;plugins&quot;:{&quot;pseudo&quot;:{&quot;url&quot;:&quot;flowplayer.h264streaming.swf&quot;},&quot;controls&quot;:{&quot;backgroundColor&quot;:&quot;#000000&quot;,&quot;backgroundGradient&quot;:&quot;low&quot;}},&quot;clip&quot;:{&quot;provider&quot;:&quot;pseudo&quot;,&quot;url&quot;:&quot;<?= $item->file_url(true) ?>&quot;},&quot;playlist&quot;:[{&quot;provider&quot;:&quot;pseudo&quot;,&quot;url&quot;:&quot;<?= $item->file_url(true) ?>&quot;}]}' /></object>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
  <? } ?>
<? } ?>

<? if ($item->is_photo()) { ?>
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
<? } ?>
  </tbody>
</table>
<? } ?>

<? if (module::get_var("embedlinks", "BBCode")) { ?>
<h3 align="center"><?= t("BBCode Links")?></h3>
<table class="g-embed-links">
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

  <? if ($item->is_photo()) { ?>
    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="[url=<?= url::abs_site("{$item->type}s/{$item->id}") ?>][img]<?= $item->resize_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>
  <? } ?>
  
<?  if (access::can("view_full", $item)) { ?>
    <tr>
      <? if ($item->is_movie()) { ?>
      <th colspan="2"><br/><?= t("Link To The Video File:") ?></th>
      <? } else { ?>
      <th colspan="2"><br/><?= t("Link To The Full Size Image:") ?></th>
      <? }?>
    </tr>

    <tr>
      <th><?= t("Text:") ?></th>
      <td><input type="text" value="[url=<?= $item->file_url(true) ?>]Click Here[/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="[url=<?= $item->file_url(true) ?>][img]<?= $item->thumb_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>

  <? if ($item->is_photo()) { ?>
    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="[url=<?= $item->file_url(true) ?>][img]<?= $item->resize_url(true) ?>[/img][/url]" onclick="this.focus(); this.select();" readonly></td>
    </tr>
  <? } ?>
<? } ?>
  
  <? if ($item->is_photo()) { ?>
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
  <? } ?>
  </tbody>
</table>
<? } ?>

<? if (module::get_var("embedlinks", "FullURL")) { ?>
<h3 align="center"><?= t("URLs")?></h3>
<table class="g-embed-links">
  <tbody>
    <tr>
      <th><?= t("This Page:") ?></th>
      <td><input type="text" value="<?= url::abs_site("{$item->type}s/{$item->id}") ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

    <tr>
      <th><?= t("Thumbnail:") ?></th>
      <td><input type="text" value="<?= $item->thumb_url(true) ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>

  <? if ($item->is_photo()) { ?>
    <tr>
      <th><?= t("Resized:") ?></th>
      <td><input type="text" value="<?= $item->resize_url(true) ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
  <? } ?>
  
<?  if (access::can("view_full", $item)) { ?>
    <tr>
      <? if ($item->is_movie()) { ?>
      <th><?= t("Video File:") ?></th>
      <? } else { ?>
      <th><?= t("Full size:") ?></th>
      <? } ?>
      <td><input type="text" value="<?= $item->file_url(true) ?>" onclick="this.focus(); this.select();" readonly></td>
    </tr>
<? } ?>

  </tbody>
</table>
<? } ?>