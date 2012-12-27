<? if ($theme->page_type != 'basket'){
  if (basket::can_view_orders()){
     ?><a class="g-button ui-icon-left ui-state-default ui-corner-all ui-state-hover" 
     href="<?= url::site("basket/view_Orders") ?>" title="<?= t("View Orders") ?>">
     <span class="ui-icon ui-icon-clipboard"></span><?= t("View Orders")?></a><?
  }
  $item = $theme->item();

  if ($item->is_photo() && product::isForSale($theme->item()->id)){
    ?><p>
    <a class="g-dialog-link g-button ui-icon-left ui-state-default ui-corner-all ui-state-hover" href="<?= url::site("basket/add_to_basket_ajax/$item->id") ?>"
    title="<?= t("Add To Basket")?>"><span class="ui-icon ui-icon-plusthick"></span><?= t("Add To Basket") ?></a></p>
    <?
  }

  if (isset($basket) && isset($basket->contents) && ($basket->size() > 0)) {
    ?>
    <div id="sidebar-basket">
      <table id="gBasketList">
        <tr>
          <th><?= t("Product") ?></th>
          <th><?= t("Cost") ?></th>
          <th></th>
        </tr><?

        $total=0;
        foreach ($basket->contents as $key => $prod_details){
          ?><tr id="" class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
              <td id="item-<?= $prod_details->item ?>" class="core-info"><?
                $item = $prod_details->getItem();
                $width = $item->width;
                ?><img src="<?= $item->thumb_url()?>" title="<?= $item->title?>" alt="<?= $item->title?>" 
                <? if ($width < module::get_var("gallery", "resize_size")):?>
                  style="max-width:60px;"/><br/>
                <? else: ?>
                  style="max-width:90px;"/><br/>
                <? endif; ?>
                <?= html::clean($prod_details->quantity) ?> x <?= html::clean($prod_details->product_name())/*= html::clean($prod_details->product_description())*/ ?></td>
              <td><? $total += $prod_details->cost?><?= basket::formatMoneyForWeb($prod_details->cost); ?></td>
              <td class="g-actions"><a href="<?= url::site("basket/remove_item/$key") ?>" 
                class="g-button2 ui-state-default ui-corner-all ui-icon-left"><span class="ui-icon ui-icon-trash"></span></a></td>
            </tr>
          <?
        }
        ?>
        <tr class="<?= text::alternate("gOddRow", "gEvenRow") ?>">
          <td><b><?= t("Total") ?></b></td>
          <td id="total"><b><?= $basket->ispp()?basket::formatMoneyForWeb($total + $postage):basket::formatMoneyForWeb($total)?></b></td>
          <td></td>
        </tr>
      </table>
    </div><br/>
    <p><a class="g-button right ui-icon-left ui-state-default ui-corner-all ui-state-hover" 
      href="<?= url::site("basket/view_basket") ?>" title="<?= t("Checkout") ?>">
      <span class="ui-icon ui-icon-cart"></span><?= t("Checkout") ?></a></p><?
  }
  else {?>
    <div id="sidebar-basket">
      <?= t("Shopping Basket is Empty") ?>
    </div><?
    }
}
