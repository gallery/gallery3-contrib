<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-social_share-google">
<!-- Place this tag where you want the +1 button to render -->
<div class="g-plusone""
 size="<?= module::get_var("social_share", "google_size") ?>"
 annotation="<?= module::get_var("social_share", "google_annotation") ?>">
</div>

<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
</div>