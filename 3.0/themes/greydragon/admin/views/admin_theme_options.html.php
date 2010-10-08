<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
#g-header                    { margin-bottom: 10px; }
#gd-admin                    { position: relative; font-size: 0.9em; }
#gd-admin legend             { width: 99.5%; padding: 0.4em 0.8em; margin-left: -11px; background: url(/themes/greydragon/images/blue-grad.png) #d5e6f2 repeat-x left top; border: #dfdfdf 1px solid;}

#g-theme-options-form        { border: none; }
#g-theme-options-form fieldset { border: #ccc 1px solid; }
#g-theme-options-form input.g-error   { padding-left: 30px; border: none; }
#g-theme-options-form input.g-success { background-color: transparent; }
#g-theme-options-form input.g-warning { background-color: transparent; border: none; }
#g-theme-options-form p.g-error       { padding-left: 30px; border: none; margin-bottom: 0; background-image: none; }

.g-admin-left                { float: left; width: 53%; }
.g-admin-right               { float: left; width: 46%; margin-left: 1%; margin-top: 1em; }
.g-admin-right h3            { border-bottom: #a2bdbf 1px solid; margin-top: 0.3em; margin-bottom: 0.3em; }

#gd-admin-head               { position: relative; height: auto; clear: both; display: block; overflow: auto; font-size: 11px; padding: 0.4em 0.8em; background-color: #b7c9d6; border: #a2bdbf 1px solid; }
#gd-admin-title              { float: left; color: #333v42; font-weight: bold; font-size: 1.6em; text-shadow: #deeefa 0 1px 0; }
#gd-admin-hlinks ul          { float: right; margin-top: 0.4em; font-size: 11px; }
#gd-admin-hlinks li          { list-style-type: none; float: left; color: #618299; display: inline; }
#gd-admin-hlinks a           { font-weight: bold; font-size: 13px; }

#g-content                   { padding: 0 1em; width: 97%; font-size: 1em; }
#g-content form ul li input  { display: inline; float: left; margin-right: 0.8em; } 
#g-content form ul li select { display: inline; float: left; margin-right: 0.8em; width: 10em; padding: 0 0 0 .2em; }
#g-content form ul li input[type='text'] { width: 50%; }
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

<div id="gd-admin" class="g-block">
  <div id="gd-admin-head">
    <div id="gd-admin-title"><?= $name ?> - <?= $version ?></div>
    <div id="gd-admin-hlinks">
      <ul><li><a href="http://blog.dragonsoft.us/gallery-3/" target="_blank"><?= t("Home") ?></a>&nbsp;|&nbsp;</li>
        <li><a href="http://gallery.menalto.com/node/91519" target="_blank"><?= t("Support") ?></a>&nbsp;|&nbsp;</li>
        <li><a href="http://codex.gallery2.org/Gallery3:Themes:greydragon" target="_blank"><?= t("Download") ?></a>&nbsp;|&nbsp;</li>
        <li><a href="http://gallery.menalto.com/gallery/g3demosites/" target="_blank"><?= t("Vote") ?></a>&nbsp;|&nbsp;</li>
        <li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9MWBSVJMWMJEU" target="_blank"><?= t("Donate") ?></a>&nbsp;</li>
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
