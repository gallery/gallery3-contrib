<?php defined("SYSPATH") or die("No direct script access.");

$options = array(
       'account_suffix' => "@domain.com",
       'base_dn' => "DC=USER,DC=COM",
       'domain_controllers' => array ("ldap://doamin.controller.com"),
       'ad_username' => 'username',
       'ad_password' => 'password'
       );


