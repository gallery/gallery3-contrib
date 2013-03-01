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
class Admin_g1_import_Controller extends Admin_Controller {
  public function index() {
    if (g1_import::is_configured()) {
      g1_import::init();
    }

    $view = new Admin_View('admin.html');
    $view->page_title = t('Gallery 1 import');
    $view->content = new View('admin_g1_import.html');

    if (is_dir(g1_import::$album_dir)) {
      $view->content->g1_stats = $g1_stats = g1_import::g1_stats();
      $view->content->g3_stats = $g3_stats = g1_import::g3_stats();
      $view->content->g1_sizes = g1_import::common_sizes();
      $view->content->g1_version = g1_import::version();

      // Don't count tags because we don't track them in g1_map
      $view->content->g1_resource_count =
        $g1_stats['users'] + $g1_stats['groups'] + $g1_stats['albums'] +
        $g1_stats['photos'] + $g1_stats['movies'] + $g1_stats['comments'];
      $view->content->g3_resource_count =
        $g3_stats['user'] + $g3_stats['group'] + $g3_stats['album'] +
        $g3_stats['item'] + $g3_stats['comment'] + $g3_stats['tag'];
    }

    $view->content->form = $this->_get_import_form();
    $view->content->version = '';
    $view->content->thumb_size = module::get_var('gallery', 'thumb_size');
    $view->content->resize_size = module::get_var('gallery', 'resize_size');

    if (g1_import::is_initialized()) {

    	if (count(g1_import::$warn_utf8)>0) {
      	message::error(t('Your G1 contains %count folder(s) containing nonstandard characters that G3 doesn\'t work with: <pre>%names</pre>Please rename the above folders in G1 before trying to import your data.', array('count' => count(g1_import::$warn_utf8), 'names' => "\n\n  ".implode("\n  ", g1_import::$warn_utf8)."\n\n")));
      }

      if ((bool)ini_get('eaccelerator.enable') || (bool)ini_get('xcache.cacher')) {
        message::warning(t('The eAccelerator and XCache PHP performance extensions are known to cause issues.  If you\'re using either of those and are having problems, please disable them while you do your import.  Add the following lines: <pre>%lines</pre> to gallery3/.htaccess and remove them when the import is done.', array('lines' => "\n\n  php_value eaccelerator.enable 0\n  php_value xcache.cacher off\n  php_value xcache.optimizer off\n\n")));
      }

      foreach (array('notification', 'search', 'exif') as $module_id) {
        if (module::is_active($module_id)) {
          message::warning(
            t('<a href="%url">Deactivating</a> the <b>%module_id</b> module during your import will make it faster',
              array('url' => url::site('admin/modules'), 'module_id' => $module_id)));
        }
      }
    } else if (g1_import::is_configured()) {
      $view->content->form->configure_g1_import->albums_path->add_error('invalid', 1);
    }
    print $view;
  }

  public function save() {
    access::verify_csrf();

    $form = $this->_get_import_form();
    if ($form->validate()) {
      $albums_path = $form->configure_g1_import->albums_path->value;
      if (!is_file($albums_path) && file_exists("$albums_path/albums.php")) {
        $albums_path = "$albums_path/albums.php";
      }

      if (($g1_init_error = g1_import::is_valid_albums_path($albums_path)) == 'ok') {
        message::success(t('Gallery 1 path saved'));
        module::set_var('g1_import', 'albums_path', $albums_path);
        url::redirect('admin/g1_import');
      } else {
        $form->configure_g1_import->albums_path->add_error($g1_init_error, 1);
      }
    }

    $view = new Admin_View('admin.html');
    $view->content = new View('admin_g1_import.html');
    $view->content->form = $form;
    print $view;
  }

  public function autocomplete() {
    $directories = array();
    $path_prefix = Input::instance()->get('q');
    foreach (glob('{$path_prefix}*') as $file) {
      if (is_dir($file) && !is_link($file)) {
        $directories[] = $file;

        // If we find an albums.php, include it as well
        if (file_exists("$file/albums.php")) {
          $directories[] = "$file/albums.php";
        }
      }
    }

    print implode("\n", $directories);
  }

  private function _get_import_form() {
    $albums_path = module::get_var('g1_import', 'albums_path', '');
    $form = new Forge(
      'admin/g1_import/save', '', 'post', array('id' => 'g-admin-configure-g1-import-form'));
    $group = $form->group('configure_g1_import')->label(t('Configure Gallery 1 Import'));
    $group->input('albums_path')->label(t('Filesystem path to your Gallery 1 albums.php file'))
      ->value($albums_path);
    $group->albums_path->error_messages(
      'invalid', t('The path you entered is not a Gallery 1 installation.'));
    $group->albums_path->error_messages(
      'broken', t('Your Gallery 1 install isn\'t working properly.  Please verify it!'));
    $group->albums_path->error_messages(
      'missing', t('The path you entered does not exist.'));
    $group->submit('')->value(g1_import::$album_dir=='' ? t('Change') : t('Continue'));
    return $form;
  }
}
