<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-mptt-tree">
  <h2>
    <?= t("MPTT Tree Visualizer") ?>
  </h2>
  <div id="g-mptt">
  <? if (empty($url)): ?>
    <pre><?= $tree ?></pre>
  <? else: ?>
    <object type="image/svg+xml" data="<?= $url ?>" style="width: 100%; height: 100%;" >
      <pre><?= $tree ?></pre>
    </object>
  <? endif ?>
  </div>
</div>
