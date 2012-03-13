<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2011 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<style>
#g-header                    { margin-bottom: 10px; }
#gd-admin                    { position: relative; font-size: 0.9em; }
#gd-admin legend             { width: 99.5%; padding: 0.4em 0.8em; margin-left: -11px; background: url(/themes/greydragon/images/blue-grad.png) #d5e6f2 repeat-x left top; border: #dfdfdf 1px solid;}

.g-admin-left                { float: left; width: 53%; }
.g-admin-right               { float: left; width: 46%; margin-left: 1%; margin-top: 1em; }
.g-admin-right h3            { border-bottom: #a2bdbf 1px solid; margin-top: 0.3em; margin-bottom: 0.3em; }

#gd-admin-head               { position: relative; height: auto; clear: both; display: block; overflow: auto; font-size: 11px; padding: 0.4em 0.8em; background-color: #b7c9d6; border: #a2bdbf 1px solid; }
#gd-admin-title              { float: left; color: #333v42; font-weight: bold; font-size: 1.6em; text-shadow: #deeefa 0 1px 0; }
#gd-admin-hlinks ul          { float: right; margin-top: 0.4em; font-size: 11px; }
#gd-admin-hlinks li          { list-style-type: none; float: left; color: #618299; display: inline; }
#gd-admin-hlinks a           { font-weight: bold; font-size: 13px; }

#gd-admin form              { border: none; }
#gd-admin fieldset          { border: #ccc 1px solid; }
#gd-admin input.g-error     { padding-left: 30px; border: none; }
#gd-admin input.g-success   { background-color: transparent; }
#gd-admin input.g-warning   { background-color: transparent; border: none; }
#gd-admin p.g-error         { padding-left: 30px; border: none; margin-bottom: 0; background-image: none; }

#g-content                  { padding: 0 1em; width: 97%; font-size: 1em; }
#g-content form ul li input  { display: inline; float: left; margin-right: 0.8em; }
#g-content form ul li select { display: inline; float: left; margin-right: 0.8em; width: 35%; padding: 0 0 0 .2em; }
#g-content form ul li input[type='text'] { width: 35%; }
#g-content form ul li textarea { height: 6em; }
#g-content form input[type="submit"] { border: #5b86ab 2px solid; padding: 0.3em; color: #fff; background: url(/themes/greydragon/images/button-grad-vs.png) #5580a6 repeat-x left top; }
#g-content form input[type="submit"]:hover,
input.ui-state-hover { background-image: url(/themes/greydragon/images/button-grad-active-vs.png); border-color: #2e5475; color: #eaf2fa !important; }
#g-content form #vercheck, #g-content form #shadowbox, #g-content form #organizecheck { display: none; }
</style>

<script>
  $(document).ready( function() {
    $('form').submit( function() {
      $('input[type=submit]', this).attr('disabled', 'disabled');
    });
  });
</script>

<?
  if ($is_module):
    $admin_info = new ArrayObject(parse_ini_file(MODPATH   . $name . "/module.info"), ArrayObject::ARRAY_AS_PROPS);
    $version = number_format($admin_info->version / 10, 1, '.', '');
  else:
    $admin_info = new ArrayObject(parse_ini_file(THEMEPATH . $name . "/theme.info"), ArrayObject::ARRAY_AS_PROPS);
    $version = $admin_info->version;
  endif;
?>

<div id="gd-admin" class="g-block">
  <div id="gd-admin-head">
    <div id="gd-admin-title"><?= t($admin_info->name) ?> - <?= $version ?></div>
    <div id="gd-admin-hlinks">
      <ul><li><a href="http://codex.gallery2.org/Gallery3:Themes:pear4gallery3" target="_blank"><?= t("Home") ?></a>&nbsp;|&nbsp;</li>
        <li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=RX4UUYVJ5D7TY&lc=SE&item_name=Pear4Gallery&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank"><?= t("Beer found") ?></a>&nbsp;|&nbsp;</li>
        <? if (isset($admin_info->support)): ?>
        <li><a href="<?= $admin_info->support;  ?>" target="_blank"><?= t("Support") ?></a>&nbsp;|&nbsp;</li>
        <? endif; ?>
        <? if (isset($admin_info->download)): ?>
        <li><a href="<?= $admin_info->download; ?>" target="_blank"><?= t("Download") ?></a>&nbsp;|&nbsp;</li>
        <? endif; ?>
      </ul>
    </div>
  </div>
  <div class="g-block-content g-admin-left">
  <?= $form ?>
  </div>
  <div class="g-admin-right">
  <?= $help ?>
  </div>
</div>
