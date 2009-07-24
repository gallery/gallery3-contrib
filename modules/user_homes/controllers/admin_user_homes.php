<?php defined("SYSPATH") or die("No direct script access.");

class Admin_User_Homes_Controller extends Controller 
{
	/**
	 * the index page of the user homes admin
	 */
	public function index() 
	{
		$view = new Admin_View("admin.html");
		$view->content = new View("admin_user_homes.html");		
		$view->content->users = ORM::factory("user")->orderby("name")->find_all();
		$root = ORM::factory("item", 1);
		$view->content->album_tree = $this->tree($root, "");

		print $view;
	}

	/**
	 * recursive function to build drop down list of all galleries
	 */
	function tree($parent, $dashes) 
	{

    		$albums = ORM::factory("item")
      			->where(array("parent_id" => $parent->id, "type" => "album"))
      			->orderby(array("title" => "ASC"))
      			->find_all();


    		$view = new View("album_list.html");
    		$view->id = $parent->id;
    		if ($parent->id == "1")
    		{
			$view->name = "root";
    		}
    		else
		{
    			$view->name = "$dashes $parent->name";
		}

		$view->children = "";
		foreach ($albums as $album) 
		{
      			$view->children .= $this->tree($album, "-$dashes");
    		}
    		return $view->__toString();
  	}

	/**
	 * Method called when the user home is changed
	 */
	public function change_home($user_id, $home)
	{
    		$user = ORM::factory("user", $user_id);
		if ($user->loaded)
		{
			if ($home==0)
			{
				$user->home = null;				
			}
			else
			{
				$user->home = $home;
			}
			$user->save();
		}
	}
	
}
