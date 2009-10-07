<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="gAdminLdap">
  <h1> <?= t("LDAP Configuration") ?> </h1>
  <p>
    <?= t("LDAP is an alternate authentication system.  When you switch to it, all your Gallery3 users and groups <b>will be deleted</b> and you'll use users and groups from your LDAP directory.") ?>
  </p>

  <p>
    <?= t("Your current LDAP configuration is:") ?>
  </p>
  <table>
    <tr>
      <td>
	<?= t("Base LDAP url") ?>
      </td>
      <td>
	<?= $config["url"] ?>
      </td>
    </tr>
    <tr>
      <td>
	<?= t("Group LDAP Domain") ?>
      </td>
      <td>
	<?= $config["group_domain"] ?>
      </td>
    </tr>
    <tr>
      <td>
	<?= t("User LDAP Domain") ?>
      </td>
      <td>
	<?= $config["user_domain"] ?>
      </td>
    </tr>
    <tr>
      <td>
	<?= t("Groups") ?>
      </td>
      <td>
	<?= join(", ", $config["groups"]) ?>
      </td>
    </tr>
    <tr>
      <td>
	<?= t("Admin users") ?>
      </td>
      <td>
	<?= join(", ", $config["admins"]) ?>
      </td>
    </tr>
  </table>

  <h2> <?= t("LDAP is not currently active") ?> </h2>
  <p>
    <?= t("Upon activation, all existing users and groups will be deleted.  The groups listed above and all available users will be associated with Gallery 3.  You will be logged in as the <b>%username</b> user.  <b>There is no undo!</b>", array("username" => $config["admins"][0])) ?>
  </p>

  <a href="<?= url::site("admin/ldap/activate?csrf=$csrf") ?>">activate</a>
</div>
