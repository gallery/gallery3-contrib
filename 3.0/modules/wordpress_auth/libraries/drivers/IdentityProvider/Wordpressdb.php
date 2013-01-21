<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
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
class IdentityProvider_Wordpressdb_Driver implements IdentityProvider_Driver {
    private $config;
    private $db;
    private $_guest_user;

    /**
     * Initializes the WordPress auth driver
     *
     * @return  void
     */
    public function __construct($config) {
        $this->config = $config;
        $d = $config['wp_database'];
        $this->db = new mysqli($d['host'], $d['username'], $d['password'], $d['database']);
        if ($this->db->connect_error) {
            throw new Exception('Cannot connect to mysql database (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
        }
        $this->db->set_charset($d['charset']);
    }

    /**
     * @see IdentityProvider_Driver::guest.
     */
    public function guest() {
        if (empty($this->_guest_user)) {
            $this->_guest_user = new Wordpress_User();
            $this->_guest_user->id = 0;
            $this->_guest_user->name = "Guest";
            $this->_guest_user->full_name = "Guest";
            $this->_guest_user->guest = true;
            $this->_guest_user->admin = false;
            $this->_guest_user->locale = null;
            $this->_guest_user->email = null;
            $this->_guest_user->url = null;
        }
        return $this->_guest_user;
    }

    /**
     * @see IdentityProvider_Driver::admin_user.
     */
    public function admin_user() {
        $tp = $this->config['wp_database']['table_prefix'];
        $query = 'SELECT user_id FROM ' . $tp . 'usermeta WHERE meta_key = "' . $tp . 'capabilities" AND meta_value = "a:1:{s:13:\"administrator\";b:1;}" ORDER BY user_id ASC LIMIT 1';

        $result = $this->db->query($query);
        $admin = $result->fetch_array(MYSQLI_ASSOC);
        $result->free();
        if (isset($admin)) {
            return $this->lookup_user($admin['user_id']);
        } else {
            throw new Exception("@todo NO ADMIN USER FOUND");
        }
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
        $password = trim($password);
        if ( strlen($user->user_pass) <= 32 ) {
            return $user->user_pass == md5($password);
        } else {
            $hasher = new PasswordHash(8, TRUE);
            return $hasher->CheckPassword($password, $user->user_pass);
        }
    }

    /**
     * @see IdentityProvider_Driver::lookup_user.
     */
    public function lookup_user($id) {
        if (is_numeric($id)) {
            $tp = $this->config['wp_database']['table_prefix'];
            $query = 'SELECT u.*, m.meta_value AS roles FROM ' . $tp . 'users u LEFT OUTER JOIN ' . $tp . 'usermeta m ON (u.ID = m.user_id) WHERE u.ID = ' . $id . ' AND meta_key = "' . $tp . 'capabilities"';
            $result = $this->db->query($query);
            $user = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if (isset($user)) {
                return new Wordpress_User($user);
            }
        }
        return null;
    }

    /**
     * @see IdentityProvider_Driver::lookup_user_by_name.
     */
    public function lookup_user_by_name($name) {
        $name = $this->db->escape_string($name);
        $tp = $this->config['wp_database']['table_prefix'];
        $query = 'SELECT u.*, m.meta_value AS roles FROM ' . $tp . 'users u LEFT OUTER JOIN ' . $tp . 'usermeta m ON (u.ID = m.user_id) WHERE u.user_login = "' . $name . '" AND meta_key = "' . $tp . 'capabilities"';
        $result = $this->db->query($query);
        $user = $result->fetch_array(MYSQLI_ASSOC);
        $result->free();
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
        if (!isset($this->wp_roles)) {
            $tp = $this->config['wp_database']['table_prefix'];
            $query = 'SELECT option_value FROM ' . $tp . 'options WHERE option_name = "' . $tp . 'user_roles" LIMIT 1';
            $result = $this->db->query($query);
            $wp_roles = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if (isset($wp_roles)) {
                $this->wp_roles = unserialize($wp_roles['option_value']);
            }
        }

        $groups = array(new Wordpress_Group('everybody'));
        foreach ($this->wp_roles as $k => $r) {
            $groups[] = new Wordpress_Group($k);
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
        $this->user_info = $user_info;
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

            case "groups":
                $groups = array(new Wordpress_Group('everybody'));

                if (isset($this->user_info['roles'])) {
                    $roles = unserialize($this->user_info['roles']);
                    foreach ($roles as $k => $r) {
                        $groups[] = new Wordpress_Group($k);
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

            case "url":
                return $this->user_info['user_url'];

            case "user_pass":
                return $this->user_info['user_pass'];

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
