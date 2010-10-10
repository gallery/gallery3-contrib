<?php

class user_rest_Core {
	static function get($request) {
		$user = rest::resolve($request->url);

		return array(
			"url" => $request->url,
			"entity" => array(
				"display_name" => $user->display_name()
			)
		);
	}
	
	
	static function resolve($id) {
		$user = identity::lookup_user($id);
		
		if (!self::_can_view_profile_pages($user)) {
			throw new Kohana_404_Exception();
		}
		return $user;
	}
	
	
	static function _can_view_profile_pages($user) {
		if (!$user->loaded()) {
		  return false;
		}

		if ($user->id == identity::active_user()->id) {
			// You can always view your own profile
			return true;
		}

		switch (module::get_var("gallery", "show_user_profiles_to")) {
		case "admin_users":
			return identity::active_user()->admin;

		case "registered_users":
			return !identity::active_user()->guest;

		case "everybody":
			return true;
		
		default:
			// Fail in private mode on an invalid setting
			return false;
		}
	}
}
  
 ?>