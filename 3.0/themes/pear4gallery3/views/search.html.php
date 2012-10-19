<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
$(function() {
  $('#g-search').attr('value', '<?=$q?>');
  $('.pearTitle').html("Search results for \"<?=$q?>\"");
});
</script>
<? if (count($items)): ?>
  <?/* Treat dynamic pages just lite album pages. */ ?>
  <? $children = $items ?>
  <? $v = new View("album.html");
  $v->set_global("children", $items);// = $items;
  print $v;?>
<? else: ?>
  <p>
  <?= t("No results found for <b>%term</b>", array("term" => $q)) ?>
  </p>
<? endif; ?>
