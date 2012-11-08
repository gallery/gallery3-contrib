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
class emboss_Core {

  static function reconcile()
  {
    emboss::copy_new(VARPATH . 'albums', VARPATH . 'originals');
    emboss::remove_old(VARPATH . 'albums', VARPATH . 'originals');
  }
  
  private function error($msg)
  {
    log::error('emboss',$msg);
    message::error($msg);
  }
  
  private function success($msg)
  {
    log::success('emboss',$msg);
    message::success($msg);
  }
  
  private function info($msg)
  {
    log::info('emboss',$msg);
    message::info($msg);
  }
  
  private function copy_new($src,$dst)
  {
    if( ! is_dir($src) )
      return;
    
    if(! file_exists($dst) )
      {
        log::info('emboss',"Creating directory $dst");
        if(! @mkdir($dst) )
          {
            emboss::error("Failed to create $dst");
            return;
          }
      }
    if(! is_dir($dst) )
      {
        emboss::error("Existing $dst is not a directory");
        return;
      }
    
    if( ! ($dh = opendir($src)) )
      {
        emboss::error("Failed to open $src for reading");
        return;
      }
    
    while( $file = readdir($dh) )
      {
        if($file=='.'||$file=='..')
          continue;
        
        $srcpath = $src . '/' . $file;
        $dstpath = $dst . '/' . $file;
        
        if( is_dir($srcpath) )
          {
            emboss::copy_new($srcpath, $dstpath);
          }
        else
          {
            if(! file_exists($dstpath) )
              {
                log::info('emboss',"Copying $file to $dst");
                if(! @copy($srcpath,$dstpath) )
                  {
                    emboss::error("Failed to copy $file to $dst");
                  }
              }
          }
      }
    
    closedir($dh);
  }
  
  private function remove_old($src,$archive)
  {
    if( ! is_dir($archive) )
      return;
    
    if(! (file_exists($src) && is_dir($src)) )
      {
        log::info('emboss',"Removing directory $src");
        emboss::rmdir_recursive($archive);
        return;
      }
    
    if( ! ($dh = opendir($archive)) )
      {
        emboss::error("Failed to open $archive for reading");
        return;
      }
    
    while( $file = readdir($dh) )
      {
        if($file=='.' || $file=='..')
          continue;
        
        $srcpath = $src . '/' . $file;
        $archivepath = $archive . '/' . $file;
        
        if( is_dir($archivepath) )
          {
            emboss::remove_old($srcpath,$archivepath);
          }
        else
          {
            if( ! file_exists($srcpath) )
              {
                log::info('emboss',"Removing $file from $archive");
                if(! @unlink($archivepath) )
                  emboss::error("Failed to remove $file from $archive");
              }
          }
      }
    
    closedir($dh);
  }
  
  private function rmdir_recursive($dir) {
    if(!$dh = @opendir($dir))
      return;
    
    while ( $obj = readdir($dh))
      {
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj))
          emboss::rmdir_recursive($dir.'/'.$obj);
      }
    
    closedir($dh);
    @rmdir($dir);
  }
  
  static function mkdir_recursive($dir) {
    $dirs = explode('/', $dir);
    $newdir = '';
    for($i=1; $i<count($dirs); $i++)
      {
        $newdir = $newdir . '/' . $dirs[$i];
        if(!file_exists($newdir))
          {
            log::info('emboss',"mkdir $newdir");
            @mkdir($newdir);
          }
      }
  }
  
  static function upload_new_overlay($file)
  {
    $tmp  = $file['tmp_name'];
    $name = $file['name'];
    
    switch($file['error']) {
    case UPLOAD_ERR_INI_SIZE:
      emboss::error(t("File size of $name exceeds maximum upload limit on server"));
      return;
      break;
    case UPLOAD_ERR_FORM_SIZE:
      emboss::error(t("File size of $name exceeds maximum upload limit set on page"));
      return;
      break;
    case UPLOAD_ERR_PARTIAL:
      emboss::error(t("File $name only partially uploaded"));
      return;
      break;
    case UPLOAD_ERR_NO_FILE:
      emboss::error(t("File $name failed to upload"));
      return;
      break;
    }
    
    $image_info = getimagesize($tmp);
    $types[] = IMAGETYPE_GIF;
    $types[] = IMAGETYPE_JPEG;
    $types[] = IMAGETYPE_PNG;
    if(! $image_info || ! in_array($image_info[2],$types) ) {
      emboss::error(t('Overlay image must be GIF, JPG, or PNG'));
      @unlink($tmp);
      return;
    }
    
    $n = db::build()
      ->select('id')
      ->from('emboss_overlays')
      ->where('name','=',$name)
      ->execute()
      ->count();
    
    if($n>0) {
      emboss::error(t("Overlay named $name already exists."));
      @unlink($tmp);
      return;
    }
    
    $width = $image_info[0];
    $height = $image_info[1];
    $where1 = array('width','=',$width);
    $where2 = array('height','=',$height);
    $where  = array($where1,$where2);
    
    $n = db::build()
      ->select('id')
      ->from('emboss_overlays')
      ->where('width','=',$width)
      ->where('height','=',$height)
      ->execute()
      ->count();
    
    if($n>0) {
      emboss::error(t("Overlay with dimensions $width x $height already exists."));
      @unlink($tmp);
      return;
    }
    
    @rename($tmp, VARPATH . 'modules/emboss/' . $name);
    
    $overlay = ORM::factory('emboss_overlay');
    $overlay->name = $name;
    $overlay->width = $width;
    $overlay->height = $height;
    $overlay->active = 1;
    $overlay->save();
    
    emboss::success('Succesfully uploaded overlay ' . $file['name']);
    emboss::evaluate_overlays();
  }
  
  static function _delete_overlay($overlay)
  {
    $query = db::build()
      ->select('id')
      ->from('emboss_overlays')
      ->where('name','=',$overlay)
      ->execute();
    $n = $query->count();
    
    $qual = '(database table: g3_emboss_overlay)';
    if($n<1) {
      message::error("Internal error... $overlay missing $qual");
      return;
    }
    if($n>1) {
      message::error("Internal error... $overlay has multiple entries $qual");
      return;
    }

    $overlay_id = $query[0]->id;
    
    $q = db::build()
      ->from('emboss_overlays')
      ->where('id','=',$overlay_id)
      ->delete()
      ->execute();
    
    @unlink(VARPATH . 'modules/emboss/' . $overlay);

    $query = db::build()
      ->update('emboss_mappings')
      ->where('cur_overlay_id','=',$overlay_id)
      ->set('cur_overlay_id',-1)
      ->execute();

    $query = db::build()
      ->update('emboss_mappings')
      ->where('best_overlay_id','=',$overlay_id)
      ->set('best_overlay_id',-1)
      ->execute();

    emboss::success("Succesfully deleted $overlay");
    emboss::evaluate_overlays();
  }

  public function usage_count($overlay_id)
  {
    $n = db::build()
      ->select()
      ->from('emboss_mappings')
      ->where('best_overlay_id','=',$overlay_id)
      ->execute()
      ->count();
    return ($n>0 ? $n : '');
  }

  static function update_overlay_options($post)
  {
    $options = array('method','size','gravity','transparency');
    foreach ($options as $option) {
      module::set_var('emboss',$option,$post["$option"]);
    }

    db::build()->update('emboss_overlays')->set('active',0)->execute();
    $activeOverlays = $post['active_overlays'];
    if(is_array($activeOverlays)) {
      foreach ($activeOverlays as $overlay) {
        $q = ORM::factory('emboss_overlay')->where('name','=',$overlay)->find();
        $q->active=1;
        $q->save();
      }
    }
  }
  
  static function evaluate_overlays()
  {
    $overlays = ORM::factory('emboss_overlay')->where('active','=',1)->find_all();
    $images = ORM::factory('item')->where('type','=','photo')->find_all();

    $n_new    = 0;
    $n_update = 0;
    $n_none   = 0;

    $has_changes=0;
    foreach ($images as $image) {
      $overlay_id = emboss::determine_best_overlay($image,$overlays);
      if($overlay_id < 0) {
        $n_none++;
      }

      $q = ORM::factory('emboss_mapping')->where('image_id','=',$image->id)->find();
      if( ! $q->loaded() ) {
        if($overlay_id>0) {
          $n_new++;
        }
        $q->image_id = $image->id;
        $q->best_overlay_id = $overlay_id;
        $q->cur_overlay_id = -1;
        $q->cur_gravity = 'unset';
        $q->cur_transparency = -1;
        $q->save();
      } else if($q->best_overlay_id != $overlay_id) {
        if($overlay_id>0) {
          $n_update++;
        }
        $q->best_overlay_id = $overlay_id;
        $q->save();
      }
    }

    if($n_none) {
      emboss::info('Cannot find an overlay for '.$n_none . t2(' image',' images'));
    }
    if($n_new) {
      emboss::info($n_new . t2(' image needs',' images need',$n_new) .
                    ' now have an overlay available');
    }
    if($n_update) {
      emboss::info(t2('This changes the overlay for 1 image',
                      "This changes the overlay for $n_update images",
                      $n_update));
    }

    if($n_none || $n_new || $n_update) {
      
    } else{
      message::info('All photos are being embossed with the correct overlay');
    }
  }

  static function determine_best_overlay($image,$overlays=NULL)
  {
    if(!$overlays) {
      $overlays = ORM::factory('emboss_overlay')->where('active','=',1)->find_all();
    }

    $method = module::get_var('emboss','method');
    $size   = 0.01 * module::get_var('emboss','size');

    $W = $size * $image->width;
    $H = $size * $image->height;

    $bestID = -1;
    $bestScore=0;
    foreach ($overlays as $overlay) {
      $score = $overlay->score($W,$H,$method);
      if ( $score>0 && $score>$bestScore ) {
        $bestScore = $score;
        $bestID = $overlay->id;
      }
    }
    return $bestID;
  }

  static function check_for_dirty()
  {
    $q = emboss::find_dirty();
    $n = $q->count();
    if($n>0) {
      $url = url::site('admin/maintenance/start/emboss_task::update_overlays?csrf=__CSRF__');
      site_status::warning(
        t2("One of your photos needs to be (re)embossed. <a %attrs>Click here to fix it</a>",
           "%count of your photos need to be (re)embossed. <a %attrs>Click here to fix them</a>",
           $n,
           array('attrs' => html::mark_clean(sprintf('href="%s" class="g-dialog-link"',$url)))),
        'emboss_dirty');
    } else {
      site_status::clear('emboss_dirty');
    }
  }

  public function find_dirty()
  {
    $gravity      = module::get_var('emboss','gravity');
    $transparency = module::get_var('emboss','transparency');

    $q = db::build()
      ->select()
      ->from('emboss_mappings')
      ->or_where('cur_overlay_id','!=',db::expr('best_overlay_id'))
      ->or_where('cur_gravity','!=',$gravity)
      ->or_where('cur_transparency','!=',$transparency)
      ->execute();

    return $q;
  }

  public function uninstall()
  {
    $items = ORM::factory('item')->find_all();
    foreach($items as $item) {
      $path = $item->file_path() . $name;
      $orig = str_replace(VARPATH.'albums/',VARPATH.'originals/',$path);
      if(file_exists($orig)) {
        @unlink($path);
        @rename($orig,$path);
      }
    }
    graphics::mark_dirty(1,1);
    
    Database::instance()->query('DROP TABLE {emboss_overlays}');
    Database::instance()->query('DROP TABLE {emboss_mappings}');
    Database::instancs()->query("delete from {modules} where name='emboss'");

    log::info('emboss','module uninstalled (database dropped/overlays removed)');
  }
    

}
