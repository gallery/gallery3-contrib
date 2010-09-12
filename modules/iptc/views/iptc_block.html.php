<?php defined("SYSPATH") or die("No direct script access.") ?>
<ul class="g-metadata">
  <? for ($i = 0; $i < count($details); $i++): ?>
    <li>
      <strong class="caption"><?= $details[$i]["caption"] ?></strong>
      <?= html::clean($details[$i]["value"]) ?>
    </li>
  <? endfor ?> 
</ul>
