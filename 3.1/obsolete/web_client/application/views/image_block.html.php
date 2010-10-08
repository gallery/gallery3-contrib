<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  $("#image_block").ready(function() {
    setTimeout("reloadRandomImage();", 10000); // Change the random image block
  });

  function reloadRandomImage() {
    $.get("/g3_client/index.php/g3_client/block/random", function(data, textStatus) {
      $("#image_block").html(data);
    });
  }
</script>
<h3>Random Image</h3>
<a href="<?= $path ?>">
  <img src="<?= $src ?>" alt="<?= $title ?>" />
</a>
<p><?= $title ?></p>