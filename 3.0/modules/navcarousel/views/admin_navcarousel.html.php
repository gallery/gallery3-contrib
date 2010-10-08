<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
   function changeaspectstate() {
     var noresizecheck = document.getElementById('noresize');
     var maintainaspectcheck = document.getElementById('maintainaspect');
     changestate = noresizecheck.checked ? maintainaspectcheck.disabled=true : maintainaspectcheck.disabled=false;
   }
window.onload=changeaspectstate;
</script>
<div id="g-admin-navcarousel">
  <h2><?= t("Navigation carousel administration") ?></h2>
  <h3><?= t("Notes:") ?></h3>
  <p><?= t("There is a known bug with the positioning and scrolling of the album.<br />
    If you are experiencing this bug then please enable the option 'Disable dynamic loading of thumbnails'.<br />
    I am working on fixing this bug and will release an update as soon as possible.") ?></p>
  <?= $form ?>
</div>
