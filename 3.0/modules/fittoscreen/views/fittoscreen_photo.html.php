<?php defined("SYSPATH") or die("No direct script access.") ?>

<script type="text/javascript">

function dynresize() {
 var $maxheight = <?= $item->resize_height ?>;
 var $maxwidth = <?= $item->resize_width ?>;

 var $ratio = $maxwidth/$maxheight;

 var $winwidth = $(window).width() <?= (module::get_var("fittoscreen", "width_unit")=="pr" ? "*".number_format(module::get_var("fittoscreen", "width")/100,2,".","") : "-".module::get_var("fittoscreen", "width")) ?>;
 var $winheight = $(window).height() <?= (module::get_var("fittoscreen", "height_unit")=="pr" ? "*".number_format(module::get_var("fittoscreen", "height")/100,2,".","") : "-".module::get_var("fittoscreen", "height")) ?>;

 if (($winwidth/$winheight)<$ratio) {
    $finalwidth = ($winwidth > $maxwidth ? $maxwidth : $winwidth);
    $finalheight = $finalwidth / $ratio;
 }
else {
    $finalheight = ($winheight > $maxheight ? $maxheight : $winheight);
    $finalwidth = $finalheight * $ratio;
 }


// $('body').prepend('<div>' + $finalheight + " " + $finalwidth + '</div>');

 $("img.g-resize").attr({
                                height: $finalheight,
                                width: $finalwidth,
                        });

}

$(window).resize(dynresize);
$(document).ready(dynresize);
</script>
