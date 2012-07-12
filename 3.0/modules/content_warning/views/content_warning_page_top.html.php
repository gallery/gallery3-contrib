<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="jqmWindow" id="content_warning_dialog">
  <hr />
  <h3> <?= module::get_var("content_warning", "title") ?></h3>
  <br />

  <p><?= nl2br(module::get_var("content_warning", "message")) ?></p>
  <br />

  <div id="cw_buttons_container">
    <div class="cw_buttons" id="cw_ko">
      <a href="<?= module::get_var("content_warning", "exit_link_url") ?>">
        <?= module::get_var("content_warning", "exit_link_text") ?>
      </a>
    </div>
    <div class="cw_buttons" id="cw_ok">
      <a href="<?= url::site("content_warning?cw=1") ?>">
        <?= module::get_var("content_warning", "enter_link_text") ?>
      </a>
    </div>
  </div>
</div>
<script type="text/javascript">
  $("#content_warning_dialog").ready(function($){
    $("#content_warning_dialog").jqm().jqmShow({});
  });
</script>
