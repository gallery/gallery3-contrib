<?php defined("SYSPATH") or die("No direct script access.");


/**
  * a plugin to authenticate users using the Gallery3 identity
  *
  */
class pam_gallery
{
  private $result;

  public function __construct($name, $pass)
  {
    $user = identity::lookup_user_by_name($name);
    $this->result = identity::is_correct_password($user, $pass);
  }

  public function isAuth()
  {
    return $this->result;
  }

}
?>
