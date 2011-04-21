<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class IdentityProvider_Wordpressfile_Driver implements IdentityProvider_Driver {
    static $_params;
    private static $_guest_user;

    /**
     * Initializes the WordPress auth driver
     *
     * @return  void
     */
    public function __construct($params) {
        self::$_params = $params;

        if (!function_exists('get_usermeta')) {
            throw new Exception("Wordpress not loaded. Add require('modules/wordpress_auth/config/identity.php'); to index.php");
        }
    }

    /**
     * @see IdentityProvider_Driver::guest.
     */
    public function guest() {
        if (empty(self::$_guest_user)) {
            self::$_guest_user = new Wordpress_User();
            self::$_guest_user->id = 0;
            self::$_guest_user->name = "Guest";
            self::$_guest_user->full_name = "Guest";
            self::$_guest_user->guest = true;
            self::$_guest_user->admin = false;
            self::$_guest_user->locale = null;
            self::$_guest_user->email = null;
            self::$_guest_user->url = null;
        }
        return self::$_guest_user;
    }

    /**
     * @see IdentityProvider_Driver::admin_user.
     */
    public function admin_user() {
        $wp_user_search = new WP_User_Search(null, null, self::$_params['groups'][count(self::$_params['groups'])-1]);
        $admins = $wp_user_search->get_results();

        if (count($admins) == 0) {
            throw new Exception("@todo NO ADMIN USER FOUND");
        }

        return new Wordpress_User($admins[0]);
    }

    /**
     * @see IdentityProvider_Driver::create_user.
     */
    public function create_user($name, $full_name, $password, $email) {
        throw new Exception("@todo INVALID OPERATION");
    }

    /**
     * @see IdentityProvider_Driver::is_correct_password.
     */
    public function is_correct_password($user, $password) {
        return user_pass_ok($user->name, $password);
    }

    /**
     * @see IdentityProvider_Driver::lookup_user.
     */
    public function lookup_user($id) {
        $user = get_userdata($id);

        if (isset($user)) {
            return new Wordpress_User($user);
        } else {
            return null;
        }
    }

    /**
     * @see IdentityProvider_Driver::lookup_user_by_name.
     */
    public function lookup_user_by_name($name) {
        $user = get_userdatabylogin($name);

        if (isset($user)) {
            return new Wordpress_User($user);
        } else {
            return null;
        }
    }

    /**
     * @see IdentityProvider_Driver::create_group.
     */
    public function create_group($name) {
        throw new Exception("@todo INVALID OPERATION");
    }

    /**
     * @see IdentityProvider_Driver::everybody.
     */
    public function everybody() {
        return new Wordpress_Group('everybody');
    }

    /**
     * @see IdentityProvider_Driver::registered_users.
     */
    public function registered_users() {
        return new Wordpress_Group('registered_users');
    }

    /**
     * @see IdentityProvider_Driver::get_user_list.
     */
    public function get_user_list($ids) {
        $users = array();
        foreach ($ids as $id) {
            $users[] = $this->lookup_user($id);
        }
        return $users;
    }

    /**
     * @see IdentityProvider_Driver::lookup_group.
     */
    public function lookup_group($name) {
        $groups = $this->groups();
        foreach ($groups as $group) {
            if ($group->id == $name) {
                return $group;
            }
        }
        return null;
    }

    /**
     * Look up the group by name.
     * @param string     $name the name of the group to locate
     * @return Group_Definition
     */
    public function lookup_group_by_name($name) {
        return $this->lookup_group(strtolower($name));
    }

    /**
     * @see IdentityProvider_Driver::groups.
     */
    public function groups() {
        if (isset($this->_groups)) {
            return $this->_groups;
        }
        $wp_role_obj = new WP_Roles();
        $wp_roles = $wp_role_obj->roles;
        $groups = array(new Wordpress_Group('everybody'));
        foreach ($wp_roles as $k => $r) {
            $groups[] = new Wordpress_Group(strtolower($k));
        }
        return $groups;
    }

    /**
     * @see IdentityProvider_Driver::add_user_to_group.
     */
    function add_user_to_group($user, $group) {
        throw new Exception("@todo INVALID OPERATION");
    }

    /**
     * @see IdentityProvider_Driver::remove_user_to_group.
     */
    function remove_user_from_group($user, $group) {
        throw new Exception("@todo INVALID OPERATION");
    }
} // End Identity Gallery Driver

class Wordpress_User implements User_Definition {
    private $user_info;

    public function __construct($user_info=null) {
        $this->user_info = get_object_vars($user_info);
    }

    public function display_name() {
        return $this->user_info['user_nicename'];
    }

    public function groups() {
        return $this->groups;
    }



    public function __get($key) {
        switch($key) {
            case "name":
                return $this->user_info['user_login'];

            case "guest":
                return false;

            case "id":
                return $this->user_info['ID'];

            case"groups":
                $groups = array(new Wordpress_Group('everybody'));

                global $table_prefix;
                $user_roles = get_usermeta($this->id, $table_prefix.'capabilities');
                if (is_array($user_roles)) {
                    foreach ($user_roles as $r) {
                        $groups[] = new Wordpress_Group($r);
                    }
                }
                $this->groups = $groups;
                return $this->groups;

            case "locale":  // @todo
                return null;

            case "admin":
                foreach($this->groups as $g) {
                    if ($g->id == 'administrator') {
                        $this->admin = true;
                        return $this->admin;
                    }
                }
                $this->admin = false;
                return $this->admin;

            case "email":
                return $this->user_info['user_email'];

            case "full_name":
                return $this->user_info['user_nicename'];
                ;

            case "url":  // @todo
                return null;

            default:
                throw new Exception("@todo UNKNOWN_KEY ($key)");
        }
    }

    public function avatar_url($size=80, $default=null) {
        return sprintf("http://www.gravatar.com/avatar/%s.jpg?s=%d&r=pg%s",
                md5($this->email), $size, $default ? "&d=" . urlencode($default) : "");
    }
}

class Wordpress_Group implements Group_Definition {
    public $id;
    public $name;

    public function __construct($id, $name = null) {
        $this->id = $id;
        if (is_null($name)) $name = ucfirst($id);
        $this->name = $name;
    }
}
