<?php defined("SYSPATH") or die("No direct script access.") ?>


<div id="g-admin-moduleupdates" class="g-block">
  <h1> <?= t("Module Updates v3.0") ?> </h1>
  <?= t("Compares your installed module version against the ones provided by <a href=\"http://www.gallerymodules.com\" target=\"_blank\">GalleryModules.com</a>.") ?>
  
  <div class="g-block-content">
  
    <form method="post" action="<?= url::site("admin/moduleupdates") ?>" id="g-configure-moduleupdates-form">
      <input type="hidden" name="csrf" value="<? echo $csrf; ?>"  />
      <fieldset>
        <legend>ModuleUpdates Information</legend>
        <ul>
          <li><?= t("<font color=red>Red = Your version is older than the remote version</font><br>") ?></li>
          <li><?= t("<font color=green>Green = Your version is newer than the remote version</font><br>") ?></li>
          <li><?= t("<font color=orange>Orange = Your file version is newer than the installed version</font><br>") ?></li>
          <li><?= t("<font color=pink>Pink = Your installed version is newer than file version</font><br>") ?></li>
          <li><?= t("<font color=blue>Blue = Does Not Exist/No information available</font><br>") ?></li>
          <li><?= t("Outbound Status: " . $Google . " - GalleryModules.com Status: " . $GalleryModules . "<br>") ?></li>
          <li><input type="submit" value="<?= t("Check Modules for Updates")?>" class="submit" /> <? if($update_time == ""){ echo "&nbsp;- Last Scan: Unknown";}else{ echo "&nbsp;- Last Scan: ".$update_time;} ?></li>
        </ul>
      </fieldset>
    </form>
  
    <br>
    <ul id="g-action-status" class="g-message-block">
      <li class="g-warning"><?= t("Versions are compared from GalleryModules.com (GM).  Most versions downloaded from the forums will not be shown.") ?></li>
    </ul>

    <table>
      <tr>
        <th> <?= t("Module") ?> </th>
        <th> <?= t("Your Version<br>[File/Installed]") ?> </th>
        <th> <?= t("Remote Version") ?> </th>
        <th> <?= t("Description") ?> </th>
      </tr>
      <? foreach ($vars as $module_name): ?>
      <tr class="<?= text::alternate("g-odd", "g-even") ?>">
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= t($module_name['name']) ?> </font> </td>
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= $module_name['code_version'] ?><? if ($module_name['version'] != '') echo "/".$module_name['version']; ?> </font> </td>
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= $module_name['remote_version'] ?> <? if(is_numeric($module_name['remote_version'])) echo "<a href=\"".$module_name['dlink']."\" target=\"_blank\">".$module_name['remote_server']."</a>"; ?> </font> </td>
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= t($module_name['description']) ?> </font> </td>
      </tr>
      <? endforeach ?>
    </table>
  </div>
</div>