<?php defined("SYSPATH") or die("No direct script access.") ?>	

<div id="g-admin-code-block">
    <h2><?= t("Strip EXIF/IPTC Tags Settings") ?></h2>

    <p><?= t("Strip EXIF and/or IPTC metadata in uploaded images.  By default, strip out location data."); ?></p>

    <div class="g-block-content">
        <?php echo $form; ?>
    </div>
</div>
