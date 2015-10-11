<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
 var load_tree = function(target_id, locked) {
   var load_url = "<?= url::site("browse/show_sub_tree/{$source->id}/__TARGETID__") ?>";
   var node = $("#node_" + target_id);
   $("#g-move .node a").removeClass("selected");
   node.find("a:first").addClass("selected");
   if (locked) {
     $("#g-move-button").attr("disabled", "disabled");
     $("#g-move form input[name=target_id]").attr("value", "");
   } else {
     $("#g-move-button").removeAttr("disabled");
     $("#g-move form input[name=target_id]").attr("value", target_id);
   }
   var sub_tree = $("#tree_" + target_id);
   if (sub_tree.length) {
     sub_tree.toggle();
   } else {
     $.get(load_url.replace("__TARGETID__", target_id), {},
           function(data) {
             node.html(data);
             node.find("a:first").addClass("selected");
           });
   }
 }
</script>
<h1 style="display: none">
  <? if ($source->type == "photo"): ?>
  <? t("Set this photo as an album cover") ?>
  <? elseif ($source->type == "movie"): ?>
  <? t("Set this movie as an album cover") ?>
  <? elseif ($source->type == "album"): ?>
  <? t("Set this album as an album cover") ?>
  <? endif ?>
</h1>
<div id="g-move">
  <ul id="tree_0">
    <li id="node_1" class="node">
      <?= $tree ?>
    </li>
  </ul>
  <form method="post" action="<?= url::site("browse/save/$source->id") ?>">
    <?= access::csrf_form_field() ?>
    <input type="hidden" name="target_id" value="" />
    <input type="submit" id="g-move-button" value="<?= t("Assign Cover")->for_html_attr() ?>" disabled="disabled"/>
  </form>
</div>
