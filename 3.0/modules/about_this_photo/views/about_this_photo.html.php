<?php defined("SYSPATH") or die("No direct script access.") ?>
<? date_default_timezone_set('Australia/ACT'); ?> 
<div class="g-metadata">
<span class="g-about-this">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
    <td><strong class="caption"><?= t("Date:&nbsp;") ?></strong></td>
    <td><?= $date ?></td>
</tr>
<tr>
    <td><strong class="caption"><?= t("Time:&nbsp;") ?></strong></td>
    <td><?= $time ?></td>
</tr>
<tr>
    <td><strong class="caption"><?= t("Views:&nbsp;") ?></strong></td>
    <td><?= $vcount ?></td>
</tr>
<tr>
    <td><strong class="caption"><?= t("Name:&nbsp;") ?></strong></td>
    <td><?= $name ?></td>
</tr>
</table>
  <div style="margin-top: 10px; margin-bottom: 10px;">
    <strong class="caption"><?= t("Caption:&nbsp;") ?></strong>
    <?= $caption ?>
  </div >
  <span >
    <strong class=="caption"><?= t("Tags: &nbsp;&nbsp;") ?></strong>
    <? foreach ($tags as $tag): ?>
    <a href="<?= $tag->url() ?>"><?= html::clean($tag->name) ?></a>,
    <? endforeach?>
  </span ><br>
</span>
</div>
