<?php defined("SYSPATH") or die("No direct script access.") ?>

<div id="g-admin-code-block">
    <h2><?= t("Amazon S3") ?></h2>

    <p><?php echo t("Amazon S3 is a lightning fast Content Delivery Network. It's used for high-traffic sites to offload the bandwidth and processing required to vend vast quantities of data (pictures, videos, etc) to the cloud, leaving the local server only the tasks of running Gallery and vending small HTML pages."); ?></p>

    <div class="g-block-content">
        <?php echo $form; ?>
    </div>
</div>

<?php echo $end; ?>
