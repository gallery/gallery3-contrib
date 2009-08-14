<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  // Check and see if the current photo has any faces associated with it.
  $existingFaces = ORM::factory("items_face")
                        ->where("item_id", $item->id)
                        ->find_all();

  // If it does, then insert some javascript and display an image map
  //   to show where the faces are at.
  if (count($existingFaces) > 0) {
?>

<div class="square" title="Test" id="divfacebox" style="display:none;left:5;top:5;width:50;height:50;"></div>

<script type="text/JavaScript">
  function setfacemap() {
	// Insert the usemap element into the resize photo's image tag.
    var photoimg = document.getElementById('gPhotoId-<?=$item->id ?>');
    photoimg.useMap = '#faces';
  }

function highlightbox(x1, y1, x2, y2, str_title) {
	// Put a div over a face when the mouse moves over it.
	// Doesn't actually work :(
    var photoimg = document.getElementById('gPhotoId-<?=$item->id ?>');
    var facediv = document.getElementById('divfacebox');
    facediv.title = str_title;
    facediv.style.display = 'block';
    facediv.style.left = photoimg.offsetLeft + x1;
    facediv.style.top = photoimg.offsetTop + y1;
    facediv.style.width=x2-x1;
    facediv.style.height=y2-y1;
}

function hidebox() {
  // Hide the div when the mouse moves off of the face.
  document.getElementById('divfacebox').style.display = 'none';
}

  // Call setfacemap when the page loads.
  window.onload = setfacemap();
</script>

<map name="faces">
<?
    // For each face, add a rectangle area to the page.
    foreach ($existingFaces as $oneFace) {
      $oneTag = ORM::factory("tag", $oneFace->tag_id)
?>
  <area shape="rect" coords="<?=$oneFace->x1 ?>,<?=$oneFace->y1 ?>,<?=$oneFace->x2 ?>,<?=$oneFace->y2 ?>" href="<?=url::site("tags/$oneFace->tag_id") ?>" title="<?=p::clean($oneTag->name); ?>" alt="<?=$oneTag->name; ?>" onMouseOver="highlightbox(<?=$oneFace->x1 ?>,<?=$oneFace->y1 ?>,<?=$oneFace->x2 ?>,<?=$oneFace->y2 ?>,'<?=p::clean($oneTag->name); ?>')" onMouseOut="hidebox()" />
<? } ?>
</map>
<?
  }
?>
