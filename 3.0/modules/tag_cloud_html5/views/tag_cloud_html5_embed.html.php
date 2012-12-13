<?php defined("SYSPATH") or die("No direct script access.") ?>
<!--[if lt IE 9]>
<?= html::script(gallery::find_file("js", "excanvas.compiled.js", false)) ?>
<![endif]--> 
<script type="text/javascript">
  // define flag variables if not already defined elsewhere
  if (typeof(jQueryScriptFlag) == 'undefined') {
    var jQueryScriptFlag = false;
  };
  if (typeof(jQueryTagCanvasScriptFlag) == 'undefined') {
    var jQueryTagCanvasScriptFlag = false;
  };
  function initScripts() {
    // load scripts if not already loaded
    if (typeof(jQuery) == 'undefined') {
      if (!jQueryScriptFlag) {
        // load both scripts
        jQueryScriptFlag = true;
        jQueryTagCanvasScriptFlag = true;
        document.write("<scr" + "ipt type=\"text/javascript\" src=\"<?= url::base(false).gallery::find_file("js", "jquery.js", false) ?>\"></scr" + "ipt>");
        document.write("<scr" + "ipt type=\"text/javascript\" src=\"<?= url::base(false).gallery::find_file("js", "jquery.tagcanvas.mod.min.js", false) ?>\"></scr" + "ipt>");
      };
      setTimeout("initScripts()", 50);
    } else if (typeof(jQuery().tagcanvas) == 'undefined') {
      if (!jQueryTagCanvasScriptFlag) {
        // load one script
        jQueryTagCanvasScriptFlag = true;
        document.write("<scr" + "ipt type=\"text/javascript\" src=\"<?= url::base(false).gallery::find_file("js", "jquery.tagcanvas.mod.min.js", false) ?>\"></scr" + "ipt>");
      };
      setTimeout("initScripts()", 50);
    } else {
      // libraries loaded - run actual code
      function redraw() {
        // set g-tag-cloud-html5-embed-canvas size
        $("#g-tag-cloud-html5-embed-canvas").attr({
          'width' : $("#g-tag-cloud-html5-embed").parent().width(),
          'height': $("#g-tag-cloud-html5-embed").parent().height()
        });
        // start g-tag-cloud-html5-embed-canvas
        if(!$('#g-tag-cloud-html5-embed-canvas').tagcanvas(<?= $options ?>,'g-tag-cloud-html5-embed-tags')) {
          // something went wrong, hide the canvas container g-tag-cloud-html5-embed
          $('#g-tag-cloud-html5-embed').hide();
        };
      };
      // resize and redraw the canvas
      $(document).ready(redraw);
      $(window).resize(redraw);
    };
  };
  initScripts();
</script>
<div id="g-tag-cloud-html5-embed">
  <canvas id="g-tag-cloud-html5-embed-canvas">
    <? echo t('Tag cloud loading...'); ?>
  </canvas>
</div>
<div id="g-tag-cloud-html5-embed-tags" style="display: none">
  <?= $cloud ?>
</div>