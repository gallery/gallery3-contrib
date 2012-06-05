<?php defined("SYSPATH") or die("No direct script access.") ?>
<!--[if lt IE 9]><?= $theme->script("excanvas.compiled.js"); ?><![endif]-->
<script type="text/javascript">
  function redraw() {

    // set g-tag-cloud-html5-page-canvas size
    $("#g-tag-cloud-html5-page-canvas").attr({
      'height': Math.min( $(window).height()*0.75, $("#g-tag-cloud-html5-page").width() ),
      'width':  Math.min( $(window).height()*0.95, $("#g-tag-cloud-html5-page").width() )
    });
    
    // start g-tag-cloud-html5-page-canvas
    if(!$('#g-tag-cloud-html5-page-canvas').tagcanvas(<?= $options ?>,'g-tag-cloud-html5-page-tags')) {
      // something went wrong, hide the canvas container g-tag-cloud-html5-page
      $('#g-tag-cloud-html5-page').hide();
    };
	
  };
  
  // resize and redraw the canvas
  $(window).resize(redraw);
  $(document).ready(redraw);
</script>
<div id="g-tag-cloud-html5-page-header">
  <div id="g-tag-cloud-html5-page-buttons">
    <?= $theme->dynamic_top() ?>
  </div>
  <h1><?= html::clean($title) ?></h1>
</div>
<div id="g-tag-cloud-html5-page">
  <canvas id="g-tag-cloud-html5-page-canvas">
    <? echo t('Tag cloud loading...'); ?>
  </canvas>
</div>
<div id="g-tag-cloud-html5-page-tags">
  <?= $cloud ?>
</div>
<?= $theme->dynamic_bottom() ?>
