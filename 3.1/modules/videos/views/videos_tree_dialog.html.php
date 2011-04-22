<?php defined("SYSPATH") or die("No direct script access.") ?>
<? // rWatcher Edit:  This file used to be server_add_tree_dialog.html.php, server_add has been replaced with videos ?>
<script type="text/javascript">
  var GET_CHILDREN_URL = "<?= url::site("videos/children?path=__PATH__") ?>";
  var START_URL = "<?= url::site("videos/start?item_id={$item->id}&csrf=$csrf") ?>";
</script>

<div id="g-videos">
  <h1 style="display: none;"><?= t("Add Photos to '%title'", array("title" => html::purify($item->title))) ?></h1>

  <p id="g-description"><?= t("Photos will be added to album:") ?></p>
  <ul class="g-breadcrumbs">
    <? $i = 0 ?>
    <? foreach ($item->parents() as $parent): ?>
    <li<? if ($i == 0) print " class=\"g-first\"" ?>> <?= html::purify($parent->title) ?> </li>
    <? $i++ ?>
    <? endforeach ?>
    <li class="g-active"> <?= html::purify($item->title) ?> </li>
  </ul>

  <ul id="g-videos-tree" class="g-checkbox-tree">
    <?= $tree ?>
  </ul>

  <div id="g-videos-progress" style="display: none">
    <div class="g-progress-bar"></div>
    <div id="g-status"></div>
  </div>

  <span>
    <button id="g-videos-add-button" class="ui-state-default ui-state-disabled ui-corner-all"
            disabled="disabled">
      <?= t("Add") ?>
    </button>
    <button id="g-videos-pause-button" class="ui-state-default ui-corner-all" style="display:none">
      <?= t("Pause") ?>
    </button>
    <button id="g-videos-continue-button" class="ui-state-default ui-corner-all" style="display:none">
      <?= t("Continue") ?>
    </button>

    <button id="g-videos-close-button" class="ui-state-default ui-corner-all">
      <?= t("Close") ?>
    </button>
  </span>

  <script type="text/javascript">
    $("#g-videos").ready(function() {
      $("#g-videos").gallery_videos();
    });
  </script>

</div>
