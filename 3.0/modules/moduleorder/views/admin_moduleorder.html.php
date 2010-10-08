<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if (module::get_version("gallery") > 31): ?>
<script type="text/javascript">
  
  $(function() {
    $(".g-admin-blocks-list ul").sortable({
      connectWith: ".g-sortable-blocks",
      opacity: .7,
      placeholder: "g-target",
      update: function(event,ui) {
      }
    }).disableSelection();
  });

  function buildmodulelist () {
    var active_modules = "";
    $("ul#g-active-blocks li").each(function(i) {
      active_modules += "&modules["+i+"]="+$(this).attr("ref");
    });
    document.moduleorder.modulelist.value = active_modules;
    document.moduleorder.submit();
  }
</script>
<? endif ?>

<div id="g-block-admin" class="g-block ui-helper-clearfix">
  <h1> <?= t("Manage module order") ?> </h1>
  <? if (module::get_version("gallery") < 32): ?>
  <h2> <?= t("Warning") ?> </h2>
  <p>
    <?= t("You must have Gallery core version of 32 or higher installed to use this module. Please update your installation.") ?>
  </p>
  <? else: ?>
  <p>
    <?= t("Select and drag blocks to change the order. Click 'Save' to save your changes.") ?>
  </p>
  <h2> <?= t("Notes") ?> </h2>
  <p>
    <?= t("The core module ('gallery') and the identity provider module (default is 'user') cannot be sorted and are therefore hidden from this list.") ?>
  </p>

  <div class="g-block-content">
    <div id="g-site-blocks">
      <div class="g-admin-blocks-list g-left" style="float: none !important;">
        <h3><?= t("Installed Modules") ?></h3>
        <div>
          <ul id="g-active-blocks" class="g-sortable-blocks">
          <?= $available ?>
          </ul>
        </div>
      </div>
    </div>
    <form name="moduleorder" action="<?= url::site("admin/moduleorder/update?csrf={$csrf}") ?>" method="post">
      <input type="hidden" name="modulelist" value="">
    </form>
  </div>
  <a class="ui-state-default ui-corner-all" style="padding: 5px;" href="javascript: buildmodulelist()">Save</a>
  <? endif ?>
</div>
