<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  // Base URL for flag pictures.
  $base_url = url::base(false, "http") . "modules/language_flags/images/" . module::get_var("language_flags", "flag_shape") . "/";
  
  // Loop through each installed locale and display a flag.
  while ($one_locale = current($installed_locales)) {
    // Skip "default" so we don't end up with the same flag twice.
    if (key($installed_locales) != "") {

      // Use seperate div id's for the current language, the default language, and everything else.
      $div_id = "g-language_flag";
      if (key($installed_locales) == $selected) {
        $div_id = "g-selected-language-flag";
      } elseif (key($installed_locales) == module::get_var("gallery", "default_locale")) {
        $div_id = "g-default-language-flag";
      }

      // Print out the HTML for the flag.
      print "<div id=\"" . $div_id . "\">" . 
            "<a href=\"javascript:image_click('" . 
            key($installed_locales) . "')\"><img src=\"" . 
            $base_url . key($installed_locales) . ".png" . 
            "\" width=\"50\" title=\"" . $one_locale . 
            "\" alt=\"" . $one_locale . "\" border=\"0\"></a></div>";
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
