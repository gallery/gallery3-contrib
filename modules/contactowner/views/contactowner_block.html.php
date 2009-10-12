<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul id="g-contact-owner">
<? if ($ownerLink != "") { ?>
  <li style="clear: both;">
<? print ($ownerLink); ?>
  </li>
<? } ?>
<? if ($userLink != "") { ?>
  <li style="clear: both;">
<? print ($userLink); ?>
  </li>
<? } ?>

</ul>

