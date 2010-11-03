<?php defined("SYSPATH") or die("No direct script access.") ?>
<?

    $menu = Menu::factory("root");
    module::event("site_menu", $menu, $this->theme, "");

?>
<? if( isset($menu->elements['add_menu']->elements) || isset($menu->elements['options_menu']->elements) ): ?>
  <div id="g-metadata" class="g-block">
    <h2><?= t("Actions") ?></h2>
    <div class="g-block-content">
      <ul class="g-metadata">
        <? foreach($menu->elements['add_menu']->elements as $menu_element): ?>
        <li><a href="<?= $menu_element->url ?>" class="g-dialog-link"><?= $menu_element->label ?></a></li>
        <? endforeach; ?>
        <? foreach($menu->elements['options_menu']->elements as $menu_element): ?>
        <li><a href="<?= $menu_element->url ?>" class="g-dialog-link"><?= $menu_element->label ?></a></li>
        <? endforeach; ?>
      </ul>
    </div>
  </div>
<? endif; ?>