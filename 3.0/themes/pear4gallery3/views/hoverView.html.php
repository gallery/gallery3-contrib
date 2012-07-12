<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="detailView" onmousemove="showHoverView();">
	<div class="overlay"> </div>
	<div class="content">
		<div class="imageContainer">
			<div class="titleLabel" id="imageTitleLabel" style="bottom: width: 624px;"></div>
			<div id="detailImageView" class=""> <img style="visibility: visible;" src="" alt="" id="img_detail"/> </div>
		</div>
    <div id="hoverView" onmouseover="pear.hovering=true;" onmouseout="pear.hovering=false;">
    <div id="hoverViewMenu">
      <div id="prev" title="(P)revious" class="controller"></div>
      <div id="pause_detail" title="Pause" class="controller" onclick="togglePlayPause();"> </div>
      <div id="play_detail" title="Play" class="controller" onclick="togglePlayPause();"> </div>
      <div id="next" title="(N)ext" class="controller"></div>
    </div></div>
    <div class="hoverViewTopMenu">
        <div id="download" title="Download this photo" class="controller half" onclick="window.open(pear.sitePath + 'pear/download/' + slideshowImages[pear.currentImg][1])"> </div>
        <div id="info" title="Show more information about this photo" class="controller half info_detail g-dialog-link"> </div>
        <? if(module::is_active("comment")): ?><div id="comment" title="Comments" class="controller half comments_detail g-dialog-link" onclick=""></div><?endif ?>
        <div id="close" title="Close" class="controller half" onclick="hideDetailView();"></div>
    </div>
	</div>
</div>
