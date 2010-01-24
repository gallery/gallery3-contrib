<?php defined("SYSPATH") or die("No direct script access.") ?>
<script>
  $("#wc-edit").ready(function() {
    $("#wc-delete-cancel").click(function(event) {
      $("#g-dialog").dialog("close");
      return false;
    });
  });
</script>
<div id="wc-edit">
  <?= form::open($path) ?>
  <fieldset>
    <legend style="display: none">Confirm Delete</legend>
    <ul>
      <li>
        Do you really want to delete '<?= $title ?>'. <br/>
  Press <b>Yes</b> to continue, <b>Cancel</b> to quit
      </li>
      <li style="text-align: center">
        <?= form::submit("submit", "Yes", "id=\"wc-delete-continue\"") ?>
        <?= form::submit("submit", "Cancel", "id=\"wc-delete-cancel\"") ?>
      </li>
    </ul>
  </fieldset>
  </form>
</div>

