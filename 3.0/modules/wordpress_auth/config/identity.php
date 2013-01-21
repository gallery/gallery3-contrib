<?php defined('SYSPATH') or die('No direct script access.');
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
/*
 * @package  Wordpress_auth
 *
 * Use Wordpress installation as authentication source.
 * Wordpress Roles are mappee to Gallery groups
 *
 * The module comes with two drivers:
 * wordpressdb: Connect directly to the wordpress database defined in
 * $config['wordpress_auth']['params']['wp_database'] and authenticate
 *
 * wordpressfile: Load the entire Wordpress codebase and use Wordpress API
 * functions to authenticate users. The path to the Wordpress basedir must be
 * defined in $config['wordpress_auth']['params']['path'] and this file must
 * be included from the gallery index.php file
 *
 * wordpressdb is fastest but does only work with straight Wordpress database.
 * If other authentications plugins have been added to wordpress, then the
 * wordpressfile driver must be used.
 *
 */


$config['wordpress_auth'] = array(
  'driver'        => 'wordpressdb',
  'allow_updates' => false,
  'params'        => array(
    'wp_database' => array(
        'username' => 'user',
        'password' => 'pass',
        'database' => 'dbname',
        'host' => 'localhost',
        'table_prefix' => 'wp_',
        'charset' => 'utf8'
    ),
    'path' => 'path/to/local/wordpress/installation'
  )
);


// load code
if ($config['wordpress_auth']['driver'] == 'wordpressfile') {
    require_once $config['wordpress_auth']['params']['path'] . 'wp-load.php';
    require_once $config['wordpress_auth']['params']['path'] . 'wp-admin/includes/user.php';
}
