<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul id="gContactOwner">
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

