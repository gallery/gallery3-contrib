<?
/*
Examples file

To test any of the functions, just change the 0 to a 1.
*/

//error_reporting(E_ALL ^ E_NOTICE);

include ("adLDAP.php");
$ldap=new adLDAP($options);
//var_dump($ldap);

echo ("<pre>\n");

// authenticate a username/password
if (0){
	$result=$ldap->authenticate("username","password");
	var_dump($result);
}

// add a group to a group
if (0){
	$result=$ldap->group_add_group("Parent Group Name","Child Group Name");
	var_dump($result);
}

// add a user to a group
if (0){
	$result=$ldap->group_add_user("Group Name","username");
	var_dump($result);
}

// create a group
if (0){
	$attributes=array(
		"group_name"=>"Test Group",
		"description"=>"Just Testing",
		"container"=>array("Groups","A Container"),
	);
	$result=$ldap->group_create($attributes);
	var_dump($result);
}

// retrieve information about a group
if (0){
	$result=$ldap->group_info("Group Name");
	var_dump($result);
}

// create a user account
if (0){
	$attributes=array(
		"username"=>"freds",
		"logon_name"=>"freds@mydomain.local",
		"firstname"=>"Fred",
		"surname"=>"Smith",
		"company"=>"My Company",
		"department"=>"My Department",
		"email"=>"freds@mydomain.local",
		"container"=>array("Container Parent","Container Child"),
		"enabled"=>1,
		"password"=>"Password123",
	);
	
	$result=$ldap->user_create($attributes);
	var_dump($result);
}

// retrieve the group membership for a user
if (0){
	$result=$ldap->user_groups("username");
	print_r($result);
}

// retrieve information about a user
if (0){
	$result=$ldap->user_info("username");
	print_r($result);
}

// check if a user is a member of a group
if (0){
	$result=$ldap->user_ingroup("username","Group Name");
	var_dump($result);
}

// modify a user account (this example will set "user must change password at next logon")
if (0){
	$attributes=array(
		"change_password"=>1,
	);
	$result=$ldap->user_modify("username",$attributes);
	var_dump($result);
}

// change the password of a user
if (0){
	$result=$ldap->user_password("username","Password123");
	var_dump($result);
}
?>