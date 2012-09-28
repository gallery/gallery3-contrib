<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
   function toggle() {
     var checkbox = document.getElementById('autoscroll');
     var toggle = document.getElementById('auto');
     var toggle1 = document.getElementById('speed');
     updateToggle = checkbox.checked ? toggle.disabled=false : toggle.disabled=true;
     updateToggle1 = checkbox.checked ? toggle1.disabled=false : toggle1.disabled=true;
   }
window.onload=toggle;
</script>
<div id="g-admin-carousel">
  <h2><?= t("Carousel Administration") ?></h2>
    <p><?= t("Change settings below for the different parameters.") ?></p>
  <?= $form ?>
  
  <div class="g-block">
  <hr />
  <h3><?= t("Notes:") ?></h3>
  <p>
    <?= t("Navigation buttons are hard to style and clutter the user interface.") ?>
    <br />
    <?= t("Use mouse wheel to scroll through the images.") ?>
  </p>
  </div>
</div>
