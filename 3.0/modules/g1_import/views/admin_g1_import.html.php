<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= $theme->css("jquery.autocomplete.css") ?>
<?= $theme->script("jquery.autocomplete.js") ?>
<script type="text/javascript">
$("document").ready(function() {
  $("form input[name=albums_path]").autocomplete(
    "<?= url::site("__ARGS__") ?>".replace("__ARGS__", "admin/g1_import/autocomplete"),
    {
      max: 256,
      loadingClass: "g-loading-small",
    });
});
</script>

<div id="g-admin-g1-import" class="g-block">
  <h1> <?= t("Gallery 1 import") ?> </h1>
  <p>
    <?= t("Import your Gallery 1 photos, movies and comments into your new Gallery 3 installation.") ?>
  </p>

  <script type="text/javascript">
    $(document).ready(function() {
      $("#g-admin-g1-import-tabs").tabs()
      <? if (!isset($g1_version)): ?>
      .tabs("disable", 1)
      .tabs("disable", 2)
      <? elseif ($g3_resource_count > .9 * $g1_resource_count):  ?>
      .tabs("select", 2)
      <? else: ?>
      .tabs("select", 1)
      <? endif ?>
      ;

      // Show the tabs after the page has loaded to prevent Firefox from rendering the
      // unstyled page and then flashing.
      $("#g-admin-g1-import-tabs").show();
    });
  </script>
  <div id="g-admin-g1-import-tabs" class="g-block-content" style="display: none">
    <ul>
      <li>
        <a href="#g-admin-g1-import-configure"><?= t("1. Configure Gallery 1 path") ?></a>
      </li>
      <li>
        <a href="#g-admin-g1-import-import"><?= t("2. Import!") ?></a>
      </li>
      <li>
        <a href="#g-admin-g1-import-notes"><?= t("3. After your import") ?></a>
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
    <div id="g-admin-g1-import-configure" class="g-block-content">
      <?= $form ?>
    </div>
    <div id="g-admin-g1-import-import">
      <? if (isset($g1_version)): ?>
      <ul>
        <li>
          <?= t("Gallery version %version detected", array("version" => $g1_version)) ?>
        </li>
        <? if ($g1_sizes["thumb"]["size"] && $thumb_size != $g1_sizes["thumb"]["size"]): ?>
        <li>
          <?= t("Your most common thumbnail size in Gallery 1 is %g1_pixels pixels, but your Gallery 3 thumbnail size is set to %g3_pixels pixels. <a href=\"%url\">Using the same value</a> will speed up your import.",
                array("g1_pixels" => $g1_sizes["thumb"]["size"],
                      "g3_pixels" => $thumb_size,
                      "url" => html::mark_clean(url::site("admin/theme_options")))) ?>
        </li>
        <? endif ?>

        <? if ($g1_sizes["resize"]["size"] && $resize_size != $g1_sizes["resize"]["size"]): ?>
        <li>
          <?= t("Your most common intermediate size in Gallery 1 is %g1_pixels pixels, but your Gallery 3 intermediate size is set to %g3_pixels pixels. <a href=\"%url\">Using the same value</a> will speed up your import.",
                array("g1_pixels" => $g1_sizes["resize"]["size"],
                      "g3_pixels" => $resize_size,
                      "url" => html::mark_clean(url::site("admin/theme_options")))) ?>
        </li>
        <? endif ?>

        <li>
          <?
          $t = array();
          $t[] = t2("1 user", "%count users", $g1_stats["users"]);
          $t[] = t2("1 group", "%count groups", $g1_stats["groups"]);
          $t[] = t2("1 album", "%count albums", $g1_stats["albums"]);
          $t[] = t2("1 photo", "%count photos/movies", $g1_stats["photos"] + $g1_stats["movies"]);
          $t[] = t2("1 comment", "%count comments", $g1_stats["comments"]);
          $t[] = t2("1 tagged photo/movie/album", "%count tagged photos/movies/albums",
                    $g1_stats["tags"]);
          ?>
          <?= t("Your Gallery 1 has the following importable data in it: %t0, %t1, %t2, %t3, %t4, %t5",
                array("t0" => $t[0], "t1" => $t[1], "t2" => $t[2],
                      "t3" => $t[3], "t4" => $t[4], "t5" => $t[5])) ?>
        </li>

        <? if ($g3_resource_count): ?>
        <li>
          <?
          $t = array();
          $t[] = t2("1 user", "%count users", $g3_stats["user"]);
          $t[] = t2("1 group", "%count groups", $g3_stats["group"]);
          $t[] = t2("1 album", "%count albums", $g3_stats["album"]);
          $t[] = t2("1 photo/movie", "%count photos/movies", $g3_stats["item"]);
          $t[] = t2("1 comment", "%count comments", $g3_stats["comment"]);
          $t[] = t2("1 tagged photo/movie/album", "%count tagged photos/movies/albums", $g3_stats["tag"]);
          ?>
          <?= t("It looks like you've imported the following Gallery 1 data already: %t0, %t1, %t2, %t3, %t4, %t5",
                array("t0" => $t[0], "t1" => $t[1], "t2" => $t[2],
                      "t3" => $t[3], "t4" => $t[4], "t5" => $t[5])) ?>
        </li>
        <? endif ?>
      </ul>
      <p>
        <a class="g-button g-dialog-link ui-state-default ui-corner-all"
           href="<?= url::site("admin/maintenance/start/g1_import_task::import?csrf=$csrf") ?>">
          <?= t("Begin import!") ?>
        </a>
      </p>
      <? endif ?>
    </div>
    <div id="g-admin-g1-import-notes" class="g-text">
      <ul>
        <li>
          <?= t("Gallery 3 does not support per-user / per-item permissions.  <b>Review permissions!</b>") ?>
        </li>
        <li>
          <?= t("The only supported file formats are JPG, PNG and GIF, FLV and MP4.  Other formats will be skipped.") ?>
        </li>
        <li>
          <p>
            <?= t("Redirecting G1 Gallery URLs once your migration is complete. Put this block at the top of %path and all gallery urls will be redirected to Gallery 3",
                  array("path" => g1_import::$gallery_dir.DIRECTORY_SEPARATOR.'.htaccess') ) ?>
          </p>

          <textarea id="g-g1-gallery-redirect-rules" rows="4" cols="60">&lt;IfModule mod_rewrite.c&gt;
      Options +FollowSymLinks
      RewriteEngine On
<? /*      RewriteBase <?= html::clean(g1_import::$gallery_url) ?> */ ?>
      RewriteRule ^(.*)$ <?= url::site("g1/map?path=\$1") ?>   [QSA,L,R=301]
    &lt;/IfModule&gt;</textarea>
          <script type="text/javascript">
            $(document).ready(function() {
              $("#g-g1-gallery-redirect-rules").click(function(event) {
                this.select();
              });
            });
          </script>
        </li>
        <li>
          <p>
            <?= t("Redirecting G1 Album URLs once your migration is complete.  Put this block at the top of %path and all album image urls will be redirected to Gallery 3",
                  array("path" => g1_import::$album_dir.DIRECTORY_SEPARATOR.'.htaccess') ) ?>
          </p>

          <textarea id="g-g1-album-redirect-rules" rows="4" cols="60">&lt;IfModule mod_rewrite.c&gt;
      Options +FollowSymLinks
      RewriteEngine On
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      
      RewriteRule ^(.*)$ <?= url::site("g1/map?path=\$1") ?>   [QSA,L,R=301]
    &lt;/IfModule&gt;</textarea>
          <script type="text/javascript">
            $(document).ready(function() {
              $("#g-g1-album-redirect-rules").click(function(event) {
                this.select();
              });
            });
          </script>
        </li>
      </ul>
    </div>
  </div>
</div>
