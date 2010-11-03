<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= t("Tags:") ?>
<? $i = 0 ?>
<? foreach ($tags as $tag): ?>
<?= (++$i != 1) ? ", " : " " ?>
<a href="<?= url::site("tag/{$tag->name}") ?>"><?= $tag->name ?></a><? endforeach ?>
