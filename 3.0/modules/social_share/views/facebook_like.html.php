<?php defined("SYSPATH") or die("No direct script access.");
$appId = module::get_var("social_share", "facebook_like_appId");
$selfURL = url::abs_current(true);
$codeType = module::get_var("social_share", "facebook_like_code_type");
$layout = module::get_var("social_share", "facebook_like_layout", "standard"); 
$action = module::get_var("social_share", "facebook_like_action", "like"); 
if (module::get_var("social_share", "facebook_like_show_faces")) {
	$show_faces = "true";
	$hite = 65;
} else {
	$show_faces = "false";
	$hite = 35;
}
if (module::get_var("social_share", "facebook_like_send")) {
	$send = "true";
} else {
	$send = "false";
}
?>
<div class="g-social_share-facebook_like">	
<?php if ($codeType == 'iframe'){ ?>

<iframe src="http://www.facebook.com/plugins/like.php?app_id=<?= $appId ?>
&amp;href=<?= $selfURL; ?>
&amp;layout=<?= $layout?>
&amp;send=false
&amp;show_faces=<?= $show_faces ?>
&amp;width=180
&amp;locale=<?= locales::cookie_locale(); ?>
&amp;action=<?= $action ?>
&amp;colorscheme=light&amp;height=<?= $hite; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:180px; height:<?= $hite; ?>px;" allowTransparency="true">
</iframe>

<?php } else { ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php if($codeType == 'xfbml'){?>

<fb:like href="<?= $selfURL; ?>" send="<?= $send ?>" width="180" show_faces="<?= $show_faces ?>" layout="<?= $layout?>" action="<?= $action ?>"></fb:like>

<?php } else { ?>

<div class="fb-like" data-href="<?= $selfURL; ?>" data-send="<?= $send ?>" data-layout="<?= $layout?>" data-width="180" data-show-faces="<?= $show_faces ?>" data-action="<?= $action ?>"></div>

<?php } 
}
?>
</div>
<?php
/**
 * Only show the like button, css and JS if the item is vewable by the guest user 
 * as facebook is a guest user to get the thumb of the item.  If this is a dynamic 
 * album then use the root album to check to see if the guest has permissions.
 */
$guest = user::lookup("1");
$item = "";
if ($theme->item()) {
  $item = $theme->item();
} else {
  $item = ORM::factory("item", 1);
}
if (access::user_can($guest, "view", $item)) {
  $show_like_code = true;
}
?>
