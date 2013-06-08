<?php defined("SYSPATH") or die("No direct script access.") ?>
<a href="<?= $url ?>" style="text-decoration: none">
<span style="position:absolute; top:<?= $top ?>px;
left:<?= $left ?>px; height: 48px; width: 48px;
background-image:url('<?= $images_url ?>/<?= $icon ?>.png');
opacity:0.<?= $trans ?>; filter:alpha(opacity=<?= $trans ?>);">
</span>
<? if (module::get_var("movie_overlay", "time")) : ?>
  <span class="g-movie-time" style="top:<?= $texttime_top ?>px;">&#9655;&nbsp;<?= $movie_time ?></span>
<? endif ?>
</a>
</div>
