<?php defined("SYSPATH") or die("No direct script access.");

if ($theme->page_type != 'favourites'):

?><div id="f-view-link"<?
  if (!$favourites->hasFavourites()):
  ?> style="display:none"<?
  endif;?>><a href="<?= url::site("favourites") ?>" title="<?= t("View Favourites") ?>"></a></div><?

endif;