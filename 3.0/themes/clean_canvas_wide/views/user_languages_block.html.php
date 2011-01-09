<?php defined("SYSPATH") or die("No direct script access.") ?>

<script type="text/javascript">
function ChangeLocale( locale ) {
    var old_locale_preference = <?= html::js_string($selected) ?>;
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

<? $i = 0 ?>
<? foreach ($installed_locales as $locale => $value): ?>
  <? if ($i>0) : ?>  <? if ($i>1) : ?> | <? endif ?> <a href="javascript:ChangeLocale( '<?= $locale ?>' )"> <?= html::purify($value) ?> </a> <? endif ?>   <? $i++ ?>
<? endforeach ?>

