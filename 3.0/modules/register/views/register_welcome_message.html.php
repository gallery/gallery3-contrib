<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-welcome-message">
  <h1 style="display: none">
    <?= t("Welcome to Gallery 3!") ?>
  </h1>

  <p>
    <h2>
      <?= t("Welcome to Gallery3") ?>
    </h2>
  </p>

  <p>
  <?= t("First things first.  You're logged in to the <b>%user_name</b> account. We have generated a password for you (%password). You should change your password to something that you'll remember.", array("user_name" => $user->name, "password" => $password)) ?>
  </p>

  <p>
    <a href="<?= url::site("register/change_password/{$user->id}/$password") ?>"
      title="<?= t("Edit your profile")->for_html_attr() ?>"
      id="g-after-install-change-password-link"
      class="g-button ui-state-default ui-corners-all">
      <?= t("Change password now") ?>
    </a>
    <script type="text/javascript">
      $("#g-after-install-change-password-link").gallery_dialog();
    </script>
  </p>

  <p>
    <?= t("Want to learn more? The <a href=\"%url\">Gallery website</a> has news and information about the Gallery project and community.", array("url" => "http://gallery.menalto.com")) ?>
  </p>

  <p>
    <?= t("Having problems? There's lots of information in our <a href=\"%codex_url\">documentation site</a> or you can <a href=\"%forum_url\">ask for help in the forums!</a>", array("codex_url" => "http://codex.gallery2.org/Main_Page", "forum_url" => "http://gallery.menalto.com/forum")) ?>
  </p>
</div>
