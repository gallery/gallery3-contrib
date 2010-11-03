<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  // Base URL for flag pictures.
  $flag_type = module::get_var("language_flags", "flag_shape");
  $base_url = url::base(false, "http") . "modules/language_flags/images/" . $flag_type . "/";

  // Loop through each installed locale and display a flag.
  while ($one_locale = current($installed_locales)) {
    // Skip "default" so we don't end up with the same flag twice.
    if (key($installed_locales) != "") {

      // Use seperate div id's and img classes for the current language, the default language, and everything else.
      $div_id = "g-language-flag";
      $img_class = "g-flag";
      if (key($installed_locales) == $selected) {
        $div_id = "g-selected-language-flag";
        $img_class = "g-selected-flag";
      } elseif (key($installed_locales) == module::get_var("gallery", "default_locale")) {
        $div_id = "g-default-language-flag";
        $img_class = "g-default-flag";
      }

      // Figure out where the flag is / use the default if it doesn't exist.
      $flag_path = MODPATH . "language_flags/images/" . $flag_type . "/" . key($installed_locales) . ".png";
      $flag_url = $base_url . key($installed_locales) . ".png";
      if (!file_exists($flag_path)) {
        $flag_url = $base_url . "default.png";
      }

      // Print out the HTML for the flag.
      print "<div id=\"" . $div_id . "\">" . 
            "<a href=\"javascript:image_click('" . 
            key($installed_locales) . "')\"><img src=\"" . 
            $flag_url . "\" width=\"50\" title=\"" . $one_locale . 
            "\" alt=\"" . $one_locale . "\" border=\"0\" class=\"" . 
            $img_class . "\" /></a></div>";
    }
    next($installed_locales);
  }
?>
<script type="text/javascript">
function image_click(flag_code)
{
    var old_locale_preference = "<?= $selected ?>";
    var locale = flag_code;
    if (old_locale_preference == locale) {
      return;
    }

    var expires = -1;
    if (locale) {
      expires = 365;
    }
    $.cookie("g_locale", locale, {"expires": expires, "path": "/"});
    window.location.reload(true);
}
</script>
