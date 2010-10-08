<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-photoannotation">
  <h1><?= t("Photo annotation administration") ?></h1>
  <h3><?= t("Notes:") ?></h3>
  <p><?= t("This module is partially compatible with the <a href=\"%url\">TagFaces module</a> by rWatcher.<br />
    This means that notes and faces that you create in either one will be shown and are editable by the other module as well. If you added users to an annotation area though they will only be displayed with the Photo Annotation module.<br />
    You cannot have both active at the same time.", array("tagfaces" => "http://codex.gallery2.org/Gallery3:Modules:tagfaces")) ?>
    <br /><br /><?= t("<a href=\"%url\">Convert existing tag annotations to user annotations</a>", array("url" => url::site("admin/photoannotation/converter/"))) ?>
    <br /><?= t("<a href=\"%url\">Check for orphaned annotations</a>", array("url" => url::site("admin/photoannotation/tagsmaintanance/"))) ?></p>
    <?= $form ?>
</div>
<script type="text/javascript">
  $("input[name='bordercolor'], input[name='clickablehovercolor'], input[name='hovercolor']").ColorPicker({
    onSubmit: function(hsb, hex, rgb, el) {
      $(el).val(hex);
      $(el).ColorPickerHide();
    },
    onBeforeShow: function () {
      $(this).ColorPickerSetColor(this.value);
    }
  })
  .bind('keyup', function(){
    $(this).ColorPickerSetColor(this.value);
  });
  <? if (!module::is_active("comment")): ?>
  $(document).ready(function(){
    $("input[name='newcommentsubject'], input[name='updatedcommentsubject'], textarea[name='newcommentbody'], textarea[name='updatedcommentbody']").attr("disabled", true);
  });
  <? endif ?>
</script>
