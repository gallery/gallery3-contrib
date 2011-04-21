<?php defined("SYSPATH") or die("No direct script access.")
?><div id="g-Save">Please enter the following details. <?

if (favourites_configuration::isEmailAdmin()){

  ?>An e-mail will be sent to both you and <?=favourites_configuration::getOwner()?> with a link to this list.<?
}
else{
  ?>An e-mail will be sent to you with a link to this list.<?
}
?><div id="favourites-save-form"><?= $form ?></div></div>