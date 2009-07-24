<?php defined("SYSPATH") or die("No direct script access.") ?>
<div class="gBlock">
  <h2>
    <?= t("User Homes") ?>
  </h2>

  <div class="gBlockContent">
    <table id="gUserAdminList">
      <tr>
	<th><?= t("Username") ?></th>
        <th><?= t("Full name") ?></th>
        <th><?= t("Home") ?></th>
      </tr>
      <? foreach ($users as $i => $user): ?>
      <tr id="gUser-<?= $user->id ?>" class="<?= text::alternate("gOddRow", "gEvenRow") ?> <?= $user->admin ? "admin" : "" ?>">
        <td id="user-<?= $user->id ?>" class="core-info ">
          <?= p::clean($user->name) ?>
	</td>
        <td>
          <?= p::clean($user->full_name) ?>
        </td>
	<td>
	<select id="s_<?=$user->id ?>">
           <option value='0'>None</option>
           <?= $album_tree ?>
	  </select>
<script type="text/javascript">
$(document).ready(function()
{
	var select=$("#s_<?=$user->id ?>");
<? if ($user->home): ?>
	select.val(<?=$user->home?>);
<? endif; ?>  
	var churl = "<?= url::site("admin/user_homes/change_home/$user->id/__ALBUM_ID__") ?>";
	select.change(function()
	{
		var album_id=select.val();
		$.get(churl.replace("__ALBUM_ID__", album_id));
	});
});
</script>
	</td>
	</tr>
      <? endforeach ?>
   </table>
  </div>
</div>
