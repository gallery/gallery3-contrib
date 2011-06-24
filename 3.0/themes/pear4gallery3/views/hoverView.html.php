<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="detailView" onmousemove="showHoverView();">
	<div class="overlay"> </div>
	<div class="content">
		<div class="imageContainer">
			<div id="backToAlbumLink" class="dark_theme">
				<button id="backToAlbumButton" class="large push large-with-push" title="Back to Album" onclick="hideDetailView();"> <div class="outer"> <div id="backToAlbumButtonLabel" class="label">Back to Album</div> </div> </button>
			</div>
			<div id="detailImageView" class=""> <img style="visibility: visible;" src="" alt="" id="img_detail"/> </div>
			<div class="titleLabel" id="imageTitleLabel" style="bottom: width: 624px;"></div>
		</div>
		<div id="hoverView" class="" onmouseover="hovering=true;" onmouseout="hovering=false;">
			<div id="hoverViewMenu">
				<div id="download_detail" title="Download this photo" class="download_detail" onclick="document.location=slideshowImages[currentImg][5]"> </div>
				<div id="prev_detail" title="(P)revious" class="prev_detail"> </div>
				<div id="pause_detail" style="display: none;" title="Pause" class="pause_detail" onclick="togglePlayPause();"> </div>
				<div id="play_detail" style="display: none;" title="Play" class="play_detail" onclick="togglePlayPause();"> </div>
				<div id="next_detail" title="(N)ext" class="next_detail"> </div>
				<div id="info_detail" title="Show more information about this photo" class="info_detail g-dialog-link"> </div>
			</div>
		</div>
	</div>
</div>
