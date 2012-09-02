<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="g-facebook-comment-block">
<h1>Facebook comments for <?= $title?></h1>
    <div id="fb-root"></div>
    <script>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?= module::get_var("facebook_comment", "appId"); ?>";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
        <fb:comments href="<?if(isset($url)) { print $url; } else { print url::abs_current(); }?>"
            num_posts="5" width="478" colorscheme="dark"> </fb:comments>
</div>

<script>
if (typeof(FB) != 'undefined') {
    FB.XFBML.parse(document.getElementById('g-dialog'));
}
setTimeout(function () {
$("#g-dialog").removeClass("g-loading-small");
}, 10);
</script>
