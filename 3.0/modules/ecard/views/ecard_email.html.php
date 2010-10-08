<?php defined("SYSPATH") or die("No direct script access.") ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <body>
    <table>
	  <tr>
		<td colspan="2"><?= html::clean($message) ?></td>
	  </tr>
	  <tr>
		<td colspan="2"><?= html::clean($custom_message) ?></td>
	  </tr>	  
	  <tr>
		<td colspan="2"><a href="<?= $item->abs_url() ?>"><img src="<?= $image ?>" border="0" style="padding: 2em 0;" /></a></td>
	  </tr>	  
      <tr>
        <td><?= t("Title:") ?></td>
        <td><?= html::purify($item->title) ?></td>
      </tr>
      <tr>
        <td><?= t("Url:") ?></td>
        <td>
          <a href="<?= $item->abs_url() ?>">
            <?= $item->abs_url() ?>
          </a>
        </td>
      </tr>
      <? if ($item->description): ?>
      <tr>
        <td><?= t("Description:") ?></td>
         <td><?= nl2br(html::purify($item->description)) ?></td>
      </tr>
      <? endif ?>
    </table>
  </body>
</html>
