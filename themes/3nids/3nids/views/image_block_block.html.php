<?php defined("SYSPATH") or die("No direct script access.") ?>

<? $fancymodule = ""; ?>
<? if (module::is_active("exif")){$fancymodule .= "exif::" . url::site("exif/show/{$item->id}") . ";;";} ?>
<? if (module::is_active("comment") && module::is_active("comment_3nids")){$fancymodule .= "comment::" . url::site("comments_3nids?item_id={$item->id}") . ";;comment_count::" . comment_3nids::count($item) . ";;" ;} ?>

<div class="gImageBlock">
   <a href="<?= $item->file_url() ?>" class="fancyclass" title="<?= $item->parent()->title ?>, <?=$item->parent()->description?>" name="<?=$fancymodule?>">
   <?= $item->thumb_img(array("class" => "gThumbnail")) ?>
  </a>

 
   <div class="gParentAlbum">
       <a href="<?= $item->parent()->url() ?>?show=<?= $item->id?>"><h4><span></span><?= $item->parent()->title ?></h4></a>
  </div>
</div>
