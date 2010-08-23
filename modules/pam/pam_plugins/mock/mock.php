<?php defined("SYSPATH") or die("No direct script access.");


/**
  * a plugin to test the PAM module
  *
  */
class pam_mock
{
  private $result;
  private $name;
  private $full_name;
  private $email;

  /**
   *
   * @param string $name
   * @param string $pass
   */
  public function __construct($name, $pass)
  {
    $this->result = ($name == $pass)?true:false;
    $this->name = $name;
    $this->full_name = 'Mock ' . $name;
    $this->email = $name . '@email.com';

  }

  /**
   *
   * @return boolean
   */
  public function isAuth()
  {
    return $this->result;
  }


  /**
   *
   * @return object or false
   */
  public function getAccount()
  {
    $account = array(
      'name' => $this->name,
      'full_name' => $this->full_name,
      'email' => $this->email,
    );

    return (object) $account;
  }



}
?>
