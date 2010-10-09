<?php

$username=strtoupper($_POST["username"]); //remove case sensitivity on the username
$password=$_POST["password"];

$options = array(
 'account_suffix' => "@trinity-health.org",
 'base_dn' => "dc=trinity-health,dc=org",
 'domain_controllers' => array ("ldap://addir.trinity-health.org"),
 'ad_username' => 'js224113',
 'ad_password' => 'pass4tis'
 );



if ($_POST["oldform"]){ //prevent null bind

  if ($username!=NULL && $password!=NULL){
    //include the class and create a connection
    include ("adLDAP.php");
    $adldap = new adLDAP($options);

    //authenticate the user
    if ($adldap -> authenticate($username,$password)){
      //establish your session and redirect
      $failed=0;
    }
  }
  $failed=1;
}

?>


<html>
<head>
<title>adLDAP example</title>
</head>

<body>
Please login to continue.<br>

<form method='post' action='<?php echo $_SERVER["PHP_SELF"]; ?>'>
<input type='hidden' name='oldform' value='1'>

Username: <input type='text' name='username' value='<?php echo ($username); ?>'><br>
Password: <input type='password' name='password'><br>
<br>

<input type='submit' name='submit' value='Submit'><br>
<?php if ($failed){ echo ("<br>Login Failed!<br><br>\n"); } ?>
</form>

<?php if ($logout=="yes") { echo ("<br>You have successfully logged out."); } ?>


</body>

</html>

