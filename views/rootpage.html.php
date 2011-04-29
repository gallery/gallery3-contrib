<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2011 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<? 
  $link_url = item::root()->url();
  if ($theme->allow_root_page):
    $link_url .= "?root=no";
  endif;
  if ($theme->show_root_desc):
  	if ($theme->root_description):
  		$root_text = $theme->root_description;
  	elseif (isset($item)):
	    $root_text = $item->description;
  	endif;
	  if ($root_text): 
	    ?><div id="g-rootpage-quote"><?= $theme->bb2html($root_text, 1); ?></div><?
	  endif; 
	endif;
?>
<div id="g-rootpage-roll"<?= ($root_text)? null : ' class="g-full"'; ?>>
<object type="application/x-shockwave-flash" data="<?= url::file("modules/imageblockex/player/minislideshow.swf"); ?>" width="100%" height="100%">
<param name="movie" value="<?= url::file("modules/imageblockex/player/minislideshow.swf"); ?>" />
<param name="FlashVars" value="xmlUrl=<?= $theme->root_feed; ?>&amp;delay=<?= $theme->root_delay; ?>&amp;showControls=false&amp;altLink=<?= $link_url ?>&amp;showDropShadow=true&amp;useResizes=true&amp;useFull=true&amp;showLoader=false" />
<param name="bgcolor" value="#1c242e" />
<param name="wmode" value="transparent" />
<param name="menu" value="false" />
<param name="quality" value="high" />
</object>
<div id="g-rootpage-link" onclick="javascript:location='<?= $link_url ?>'" ></div>
<span><a href="<?= $link_url ?>"><?= t("Click to Enter") ?></a></span>
</div>
