<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="add_to_favourites">
<a id="favourites_<?=$item->id?>" class="icon-f<?
  $favselected = false;
  if (isset($favourites))
  {
    if($favourites->contains($item->id)){
      ?> f-selected<?
      $favselected = true;
    }
  }
?>" href="<?= url::site("favourites/toggle_favourites/$item->id") ?>" title="<?=$favselected?t("Remove from favourites"):t("Add to favourites") ?>"></a>
</div>