<?php defined("SYSPATH") or die("No direct script access.") ?>

<script type="text/javascript" src="<?= url::file("modules/ratings/js/jscolor/jscolor.js"); ?>"></script>

<div id="g-ratings-admin">
  <h3>Ratings Settings</h3>
  <table style="border:1px solid black; width:300px; padding:10px; background-color:white;" >
    <tr>
      <td>Icon Set:</td>
      <td align=center><img height=35 src="<?= url::file("modules/ratings/vendor/img/hearts.jpg"); ?>" /></td>
      <td align=center><img height=35 src="<?= url::file("modules/ratings/vendor/img/filmstrip.jpg"); ?>" /></td>
      <td align=center><img height=35 src="<?= url::file("modules/ratings/vendor/img/stars.jpg"); ?>" /></td>
      <td align=center><img height=35 src="<?= url::file("modules/ratings/vendor/img/camera.jpg"); ?>" /></td>
    </tr>
    <tr>
      <td>Number:</td>
      <td align=center>1</td>
      <td align=center>2</td>
      <td align=center>3</td>
      <td align=center>4</td>
    </tr>
  </table>
  <?= $ratings_form ?>
</div>

<script>
$('input.g-unique').click(function() {
    $('input.g-unique:checked').not(this).removeAttr('checked');
});
</script>


