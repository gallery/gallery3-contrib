<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= $theme->dynamic_top() ?>
<?/* Treat dynamic pages just lite album pages. */ ?>
<?= new View("album.html") ?>
<?= $theme->dynamic_bottom() ?>

