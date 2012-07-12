<?php defined("SYSPATH") or die("No direct script access.") ?>
<center>
  <? if (count($details) > 0): ?>
    <a href="javascript:void(0);" onclick="showFotomotoDialog(<?=$details[0][0]; ?>);" style="font-weight: bold; font-size: 14px;"><?=$details[0][1]; ?></a>
    <? for ($i = 1; $i < count($details); $i++): ?>    
      <span style="font-weight: bold; font-size: 14px;"> | </span>
      <a href="javascript:void(0);" onclick="showFotomotoDialog(<?=$details[$i][0]; ?>);" style="font-weight: bold; font-size: 14px;"><?=$details[$i][1]; ?></a>
    <? endfor ?>
  <? endif; ?>
</center>
<script>
  function showFotomotoDialog(window_type) {
    FOTOMOTO.API.showWindow(window_type, "<?= url::abs_site("fotomotorw/resize/" . md5($item->created) . "/{$item->id}"); ?>");
  }
</script>
