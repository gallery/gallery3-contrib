<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if (module::get_var("pages", "show_sidebar")) : ?>
  <style type="text/css">
    <? if (module::get_var("gallery", "active_site_theme") == "greydragon") : ?>
    #g-column-right {
      display: none;
    }
    .g-page-block-content {
      width: 99%;
    }
    <? else: ?>
    #g-sidebar {
      display: none;
    }
    #g-content {
      width: 950px;
    }
    <? endif ?>
  </style>
<? endif ?>
<div class="g-page-block">
  <h1> <?= t($title) ?> </h1>
  <div class="g-page-block-content">
    <?=t($body) ?>
  </div>
</div>
