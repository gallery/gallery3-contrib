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
		$home = ORM::factory("user_home")
			->where("id", $user->id)->find();
	  	if ($home)
		{
			if ($home->home!=0)
			{
		 		$session = Session::instance();
		  		$session->set("redirect_home",$home->home);
			}
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

	/** 
	 * called just before a user is deleted. This will remove the user from 
	 * the user_homes directory.
	 */
  	static function user_before_delete($user) 
	{
    		ORM::factory("user_home")
      			->where("id", $user->id)
      			->delete_all();
  	}

	/**
	 * called when admin is adding a user
	 */
	static function user_add_form_admin($user, $form)
	{
		$form->add_user->dropdown("user_home")
        		->label(t("Home Gallery"))
        		->options(self::createGalleryArray())
        		->selected(0);		
	}

	/**
	 * called after a user has been added
	 */
	static function user_add_form_admin_completed($user, $form)
	{
		$home = ORM::factory("user_home")
			->where("id", $user->id)->find();
		$home->id=$user->id;
		$home->home=$form->add_user->user_home->value;
		$home->save();
	}

	/**
	 * called when admin is editing a user
	 */
	static function user_edit_form_admin($user, $form)
	{
		$home = ORM::factory("user_home")
			->where("id", $user->id)->find();

		if ($home)
		{
			$selected = $home->home;
		}
		else
		{
			$selected = 0;
		}
		$form->edit_user->dropdown("user_home")
        		->label(t("Home Gallery"))
        		->options(self::createGalleryArray())
        		->selected($selected);			
	}
       	/**
	 * called after a user had been edited by the admin
	 */
	static function user_edit_form_admin_completed($user, $form)
	{
		$home = ORM::factory("user_home")
			->where("id", $user->id)->find();
		$home->id=$user->id;
		$home->home=$form->edit_user->user_home->value;
		$home->save();
		
	}

       
	/**
	 * called when user is editing their own form
	 */
	static function user_edit_form($user, $form)
	{
		$home = ORM::factory("user_home")
			->where("id", $user->id)->find();

		if ($home)
		{
			$selected = $home->home;
		}
		else
		{
			$selected = 0;
		}
		$form->edit_user->dropdown("user_home")
        		->label(t("Home Gallery"))
        		->options(self::createGalleryArray())
        		->selected($selected);			
	}

	/**
	 * called after a user had been edited by the user
	 */
	static function user_edit_form_completed($user, $form)
	{
		$home = ORM::factory("user_home")
			->where("id", $user->id)->find();
		$home->id=$user->id;
		$home->home=$form->edit_user->user_home->value;
		$home->save();
		
	}
	
	/**
	 * creates an array of galleries
	 */
	static function createGalleryArray()
	{
		$array[0] = "none";
		$root = ORM::factory("item", 1);
		self::tree($root, "", $array);

		return $array;
	}

	/**
	 * recursive function to build array for drop down list
	 */
	static function tree($parent, $dashes, &$array) 
	{
    		if ($parent->id == "1")
		{
			$array[$parent->id] = "home";
    		}
    		else
		{
			$array[$parent->id] = "$dashes $parent->name";
		}

		$albums = ORM::factory("item")
      			->where(array("parent_id" => $parent->id, "type" => "album"))
      			->orderby(array("title" => "ASC"))
      			->find_all();
		foreach ($albums as $album) 
		{
      			self::tree($album, "-$dashes", $array);
    		}
    		return;
  	}

	
}
