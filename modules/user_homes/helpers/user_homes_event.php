<?php defined("SYSPATH") or die("No direct script access.");

class user_homes_event_Core 
{
	/**
	 * called when a user logs in. This will setup the session with the
	 * user home if it exists on the database. This means when the page
	 * is refreshed after logging in the direction can occur.
	 */
  	static function user_login($user)
  	{
	  	if ($user->home)
	  	{
		  
		 	$session = Session::instance();
		  	$session->set("redirect_home",$user->home);
	  	}

  	}

	/**
	 * called after a log in occurs and when the first gallery is loaded.
	 * if the home variable exists on the session then a redirect will 
	 * occur to that home and the variable removed from the session to 
	 */
	static function gallery_ready()
	{
		
		$session = Session::instance();
		$home = $session->get("redirect_home");
		if ($home)
		{
			// remove from session to ensure redirect does not
			// occur again
			$session->set("redirect_home",null);	
			url::redirect("albums/$home");
		}

	}  
}
