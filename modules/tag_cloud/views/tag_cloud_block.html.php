<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
function ajaxify_tag3d_form() {
  $("#gTag3D form").ajaxForm({
    dataType: "json",
    success: function(data) {
      if (data.result == "success") {
        $.get($("#gTagCloud3D").attr("title"), function(data, textStatus) {
          $("#gTagCloud3D").html(data);
          set_tag_cloud();
	});
      }
      $("#gTag3D form").resetForm();
    }
  });
}
  $("document").ready(function() {
    ajaxify_tag3d_form();
    set_tag_cloud();
  });
  $("#gAddTagForm").ready(function() {
    var url = $("#gTagCloud").attr("title") + "/autocomplete";
    $("#gAddTagForm input:text").autocomplete(
      url, {
        max: 30,
        multiple: true,
          multipleSeparator: ',',
          cacheLength: 1}
    );
  });

function set_tag_cloud() {
  var width = $("#gTagCloud3D").width();
  var tags = document.createElement("tags");
  $("#gTagCloud3D a").each(function(i) {
    var addr = $(this).clone();
    $(addr).attr("style", "font-size:" + $(this).css("fontSize")) + ";";
    $(tags).append(addr);
  });
  var object = document.createElement("object");
  $(object).attr({
    type: "application/x-shockwave-flash",
    data: "<?= url::file("modules/tag_cloud/lib/tagcloud.swf") ?>",
    width: width,
    height: .75 * width
  });
  $(object).append("<param name=\"movie\" value=\"<?= url::file("modules/tag_cloud/lib/tagcloud.swf") ?>\" />");
  $(object).append("<param name=\"bgcolor\" value=\"#ffffff\" />");
  $(object).append("<param name=\"allowScriptAccess\" value=\"always\" />");
  var value = 'tcolor=0x333333&tcolor2=0x009900&hicolor=0x000000&tspeed=100&distr=true&mode=tags&tagcloud=' + escape("<tags>" + $(tags).html() + "</tags>");
  $(object).append("<param name=\"flashvars\" value=\"" + value + "\" />");
  console.log($(object).html());
  $("#gTagCloud3D").html(object);
}

</script>
<div id="gTagCloud3D" title="<?= url::site("tags") ?>">
  <?= $cloud ?>
</div>
<?= $form ?>

