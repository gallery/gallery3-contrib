<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
input[type="text"] {
  width: 95%;
}
</style>
<h1 style="display: none;"><?= t("HTML Code") ?></h1>
<div id="g-embed-links-html-data">
<? $counter = 0; ?>
<? for ($i = 0; $i < count($titles); $i++): ?>    
  <table class="g-links-html" >
  <thead><tr><th colspan="2"><?= t($titles[$i][0]) ?></th></thead>
    <tbody>
          <? for ($j = $counter; $j < $titles[$i][1]+$counter; $j++): ?>    
            <tr>
              <td width="100"><?= t($details[$j][0]) ?></td>
              <td><input type="text" onclick="this.focus(); this.select();" value="<?= $details[$j][1] ?>" readonly></td>
            </tr>
          <? endfor ?>
          <? $counter+= $titles[$i][1]; ?>      
    </tbody>
  </table>
<? endfor ?>
</div>
