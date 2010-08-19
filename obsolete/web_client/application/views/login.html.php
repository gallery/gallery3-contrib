<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="login">
  <ul>
    <li id="g-login-form">
      <?= form::open("g3_client/login") ?>
      <fieldset>
      <legend>Please provide your userid and password for the remote system</legend>
        <ul>
          <li>
            <?= form::label("user", "User Id:") ?><br/>
            <?= form::input("user", $form["user"]) ?>
            <?= empty($errors["user"]) ? "" : "<span class=\"error\">{$errors["user"]}</span>" ?>
          </li>
          <li>
            <?= form::label("password", "Password") ?><br/>
            <?= form::password("password", $form["password"]) ?>
            <?= empty($errors["password"]) ? "" : "<span class=\"error\">{$errors["password"]}</span>" ?>
          </li>
          <li>
            <?= form::submit("submit", "Login") ?>
            <?= form::input(array('type'=>'reset','name'=>'reset'), "Reset") ?>
          </li>
        </ul>
        </fieldset>
      </form>
    </li>
  </ul>
</div>

