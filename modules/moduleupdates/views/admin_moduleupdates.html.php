<?php defined("SYSPATH") or die("No direct script access.") ?>

<div id="g-admin-moduleupdates" class="g-block">
  <h1> <?= t("Module Updates") ?> </h1>
  <p>
  <?= t("Compares your installed module version against the ones stored in the GitHub.<br><br>") ?>
	<?= t("<font color=red>Red = Out of Date</font><br>") ?>
	<?= t("<font color=green>Green = Your version is newer</font><br>") ?>
	<?= t("<font color=blue>Blue = Does Not Exist/No information available</font><br>") ?>
  </p>

  <ul id="g-action-status" class="g-message-block">
    <li class="g-warning"><?= t("Versions are compared from the official Gallery3 (G3) and official Gallery3 Community Contributions (G3CC).  Versions downloaded from the forums will not be shown.") ?></li>
  </ul>

  <div class="g-block-content">
    <table>
      <tr>
        <th> <?= t("Module") ?> </th>
        <th> <?= t("Your Version") ?> </th>
        <th> <?= t("Remote Version") ?> </th>
        <th> <?= t("Description") ?> </th>
      </tr>
      <? foreach ($vars as $module_name): ?>
      <tr class="<?= text::alternate("g-odd", "g-even") ?>">
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= $module_name['name'] ?> </font> </td>
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= $module_name['code_version'] ?> </font> </td>
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= $module_name['remote_version'] ?> <?= $module_name['remote_server'] ?> </font> </td>
        <td> <? echo "<font color=".$module_name['font_color'].">"; ?> <?= $module_name['description'] ?> </font> </td>
      </tr>
      <? endforeach ?>
    </table>
  </div>
</div>
<pre>
</pre>