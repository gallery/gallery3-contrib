<?php defined("SYSPATH") or die("No direct script access.") ?>
<a href="<?= $url ?>" <?= html::attributes($attrs) ?>>
  <object <?= html::attributes($object_attrs) ?>>
    <p><?= t("Your web browser doesn't have a PDF plugin.  Click here to download the PDF file instead.") ?></p>
  </object>
</a>
