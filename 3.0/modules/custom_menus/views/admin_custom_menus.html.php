<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-block">
  <h1> <?= t("Manage menus") ?> </h1>
  <div class="g-block-content">
    <a href="<?= url::site("admin/custom_menus/form_create/0") ?>" class="g-dialog-link g-create-link"><?= t("Add new menu") ?></a>
    <?= $menu_list ?>
  </div>
</div>
