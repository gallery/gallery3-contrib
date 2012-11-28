<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-exif-gps-admin">
  <h2> <?= t("EXIF GPS Settings") ?> </h2>
  <br /><div><?=t("You may sign up for a Google APIs Console key"); ?> <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_new">here</a>.<br /></div>  
  <?= $exifgps_form ?>
  <br /><br /><div><strong><?=t("Default Zoom Level:"); ?></strong> <?=t("This value represents how far zoomed in or out the map will start out at.  A value of 0 (the coarsest) will zoom the map all of the way out, while higher numbers will zoom the map further in.  Depending on the map type, the highest zoom value you can use will be around 19-23."); ?></div>
</div>
