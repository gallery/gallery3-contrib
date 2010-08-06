<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $(document).ready(function(){
    $(".g-admin-blocks-list").height($(".g-admin-blocks-list").height());
  });
  
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

<div id="g-block-admin" class="g-block ui-helper-clearfix">
  <h1> <?= t("Manage Module Order") ?> </h1>
  <p>
    <?= t("Select and drag blocks to change the order. Click 'Save' to save your changes.") ?>
  </p>
  <h2> <?= t("Important notes") ?> </h2>
  <p>
    <?= t("You will only need to change this order in rare circumstances (e.g. if two modules display content at the bottom of the image and you want to change the order in which this content is being shown). If everything on your Gallery Site is looking normal then please do not touch this.") ?>
  </p>
  <p>
    <?= t("The core module ('gallery') and the identity provider module (default is 'user') cannot be sorted and are therefore hidden from this list.") ?>
  </p>

  <div class="g-block-content">
    <div id="g-site-blocks">
      <div class="g-admin-blocks-list g-left">
        <h3><?= t("Installed Modules") ?></h3>
        <div>
          <ul id="g-active-blocks" class="g-sortable-blocks">
          <?= $available ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <form name="moduleorder" action="<?= url::site("admin/moduleorder/update?csrf={$csrf}") ?>" method="post">
    <input type="hidden" name="modulelist" value="">
  </form>
   <a class="ui-state-default ui-corner-all" style="padding: 5px;" href="javascript: buildmodulelist()">Save</a>
</div>
