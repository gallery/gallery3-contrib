<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-movie-tools-admin" class="g-block ui-helper-clearfix">
  <h1> <?= t("Movie tools settings") ?> </h1>
  <p>
    <?= t("Movie tools allows you to add support for additional movie formats and customize when movie thumbnails are extracted.") ?>
  </p>
  <p>
    <?= t("A table of common movie formats is given below, all of which can be enabled as desired.") ?>
    <?= t("In addition, you can specify additional movie formats not on the table.") ?>
    <?= t("If Gallery is unable to extract a thumbnail from a new movie type, it will use a placeholder.") ?>
    <?= t("If Gallery is unable to play a movie, a download link will be provided to the user instead.") ?>
  </p>
  <p>
    <b><?= t("Technical details:")?></b>
    <?= t("If the movie is shorter than the time specified, the thumbnail will be taken from the start of the movie.") ?>
    <?= t("Also, if you want to disable a previously-supported movie type, it's recommended to first delete any movies if that type.") ?>
    <?= t("Otherwise, Gallery could act strangely with the now-unsupported movies.") ?>
  </p>
  <p>
    <table>
      <tr>
        <td><b>Category</b></td>
        <td><b>Description</b></td>
        <td><b>Formats</b></td>
      </tr>
      <? foreach ($formats as $id => $data): ?>
      <tr>
        <td><?= $data["name"] ?></td>
        <td><?= $data["desc"] ?></td>
        <td><?= movie_tools::formats_array_to_string($data["types"]) ?></td>
      </tr>
      <? endforeach; ?>
    </table>
  </p>

  <?= $form ?>
</div>
