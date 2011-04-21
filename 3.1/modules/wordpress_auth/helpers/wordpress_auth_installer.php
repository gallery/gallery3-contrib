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
class wordpress_auth_installer {
    static function can_activate() {
        $messages = array();
        try {
            // load config
            require MODPATH . 'wordpress_auth/config/identity.php';
            // create instance and fetch admin user
            $driver = ucfirst($config['wordpress_auth']['driver']);
            $filename = MODPATH . 'wordpress_auth/libraries/drivers/IdentityProvider/' . $driver . '.php';
            $classname = 'IdentityProvider_' . $driver . '_Driver';
            require($filename);
            $wordpress_auth_provider = new $classname($config['wordpress_auth']['params']);
            $admin = $wordpress_auth_provider->admin_user();
            // Everything is fine
            $messages["warn"][] = IdentityProvider::confirmation_message();
        }
        catch (Exception $e) {
            $messages["error"][] =
                    'Cannot install Wordpress identity provider. Error: ' . $e->getMessage();
        }
        return $messages;
    }
    
    static function install() {
        IdentityProvider::change_provider('wordpress_auth');
    }

    static function initialize() {
        module::set_version('wordpress_auth', 1);
        $root = item::root();
        foreach (IdentityProvider::instance()->groups() as $group) {
            module::event("group_created", $group);
            access::allow($group, "view", $root);
            access::allow($group, "view_full", $root);
        }
    }

    static function uninstall() {
        // Delete all groups so that we give other modules an opportunity to clean up
        $wordpress_auth_provider = new IdentityProvider("wordpress_auth");
        foreach ($wordpress_auth_provider->groups() as $group) {
            module::event("group_deleted", $group);
        }
    }
}
