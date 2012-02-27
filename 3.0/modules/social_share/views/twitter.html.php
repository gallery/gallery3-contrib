<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-social_share-twitter_tweet">
<a href="https://twitter.com/share"
   class="twitter-share-button"
   data-lang="en"
   data-size="<?= module::get_var("social_share", "twitter_size") ?>"
   data-count="<?= module::get_var("social_share", "twitter_count_location") ?>">
   Tweet
   </a>
<script>!function(d,s,id)
{ var js,fjs=d.getElementsByTagName(s)[0];
  if(!d.getElementById(id))
    { js=d.createElement(s);
      js.id=id;
      js.src="//platform.twitter.com/widgets.js";
      fjs.parentNode.insertBefore(js,fjs);
    }
}
(document,"script","twitter-wjs");
</script>
</div>