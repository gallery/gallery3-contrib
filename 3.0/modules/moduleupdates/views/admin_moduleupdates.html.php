<?php defined("SYSPATH") or die("No direct script access.") ?>


<div id="g-admin-moduleupdates" class="g-block">
  <h1> <?= t("Module Updates v".$mu_version.".0") ?> </h1>
  <?= t("Compares your installed module version against the ones stored in the GitHub and GalleryModules.") ?>
  
  <div class="g-block-content">
  
    <form method="post" action="<?= url::site("admin/moduleupdates") ?>" id="g-configure-moduleupdates-form">
      <input type="hidden" name="csrf" value="<? echo $csrf; ?>"  />
      <fieldset>
        <legend>ModuleUpdates Information</legend>
        <ul>
          <li><?= t("<font color=red>Red = Your version is older than the GitHub</font><br>") ?></li>
          <li><?= t("<font color=green>Green = Your version is newer than the GitHub</font><br>") ?></li>
          <li><?= t("<font color=orange>Orange = Your file version is newer than the installed version</font><br>") ?></li>
          <li><?= t("<font color=pink>Pink = Your installed version is newer than file version</font><br>") ?></li>
          <li><?= t("Outbound Status: " . $Google . " - GitHub Status: " . $GitHub . "<br>") ?></li>
          <li><input type="submit" value="<?= t("Check Modules for Updates")?>" class="submit" /> <? if($update_time == ""){ echo "&nbsp;- Last Scan: Unknown";}else{ echo "&nbsp;- Last Scan: ".$update_time;} ?></li>
        </ul>
      </fieldset>
    </form>
  
    <br>
    <ul id="g-action-status" class="g-message-block">
      <li class="g-warning"><?= t("Versions are compared from GitHub for Gallery Core (GC), Gallery Community Contributions (GCC), and GalleryModules.com (GM).  Versions downloaded from the forums will not be shown.") ?></li>
    </ul>
	<fieldset>
		<legend>Core Modules</legend>
		<table>
		  <tr>
			<th> <?= t("Module")                        ?> </th>
			<th><center> <?= t("Local /<br>Installed")  ?> </center></th>
			<th width=50><center> <?= t("GC")           ?> </center></th>
			<th> <?= t("Description")                   ?> </th>
		  </tr>
		  <? foreach ($vars as $module_name): ?>
        <? if(is_numeric($module_name['core_version'])) { ?>
          <tr class="<?= text::alternate("g-odd", "g-even") ?>">
            <td> <?= t($module_name['name'])                                    ?> </td>
            <td align=center> <? echo "<font color=".$module_name['font_color_local'].">";   ?> <? if($module_name['font_color_local'] != "black"){ echo "<b><u>*"; } ?> <? if (is_numeric($module_name['code_version'])) echo $module_name['code_version']; ?><? if (is_numeric($module_name['version'])) echo " / ".$module_name['version']; ?> <? if($module_name['font_color_local'] != "black"){ echo "*</b></u>"; } ?> </td>
            <td align=center> <? echo "<font color=".$module_name['font_color_core'].">";    ?> <? if($module_name['font_color_core'] != "black" and $module_name['font_color_core'] != "" ){ echo "<b><u>*"; }        ?> <? if(is_numeric($module_name['core_version'])) if($module_name['core_version'] > $module_name['code_version']) { echo "<a href=\"".$module_name['core_dlink']."\" target=\"_blank\">".$module_name['core_version']."</a>";} else { echo $module_name['core_version']; }                ?> <? if($module_name['font_color_core'] != "black"){ echo "*</b></u>"; }     ?> </font> </td>
            <td> <?= t($module_name['description'])                             ?> </td>
          </tr>
        <? } ?>
		  <? endforeach ?>
		</table>
	</fieldset>
	<fieldset>
		<legend>Community Contributed Modules</legend>
		<table>
		  <tr>
			<th> <?= t("Module")                        ?> </th>
			<th><center> <?= t("Local /<br>Installed")  ?> </center></th>
			<th width=50><center> <?= t("GCC")          ?> </center></th>
			<th width=85><center> <?= t("GM")           ?> </center></th>
			<th> <?= t("Description")                   ?> </th>
		  </tr>
		  <? foreach ($vars as $module_name): ?>
        <? if(is_numeric($module_name['contrib_version']) or is_numeric($module_name['gh_version'])) { ?>
          <tr class="<?= text::alternate("g-odd", "g-even") ?>">
            <td> <?= t($module_name['name'])                                                 ?> </td>
            <td align=center> <? echo "<font color=".$module_name['font_color_local'].">";   ?> <? if($module_name['font_color_local'] != "black"){ echo "<b><u>*"; }                                                  ?> <? if (is_numeric($module_name['code_version'])) echo $module_name['code_version']; ?><? if (is_numeric($module_name['version'])) echo " / ".$module_name['version']; ?> <? if($module_name['font_color_local'] != "black"){ echo "*</b></u>"; } ?> </td>
            <td align=center> <? echo "<font color=".$module_name['font_color_contrib'].">"; ?> <? if($module_name['font_color_contrib'] != "black" and $module_name['font_color_contrib'] != "" ){ echo "<b><u>*"; }  ?> <? if(is_numeric($module_name['contrib_version'])) if($module_name['contrib_version'] > $module_name['version'] or $module_name['core_version'] > $module_name['code_version']) { echo "<a href=\"".$module_name['contrib_dlink']."\" target=\"_blank\">".$module_name['contrib_version']."</a>";} else { echo $module_name['contrib_version']; } ?> <? if($module_name['font_color_contrib'] != "black"){ echo "*</b></u>"; }  ?> </font> </td>
            <td align=center> <? echo "<font color=".$module_name['font_color_gh'].">";      ?> <? if($module_name['font_color_gh'] != "black" and $module_name['font_color_gh'] != "" ){ echo "<b><u>*"; }            ?> <? if(is_numeric($module_name['gh_version'])) if($module_name['gh_version'] > $module_name['version'] or $module_name['core_version'] > $module_name['code_version']) { echo "<a href=\"".$module_name['gh_dlink']."\" target=\"_blank\">".$module_name['gh_version']."</a>";} else { echo $module_name['gh_version']; }                          ?> <? if($module_name['font_color_gh'] != "black"){ echo "*</b></u>"; }       ?> </font> </td>
            <td> <?= t($module_name['description'])                                          ?> </td>
          </tr>
        <? } ?>
		  <? endforeach ?>
		  </table>
	</fieldset>
  </div>
</div>