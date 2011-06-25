<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
  $("document").ready(function() {
    $("#g-tweet").gallery_twitter();
  });
</script>
<div id="g-twitter-tweet" class="g-block">
  <h1> <?= t("Share this %type on Twitter", array("type" => $type, "title"=> $title)) ?> </h1>
  <? if (!$is_registered): ?>
  <p><?= t("The Twitter module is not configured yet.") ?></p>
  <? elseif (!$user_token_set): ?>
  <p><?= t("You must authorize Gallery to send Tweets to your Twitter account.") ?></p>
  <p><a href="<?= $twitter_auth_url ?>"><img src="<?= url::file("modules/twitter/vendor/twitteroauth/images/lighter.png") ?>" alt="Sign in with Twitter"/></a></p>
  <? else: ?>
  <div class="g-block-content">
    <?= $form ?>
    <div id="g-twitter-character-count">
      <?= $character_count ?>
    </div>
  </div>
  <? endif; ?>
</div>