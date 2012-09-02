<?php defined("SYSPATH") or die("No direct script access.") ?>
<style type="text/css">
  #g-photo-detail { /*font-size: .85em;*/ }
  .g-odd { background: #333; color: #fff; padding: 2px 5px; }
  .g-even { background: #222; color: #fff; padding: 2px 5px; }
</style>
<h1><?= t("Photo detail") ?></h1>
<div id="g-photo-detail">
  <table class="g-metadata" >
    <tbody>
      <? for ($i = 0; $i < count($details); $i++): ?>
      <tr>
         <td class="g-even">
         <?= $details[$i]["caption"] ?>
         </td>
         <td class="g-odd">
         <?= html::clean($details[$i]["value"]) ?>
         </td>
       </tr>
       <? endfor ?>
    <? if (isset($tags)): ?>
      <tr>
         <td class="g-even">
             Tags
         </td>
         <td class="g-odd">
            <? foreach ($tags as $key => $tag): ?>
              <a href="<?= $tag->url() ?>"><?= html::clean($tag->name) ?></a>
<? if ($key == (count($tag)-1)) echo "," ?>
            <? endforeach?>
         </td>
       </tr>
      <? endif ?>
    </tbody>
  </table>
</div>
<? if(module::is_active("exif")): ?>
<div style="margin: 25px 0 0; text-align: center;">
<div id="exif_data">
<button class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all"
    onclick="$.get('<?= url::site("exif/show/$item->id")?>',
    function(data){
        $('#exif_data').html(data);
        $('#g-dialog').dialog('option', 'position', 'center');
         });">
   <span class="ui-button-text">Load exif data</span>
</button>
</div></div>
<? endif ?>
