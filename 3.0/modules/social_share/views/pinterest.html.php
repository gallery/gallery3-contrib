<?php defined("SYSPATH") or die("No direct script access.");

$url = url::abs_current(true);
$description="need to find out how to get this";
if ($theme->item()) {
    $item = $theme->item();
    $media = $item->thumb_url(true);
?>
<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
<a href="http://pinterest.com/pin/create/button/?url=<?= $url; ?>&media=<?= $media; ?>&description=<?= $description; ?>" class="pin-it-button" count-layout="<?= module::get_var("social_share", "pinterest_count_location") ?>">
    <img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />
</a>
<?php } ?>