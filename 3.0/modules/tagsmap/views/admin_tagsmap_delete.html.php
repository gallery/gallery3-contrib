<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-tags-map-delete-admin">
  <h2> <?= t("Delete GPS Data For Tag ") . $tag_name . "?" ?> </h2>
<?= t("Are you sure you wish to delete all GPS data associated with this tag?") ?> <br/><br/>
<a href="<?= url::site("admin/tagsmap/delete_gps/" . $tag_id) ?>">Delete</a> 
<a href="<?= url::site("admin/tagsmap") ?>">Cancel</a>
  <?= $tagsmapdelete_form ?>
</div>
