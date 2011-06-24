<?php defined("SYSPATH") or die("No direct script access.") ?>

<script type="text/javascript">

  $("#g-login-form").ready(function() {
    $("#g-password-reset").bind('click', 
      function() {
        $.ajax({
          url: "<?= url::site("password/reset") ?>",
          success: function(data) {
            $("#g-login").html(data);
            ajaxify_login_reset_form();
            return false;
          }
        });
      });
  });

  function ajaxify_login_reset_form() {
    $("#g-login form").ajaxForm({
      dataType: "json",
      success: function(data) {
        if (data.form) {
          $("#g-login").replaceWith(data.form);
          ajaxify_login_reset_form();
        }
        if (data.result == "success") {
          $("#g-dialog").dialog("close");
          window.location.reload();
        }
      }
    });
  };
</script>
<div id="g-login">
  <?= $form ?>
  <? if (identity::is_writable()): ?>
  <a href="#" id="g-password-reset"><?= t("Forgot Your Password?") ?></a>
  <? endif ?>
</div>

