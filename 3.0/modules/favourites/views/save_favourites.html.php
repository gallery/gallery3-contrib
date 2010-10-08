<?php defined("SYSPATH") or die("No direct script access.");

?><div id="f-save-link"<?
  if (!$favourites->hasFavourites()):
  ?> style="display:none"<?
  endif;?>><a href="<?= url::site("favourites/save") ?>" title="<?= t("Save Favourites") ?>" class="g-dialog-link"></a></div><?
