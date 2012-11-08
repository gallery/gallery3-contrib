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
class emboss_event_Core {
  static function admin_menu($menu,$theme) {
    module::set_var('emboss','admin_menu',1);
    $menu->get('content_menu')
      ->append(
        Menu::factory('link')
        ->id('emboss')
        ->label(t('Emboss'))
        ->url(url::site('admin/emboss')));
  }

  static function item_moved($item,$olddir)
  {
    if( ! ($item->is_photo() || $item->is_album()) ) {
      return;
    }
    
    $name = $item->name;
    $old_path = $olddir->file_path() . '/' . $name;
    $new_path = $item->file_path();
    
    if( $new_path == $old_path) {
      return;
    }
    
    $old_orig = str_replace(VARPATH . 'albums/', VARPATH . 'originals/', $old_path);
    $new_orig = str_replace(VARPATH . 'albums/', VARPATH . 'originals/', $new_path);
    $new_dir = str_replace('/'.$name , '',$new_orig);

    if( file_exists($old_orig))
      {
        emboss::mkdir_recursive($new_dir);
        @rename($old_orig,$new_orig);
        log::info('emboss','Moved '.$item->name.' to '.str_replace(VARPATH,'',$new_dir));
      }
  }

  static function item_updated($original,$item)
  {
    if( ! ($item->is_photo() || $item->is_album()) ) {
      return;
    }
    $oldpath = $original->file_path();
    $newpath = $item->file_path();
    if( $oldpath != $newpath ) {
      $oldorig = str_replace(VARPATH.'albums/',VARPATH.'originals/',$oldpath);
      $neworig = str_replace(VARPATH.'albums/',VARPATH.'originals/',$newpath);
      log::info('emboss',"rename $oldorig to $neworig");
      @rename($oldorig,$neworig);
    }
  }

  static function item_deleted($item)
  {
    if( ! $item->is_photo()  ) {
      return;
    }

    $name = $item->name;
    $id = $item->id;
    $path = $item->file_path();
    $orig = str_replace(VARPATH.'albums/',VARPATH.'originals/',$path);

    @unlink($orig);

    db::build()
      ->from('emboss_mappings')
      ->where('image_id','=',$id)
      ->delete()
      ->execute();

    log::info('emboss',"item_deleted: $name");
  }

  static function item_created($item)
  {
    if( ! $item->is_photo()  ) {
      return;
    }

    $path = $item->file_path();
    $dirs = explode('/',$path);
    array_pop($dirs);
    $dir  = implode('/',$dirs);

    $orig    = str_replace(VARPATH.'albums/',VARPATH.'originals/',$path);
    $origdir = str_replace(VARPATH.'albums/',VARPATH.'originals/',$dir);
    
    emboss::mkdir_recursive($origdir);
    @copy($path,$orig);

    $q = ORM::factory('emboss_mapping');
    $q->image_id = $item->id;
    $q->best_overlay_id = emboss::determine_best_overlay($item);
    $q->cur_overlay_id = -1;
    $q->cur_gravity = '';
    $q->cur_transparency = -1;
    $q->save();

    emboss::check_for_dirty();
  }

}

