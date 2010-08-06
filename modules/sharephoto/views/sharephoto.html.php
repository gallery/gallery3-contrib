<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-sharephoto">	
<? if (module::get_var("sharephoto", "Icons")) { ?>
	<div id="dock">
		<div class="dock-container">		
			<div class="dock-contaner-left"></div>
			<div class="addthis_toolbox">   
				<div class="custom_images">
					<a class="addthis_button_facebook"><span>Facebook</span>
					<img src="<?= url::file("modules/sharephoto/images/facebook.png") ?>" /></a>
					
					<a class="addthis_button_twitter"><span>Twitter</span>
					<img src="<?= url::file("modules/sharephoto/images/twitter.png") ?>" /></a>
					
					<a class="addthis_button_myspace"><span>MySpace</span>
					<img src="<?= url::file("modules/sharephoto/images/myspace.png") ?>" /></a>
					
					<a class="addthis_button_stumbleupon"><span>Stumble</span>
					<img src="<?= url::file("modules/sharephoto/images/stumbleupon.png") ?>" /></a>
					
					<a class="addthis_button_delicious"><span>Delicious</span>
					<img src="<?= url::file("modules/sharephoto/images/delicious.png") ?>" /></a>
					
					<a class="addthis_button_more"><span><?= t("More...") ?></span>
					<img src="<?= url::file("modules/sharephoto/images/addthis_64.png") ?>" /></a>
				</div>
			</div>        
		</div>
	</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
<script type="text/javascript" src="<?= url::file("modules/sharephoto/js/fisheye-iutil.min.js") ?>"></script>
<? } ?>

<? if (module::get_var("sharephoto", "HTMLLinks")) { ?> 
	<div id="g-sharephoto-links">
		<li id="g-sharephoto-links-position">
		  <div style="cursor:pointer;" class="show-link"><?= t("URL") ?></div>
		  <div class="show-div">
			  <div>
				  <span id="g-sharephoto-links-small"><?= t("Copy link:") ?></span>
				  <input type="text" value="<?= url::abs_site("{$item->type}s/{$item->id}") ?>" onclick="this.focus(); this.select();" readonly>
			  </div>
		  </div>
		</li>
		<li id="g-sharephoto-links-position">
		  <div style="cursor:pointer;" class="show-link"><?= t("Text Link") ?></div>
		  <div class="show-div">
			  <div>
				<span id="g-sharephoto-links-small"><?= t("Copy Code:") ?></span>
				<input type="text" value='<a href="<?= url::abs_site("{$item->type}s/{$item->id}") ?>"><?= html::purify($item->title) ?></a>' onclick="this.focus(); this.select();" readonly>
				<br>
				<span id="g-sharephoto-links-small"><?= t("Preview:") ?></span><br>
				<a href="<?= url::abs_site("{$item->type}s/{$item->id}") ?>"><?= html::purify($item->title) ?></a>
			  </div>
		  </div>
		</li>
		<li id="g-sharephoto-links-position">
		  <div style="cursor:pointer;" class="show-link"><?= t("Thumbnail Link") ?></div>
		  <div class="show-div">
			  <div>
				<span id="g-sharephoto-links-small"><?= t("Copy Code:") ?></span>
				 <input type="text" value='<a href="<?= url::abs_site("{$item->type}s/{$item->id}") ?>"><img src="<?= $item->thumb_url(true) ?>"></a>' onclick="this.focus(); this.select();" readonly></th></b>
				<br>
				<span id="g-sharephoto-links-small"><?= t("Preview:") ?></span><br>
				<a href="<?= url::abs_site("{$item->type}s/{$item->id}") ?>"><img src="<?= $item->thumb_url(true) ?>"></a><br>
			  </div>
		  </div>
		</li>
	</div>
<? } ?>
</div>
