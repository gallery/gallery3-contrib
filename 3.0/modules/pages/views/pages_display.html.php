<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if (module::get_var("pages", "show_sidebar")) : ?>
  <style type="text/css">
  #g-sidebar {
    display: none;
  }
  #g-content {
    width: 950px;
  }
  </style>
<? endif ?>
<div class="g-page-block">
  <h1> <?= $title ?> </h1>
  <div class="g-page-block-content">
    <?=$body ?>
  </div>
</div>
