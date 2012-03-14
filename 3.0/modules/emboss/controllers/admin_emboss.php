<?php defined('SYSPATH') or die('No direct script access.');
/*************************************************************************
 * Copyright (C) 2012  Michel A. Mayer                                   *
 *                                                                       *
 * This program is free software: you can redistribute it and/or modify  *
 * it under the terms of the GNU General Public License as published by  *
 * the Free Software Foundation, either version 3 of the License, or     *
 * (at your option) any later version.                                   *
 *                                                                       *
 * This program is distributed in the hope that it will be useful,       *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 * GNU General Public License for more details.                          *
 *                                                                       *
 * You should have received a copy of the GNU General Public License     *
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. *
 *************************************************************************/
class Admin_Emboss_Controller extends Admin_Controller {
  public function index() {
    $view = new Admin_View('admin.html');
    $view->page_title = t('Emboss');
    $view->content = new View('admin_emboss.html');

    $images = ORM::factory('emboss_overlay')->find_all();

    $view->content->images = $images;
    $view->content->emboss_thumbs = module::get_var('emboss','thumbs',0);
    $view->content->emboss_resize = module::get_var('emboss','resize',1);
    $view->content->emboss_full = module::get_var('emboss','full',1);
    
    print $view;
  }

  static function update() {
    access::verify_csrf();
    emboss::update_overlay_options($_POST);
    emboss::evaluate_overlays();
    emboss::check_for_dirty();
    url::redirect('admin/emboss');
  }

  static function new_overlay() {
    access::verify_csrf();
    $file = $_FILES['overlay'];
    emboss::upload_new_overlay($file);
    emboss::check_for_dirty();
    url::redirect('admin/emboss');
  }

  static function delete_overlay() {
    access::verify_csrf();
    emboss::_delete_overlay($_REQUEST['name']);
    emboss::check_for_dirty();
    url::redirect('admin/emboss');
  }

  static function clear_log() {
    db::build()
      ->delete()
      ->from('logs')
      ->where('category','=','emboss')
      ->execute();
    url::redirect('admin/emboss');
  }

  static function uninstall() {
    access::verify_csrf();
    emboss::uninstall();
    url::redirect('admin/modules');
  }
}