<?php defined("SYSPATH") or die("No direct script access.");
if ($theme->item()) {
    $url =  urlencode(url::abs_current(true));
    $item = $theme->item();
    $media = urlencode($item->thumb_url(true));
    $description=urlencode($item->description);
?>
<div class="g-social_share-pinterest_pinit">	
    <a href="http://pinterest.com/pin/create/button/?url=<?= $url; ?>&media=<?= $media; ?>&description=<?= $description; ?>" class="pin-it-button" count-layout="<?= module::get_var("social_share", "pinterest_count_location") ?>">
        <img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />
    </a>
    <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
</div>
<?php } ?>