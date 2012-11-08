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
class emboss_task_Core {

  static function available_tasks() {
    $q = emboss::find_dirty();
    $n = $q->count();

    $description = ( ($n==0)
                    ? (t('All photo overlays are up to date') )
                    : t2('one Photo needs its emboss overlay updated',
                         "$n Photos need their emboss overlay updated", $n) );

    $tasks[] = Task_Definition::factory()
      ->callback('emboss_task::update_overlays')
      ->name(t('Update photo embossing'))
      ->description($description)
      ->severity($n>0 ? log::WARNING : log::SUCCESS);
    return $tasks;
  }

  static function update_overlays($task)
  {
    $errors = array();
    try {
      $mode = $task->get('mode','init');
      switch($mode) {
      case 'init':
        $q = emboss::find_dirty();
        foreach ($q as $item) {
          $ids[] = array('id'=>$item->id,
                         'image_id'=>$item->image_id,
                         'overlay_id'=>$item->best_overlay_id);
        }
        $count = count($ids);

        if($count>0) {
          $task->set('ids',$ids);
          $task->set('count',$count);
          $task->set('current',0);
          $task->set('mode','continue');
        } else {
          $task->done = true;
          $task->state = 'success';
          $task->percent_complete = 100;
          site_status::clear('emboss_dirty');
          return;
        }
        break;

      case 'continue':
        $ids     = $task->get('ids');
        $count   = $task->get('count');
        $current = $task->get('current');
        break;
      }
      
      $i = 1*$current;
      $id = $ids[$i];
      $current++;
      $task->set('current',$current);
      
      emboss_task::do_embossing($id['id'],$id['image_id'],$id['overlay_id']);

      if($current>=$count) {
        $task->done = true;
        $task->state = 'success';
        $task->percent_complete = 100;
        $task->status = 'Complete';
        site_status::clear('emboss_dirty');
      } else {
        $task->percent_complete = $current/$count * 100;
        $task->status = t("Reembossed $current of $count photos");
      }


    } catch (Exception $e) {
      Kohana_Log::add('error',(string)$e);
      $task->done = true;
      $task->state = 'error';
      $task->status = $e->getMessage();
      $errors[] = (string)$e;
    }
    if ($errors) {
      $task->log($errors);
    }
  }

  static function do_embossing($id,$image_id,$overlay_id)
  {
    $gravity      = module::get_var('emboss','gravity');
    $transparency = module::get_var('emboss','transparency');

    $item = ORM::factory('item')->where('id','=',$image_id)->find();
    $path = $item->file_path() . $name;
    $orig = str_replace(VARPATH.'albums/',VARPATH.'originals/',$path);

    @unlink($path);

    if($overlay_id<0) {
      log::info('emboss','Remove embossing from '.$item->name);
      @copy($orig,$path);
      
    } else {
      $overlay = ORM::factory('emboss_overlay')->where('id','=',$overlay_id)->find();
      $overlay_path = VARPATH.'modules/emboss/'.$overlay->name;
      
      $opts['file'] = $overlay_path;
      $opts['position'] = $gravity;
      $opts['transparency'] = 100-$transparency;
      
      log::info('emboss','Embossing '.$item->name.' with '.$overlay->name);

      gallery_graphics::composite($orig,$path,$opts);
    }

    $item->thumb_dirty = 1;
    $item->resize_dirty = 1;
    $item->save();

    graphics::generate($item);

    db::build()->update('emboss_mappings')
      ->where('id','=',$id)
      ->set('cur_overlay_id',$overlay_id)
      ->set('cur_gravity',$gravity)
      ->set('cur_transparency',$transparency)
      ->execute();
  }
  


}


