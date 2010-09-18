<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= t("Tags:") ?>
<? foreach ($tags as $tag): ?>
 <a href="<?= url::site("tag/{$tag->name}") ?>"><?= $tag->name ?></a>
<? endforeach ?>
