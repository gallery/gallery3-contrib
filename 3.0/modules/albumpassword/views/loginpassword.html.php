<script type="text/javascript">
  function ajaxify_login_reset_form() {
    $("#g-login form").ajaxForm({
      dataType: "json",
      success: function(data) {
        if (data.form) {
          $("#g-login form").replaceWith(data.form);
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
<div id="g-login-password">
  <ul>
    <li id="g-login-password-form">
      <?= $form ?>
    </li>
  </ul>
</div>
