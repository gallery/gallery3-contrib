<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-admin-remote" class="g-block">
  <h1> <?= t("Gallery Remote Protocol 2") ?> </h1>
  <p>
    <?= t("Use your Gallery 1 & 2 tools like GalleryRemote, etc. with your new Gallery 3 installation.") ?>
  </p>

  <script type="text/javascript">
    $(document).ready(function() {
    	$("#g-admin-remote-tabs").tabs();
      // Show the tabs after the page has loaded to prevent Firefox from rendering the
      // unstyled page and then flashing.
      $("#g-admin-remote-tabs").show();
    });
  </script>
  <div id="g-admin-remote-tabs" class="g-block-content" style="display: none">
    <ul>
      <li>
        <a href="#g-admin-remote-notes"><?= t("Notes") ?></a>
      </li>
      <li>
        <form action="https://www.paypal.com/cgi-bin/webscr" target="_blank" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
          <input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." style="background-color: transparent">
          <img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/de_DE/i/scr/pixel.gif" width="1" height="1">
				</form>
      </li>
    </ul>
    <div id="g-admin-remote-notes" class="g-text">
      <ul>
      	<li>
      		<p>
            <?= t("To use GalleryRemote 1.5.1 you need to edit %file and change line <br />%from <br />to line <br />%to",
                  array( "file" => 'modules/gallery/config/cookie.php'
                  			,"from" => '$config[\'httponly\'] = true;'
                  			,"to" => '$config[\'httponly\'] = false;') ) ?>
      		</p>
      	</li>      	
        <li>
          <p>
            <?= t("In any case you need to enable the G3 Gallery Remote interface: <br />Put the following block at the top of %path and the Gallery Remote module will be available",
                  array("path" => '.htaccess') ) ?>
          </p>

          <textarea id="g-remote-redirect-rules" rows="4" cols="60">&lt;IfModule mod_rewrite.c&gt;
      Options +FollowSymLinks
      RewriteEngine On
      RewriteBase /gallery/
      ErrorDocument 404 default
      RewriteRule ^main.php(.*)$ / [QSA,L,R=404]
      RewriteRule ^gallery_remote2.php(.*)$ index.php?kohana_uri=/gallery_remote$1 [QSA,PT,L]
&lt;/IfModule&gt;</textarea>
          <script type="text/javascript">
            $(document).ready(function() {
              $("#g-remote-redirect-rules").click(function(event) {
                this.select();
              });
            });
          </script>
        </li>
      </ul>
    </div>
  </div>
</div>
