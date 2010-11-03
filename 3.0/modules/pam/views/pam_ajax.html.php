<?php defined("SYSPATH") or die("No direct script access.") ?>

<script type="text/javascript">
  $("#g-login-form").ready(function() {

    // Setting the focus here doesn't work on IE7, perhaps because the field is
    // not ready yet?  So set a timeout and do it the next time we're idle
    setTimeout('$("#g-username").focus()', 100);
  });

</script>
<div id="g-login">
  <ul>
    <li id="g-login-form">
      <?= $form ?>
    </li>
  </ul>
</div>
