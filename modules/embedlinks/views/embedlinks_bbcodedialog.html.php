<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
  #gExifData {font-size: .85em;}
  .gOdd {background: #BDD2FF;}
  .gEven {background: #DFEFFC;}
</style>
<h1 style="display: none;"><?= t("BBCode") ?></h1>
<div id="gExifData">
<? $counter = 0; ?>
<? for ($i = 0; $i < count($titles); $i++): ?>    
  <table class="gMetadata" >
  <thead><tr><th cellspan="2"><?= t($titles[$i][0]) ?></th></thead>
    <tbody>
          <? for ($j = $counter; $j < $titles[$i][1]+$counter; $j++): ?>    
            <tr>
              <td><?= t($details[$j][0]) ?></td>
              <td><input type="text" value="<?= $details[$j][1] ?>" readonly></td>
            </tr>
          <? endfor ?>
          <? $counter+= $titles[$i][1]; ?>      
    </tbody>
  </table>
<? endfor ?>
</div>
