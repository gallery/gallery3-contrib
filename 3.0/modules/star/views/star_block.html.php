<?php defined("SYSPATH") or die("No direct script access.") ?>

<? if ($theme->item->is_photo()) { ?>
<? if (star::is_starred($theme->item)) { ?>
<? $csrf = access::csrf_token(); ?>

<script type="text/javascript">
function unstar_item() {
  var http = false;

  if(navigator.appName == "Microsoft Internet Explorer") {
    http = new ActiveXObject("Microsoft.XMLHTTP");
  } else {
    http = new XMLHttpRequest();
  }

  http.open("GET", "<?= url::site("display/unstar/{$theme->item->id}?csrf={$csrf}") ?>");
  http.onreadystatechange=function() { }
  http.send(null);
  window.location.reload();
}
</script>

<div class="g-star-block">
<a href="javascript: unstar_item()"
   title="<?= t("Unstar Image") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Unstar Image") ?></a>
</div>
<? } ?>
<? } ?>

<? if ($theme->item->is_photo()) { ?>
<? if (!star::is_starred($theme->item)) { ?>
<? $csrf = access::csrf_token(); ?>

<script type="text/javascript">
function star_item() {
  var http = false;

  if(navigator.appName == "Microsoft Internet Explorer") {
    http = new ActiveXObject("Microsoft.XMLHTTP");
  } else {
    http = new XMLHttpRequest();
  }

  http.open("GET", "<?= url::site("display/star/{$theme->item->id}?csrf={$csrf}") ?>");
  http.onreadystatechange=function() { }
  http.send(null);
  window.location.reload();
}
</script>

<div class="g-star-block">
<a href="javascript:star_item()"
   title="<?= t("Star Image") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Star Image") ?></a>
</div>
<? } ?>
<? } ?>

<? if (false) { //old style. Notifications worked, but gave you a download popup and required manual refresh to see change.?>
<? if (!star::is_starred($theme->item)) { ?>
<? $csrf = access::csrf_token(); ?>
<div class="g-star-block">
<a href="<?= url::site("display/star/{$theme->item->id}?csrf={$csrf}") ?>"
   ajax_handler="function(data) { window.location.reload() }"
   title="<?= t("Star options") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Star Image") ?></a>
</div>
<? } ?>
<? } ?>





<? if (star::show_only_starred_items()) { ?>
<? $csrf = access::csrf_token(); ?>
<div class="g-download-fullsize-block">

<script type="text/javascript">
function star_only_off() {
  var http = false;

  if(navigator.appName == "Microsoft Internet Explorer") {
    http = new ActiveXObject("Microsoft.XMLHTTP");
  } else {
    http = new XMLHttpRequest();
  }

  http.open("GET", "<?= url::site("display/star_only_off/?csrf={$csrf}") ?>");
  http.onreadystatechange=function() { }
  http.send(null);
  window.location.reload();
}
</script>

<a href="javascript:star_only_off()"
   title="<?= t("Show all items.") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Show all items.") ?></a>
</div>
<? } ?>

<? if (!star::show_only_starred_items()) { ?>
<? $csrf = access::csrf_token(); ?>
<div class="g-download-fullsize-block">

<script type="text/javascript">
function star_only_on() {
  var http = false;

  if(navigator.appName == "Microsoft Internet Explorer") {
    http = new ActiveXObject("Microsoft.XMLHTTP");
  } else {
    http = new XMLHttpRequest();
  }

  http.open("GET", "<?= url::site("display/star_only_on/?csrf={$csrf}") ?>");
  http.onreadystatechange=function() { }
  http.send(null);
  window.location.reload();
}
</script>

<a href="javascript:star_only_on()"
   title="<?= t("Show only starred.") ?>"
  class="g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Show only starred.") ?></a>
</div>
<? } ?>


<? //Buttons were like the below. The notification message worked when doing this, but you got a popup to download the response, and had to manually reload. Something was wrong.?>
<? if (false) { ?>
<? $csrf = access::csrf_token(); ?>
<div class="g-download-fullsize-block">
<a href="<?= url::site("display/star_only_on/?csrf={$csrf}") ?>"
   ajax_handler="function(data) { window.location.reload() }"
   title="<?= t("Show only starred.") ?>"
  class="g-ajax-link g-button ui-icon-left ui-state-default ui-corner-all"><?= t("Show only starred.") ?></a>
</div>
<? } ?>
