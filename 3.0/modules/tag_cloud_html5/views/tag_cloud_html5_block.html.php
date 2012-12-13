<?php defined("SYSPATH") or die("No direct script access.") ?>
<!--[if lt IE 9]>
<?= html::script(gallery::find_file("js", "excanvas.compiled.js", false)) ?>
<![endif]-->
<script type="text/javascript">
  $(document).ready(function() {

    // set g-tag-cloud-html5-canvas size
    $("#g-tag-cloud-html5-canvas").attr({
      'width' : Math.floor($("#g-tag-cloud-html5").width()*<?= $width ?>),
      'height': Math.floor($("#g-tag-cloud-html5").width()*<?= $height ?>)
    });
    
    // start g-tag-cloud-html5-canvas
    if(!$('#g-tag-cloud-html5-canvas').tagcanvas(<?= $options ?>,'g-tag-cloud-html5-tags')) {
      // something went wrong, hide the canvas container g-tag-cloud-html5
      $('#g-tag-cloud-html5').hide();
    };
	
    // tag autocomplete for g-add-tag-form
    $("#g-add-tag-form input:text").autocomplete(
      "<?= url::site("/tags/autocomplete") ?>", {
        max: 30,
        multiple: true,
        multipleSeparator: ',',
        cacheLength: 1}
    );

  });
</script>
<div id="g-tag-cloud-html5">
  <canvas id="g-tag-cloud-html5-canvas">
    <? echo t('Tag cloud loading...'); ?>
  </canvas>
</div>
<div id="g-tag-cloud-html5-tags">
  <?= $cloud ?>
</div>
<?= $wholecloud_link ?>
<?= $form ?>
