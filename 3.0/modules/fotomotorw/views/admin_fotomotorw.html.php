<?php defined("SYSPATH") or die("No direct script access.") ?>

<div class="g-block">
  <h1> <?= t("Fotomoto settings") ?> </h1>
  <div class="g-block-content">

  <table>
    <tr>
      <th> <?= t("Fotomoto Site Key") ?></th>
    </tr>
    <tr class="setting-row g-odd">
      <td>
        <a href="<?= url::site("admin/advanced_settings/edit/fotomotorw/fotomoto_site_key"); ?>" class="g-dialog-link">
          <? if (module::get_var("fotomotorw", "fotomoto_site_key", "") == ""): ?>
          <i> <?= t("Click to add your Fotomoto Site Key") ?> </i>
          <? else: ?>
          <?= module::get_var("fotomotorw", "fotomoto_site_key"); ?>
          <? endif; ?>
      </a>
      </td>
    </tr>
  </table>
  <p>(Log in to the <a href="http://my.fotomoto.com/">Fotomoto Dashboard</a> to get your Site Key.)</p>

  <table>
    <tr>
      <th> <?= t("Fotomoto Display Options") ?></th>
    </tr>
    <tr>
      <td>
        <?=$display_form; ?>
      </td>
    </tr>
  </table>
  
  <table>
    <tr>
      <th> <?= t("Fotomoto Private Key") ?> 
      (<a href="<?= url::site("admin/fotomotorw/reset_private_key"); ?>"><?= t("Reset Private Key"); ?></a>)</th>
    </tr>
    <tr class="setting-row g-odd">
      <td><?= module::get_var("fotomotorw", "fotomoto_private_key"); ?>
      </td>
    </tr>
  </table>
  </div>
</div>

<div class="g-block">
  <h1> <?= t("Configuring Auto Pickup") ?> </h1>
  <?
    if (module::get_var("fotomotorw", "fotomoto_private_key") == "") {
      print t("Please click the \"Reset Private Key\" link above to continue.");
    } else {
  ?>
    <?= t("Step 1: Log into your Fotomoto account and select Settings -> Auto Pickup, or <a href=\"http://my.fotomoto.com/cloud_storage_interfaces\" target=\"_blank\">click here</a>."); ?><br />
    <?= t("Step 2: Select \"Create New Profile\"."); ?><br />
    <?= t("Step 3: Enter in a Profile Name, and set \"Storage Type\" to \"HTTP\".  Leave \"Username\" and \"Password\" blank."); ?><br />
    <?= t("Step 4: For \"Host\", enter in \"") . substr(url::abs_site(), 0, strpos(substr(url::abs_site(), 7), "/")+7); ?>"<br />
    <?= t("Step 5: For \"Path\", enter in \"") . str_replace(Kohana::config('core.url_suffix'), "", substr(url::site("fotomotorw/print_proxy/" . module::get_var("fotomotorw", "fotomoto_private_key")), 1)) . "\""; ?><br />
    <?= t("Step 6: For \"Filename Lookup Pattern\", enter in \"FILENAME.EXT\"."); ?><br />
    <?= t("Step 7: Press \"Save Profile\" to finish."); ?>
  <? } ?>
</div>
