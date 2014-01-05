<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="add_to_basket">
<a href="<?= url::site("basket_plus/add_to_basket_ajax/$item->id") ?>" title="<?= t("Add To basket") ?>" class="g-dialog-link">
<?= t("Add To basket") ?></a>
</div>