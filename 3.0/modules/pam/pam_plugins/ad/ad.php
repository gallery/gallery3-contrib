<?php defined("SYSPATH") or die("No direct script access.");


/**
  * a plugin to authenticate user accounts on an Active Directory domain controller
  *
  */
class pam_ad
{
  private $adldap;
  private $name;
  private $pass;
  private $auth;


  /**
   *
   * @param string $name
   * @param string $pass
   */
  public function __construct($name, $pass)
  {
    require("adLDAP/adLDAP.php");
    // this will load $options
    require('config.php');

    $this->name = $name;
    $this->pass = $pass;
    $this->adldap = new adLDAP($options);
    $this->_auth();

  }

  /**
   *
   * @return boolean
   */
  public function isAuth()
  {
    return $this->auth;
  }

  /**
   *
   * @return object or false
   */
  public function getAccount()
  {
    return $this->_account();
  }

  /**
   * perform the AD authentication and set the var auth
   */
  private function _auth()
  {
    if ($this->adldap->authenticate($this->name, $this->pass)){
      $this->auth = TRUE;
    }
    else {
      $this->auth = FALSE;
    }
  }


  /**
   *
   * @return object or false
   */
  public function _account()
  {
      $result = $this->adldap->user_info($this->name);
      $user_info = $result[0];
      if (isset($user_info)){
        $account = array(
          'name' => $this->name,
          'full_name' => $user_info['displayname'][0],
          'email' => $user_info['mail'][0],
        );
      return (object) $account;
      }

  return false;
  }


}

