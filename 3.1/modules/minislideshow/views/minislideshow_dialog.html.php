<?php defined("SYSPATH") or die("No direct script access.") ?>
<style>
input[type="text"] {
  width: 95%;
}
</style>
<h1 style="display: none;"><?= t("MiniSlide Show") ?></h1>
<div id="g-mini-slideshow">
<embed src="<?= module::get_var("minislideshow", "slideshow_url") ?>" width="485" height="300"
 align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer"
type="application/x-shockwave-flash" name="minislide" wmode="transparent"
 allowFullscreen="true" allowScriptAccess="always" quality="high"
flashvars="xmlUrl=<?= url::site("rss/feed/gallery/album/" . $item_id, "http") ?><?=$slideshow_params ?>"></embed>
<table><tr>
<td>Embed:</td>
<td><input type="text" onclick="this.focus(); this.select();" value="<embed src=&quot;<?= module::get_var("minislideshow", "slideshow_url") ?>&quot; width=&quot;485&quot; height=&quot;300&quot; align=&quot;middle&quot; pluginspage=&quot;http://www.macromedia.com/go/getflashplayer&quot; type=&quot;application/x-shockwave-flash&quot; name=&quot;minislide&quot; wmode=&quot;transparent&quot; allowFullscreen=&quot;true&quot; allowScriptAccess=&quot;always&quot; quality=&quot;high&quot; flashvars=&quot;xmlUrl=<?= url::site("rss/feed/gallery/album/" . $item_id, "http") ?><?=$slideshow_params ?>&quot;></embed>" readonly></td>
</tr></table>
</div>
