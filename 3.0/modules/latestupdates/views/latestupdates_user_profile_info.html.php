<?php defined("SYSPATH") or die("No direct script access.") ?>
<script> 
  $(function() {
    $( "#tabs" ).tabs();
  });
</script> 
<br />
<div id="tabs" style="height: 200px"> 
  <ul>
    <li><a class="g-menu-link" href="<?= url::site("latestupdates/user_profiles/popular/{$user_id}") ?>" title="<?= t("Most Viewed") ?>"><?= t("Most Viewed") ?></a></li>
    <li><a class="g-menu-link" href="<?= url::site("latestupdates/user_profiles/recent/{$user_id}") ?>" title="<?= t("Recent Uploads") ?>"><?= t("Recent Uploads") ?></a></li>
    <li><a class="g-menu-link" href="<?= url::site("latestupdates/user_profiles/albums/{$user_id}") ?>" title="<?= t("Recent Albums") ?>"><?= t("Recent Albums") ?></a></li>
  </ul> 
</div>
