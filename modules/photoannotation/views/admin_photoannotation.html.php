<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-photoannotation">
  <h2><?= t("Photo annotation administration") ?></h2>
  <h3><?= t("Notes:") ?></h3>
  <p><?= t("This module is partially compatible with the <a href=\"http://codex.gallery2.org/Gallery3:Modules:tagfaces\">TagFaces module</a> by rWatcher.<br />
    This means that notes and faces that you create in either one will be shown and are editable by the other module as well. If you added users to an annotation area though they will only be displayed with the Photo Annotation module.<br />
    You cannot have both active at the same time.<br /><br />
    If you decide to show annotations below the photo but they are displayed below the comments section (or any other data),
    please download and install the <a href=\"http://codex.gallery2.org/Gallery3:Modules:moduleorder\">Module order module</a>.") ?></p>
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
</script>
