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

class g1_import_Core {
  public static $init = false;
  public static $map = array();

  public static $gallery_dir = null;
  public static $album_dir = null;
  public static $gallery_url = null;
  public static $album_url = null;
  public static $resize_size = null;
  public static $thumb_size = null;
  public static $tree = array();
  public static $version = null;
  public static $warn_utf8 = array();

  public static $queued_items = array();
  public static $queued_comments = array();
  public static $queued_highlights = array();
  public static $albums_flat = array();
  public static $albums_hidden = array();

  private static $current_g1_item = null;

  static function is_configured() {
    return module::get_var('g1_import', 'albums_path');
  }

  static function is_initialized() {
    return g1_import::$init == 'ok';
  }

  static function init() {
    if (g1_import::$init) {
      return;
    }

    $albums_path = module::get_var('g1_import', 'albums_path');
    if (empty($albums_path)) {
      throw new Exception('@todo G1_IMPORT_NOT_CONFIGURED');
    }

    g1_import::$init = g1_import::check_config($albums_path);
  }

  static function is_valid_albums_path($albums_path) {
    return g1_import::check_config($albums_path);
  }

  /**
   * Initialize the embedded Gallery 1 instance.  Call this before any other Gallery 1 calls.
   *
   * Return values:
   *  "ok"      - the Gallery 1 install is fine
   *  "missing" - the albums path does not exist
   *  "invalid" - the albums path is not a valid Gallery 1 code base
   *  "broken"  - the albums path is correct, but the Gallery 1 install is broken
   */
  static function check_config($albums_path) {
    self::$album_dir = '';

    if (!is_file($albums_path)) {
      return 'missing';
    }

    $config_path = dirname($albums_path).DIRECTORY_SEPARATOR.'config.php';
    if (!is_file($config_path)) {
      return 'invalid';
    }

    $albumDir = '';
    $albumUrl = '';
    $galleryUrl = '';
    $thumbSize = '';
    $resizeSize = '';
    foreach(file($config_path) as $line) {
      //look for a line like: $gallery->app->albumDir = "/home/t-horner.com/www/albums";
      if(preg_match('/\$gallery *-> *app *-> *albumDir *= *["\']([^"\']*)["\']/i',$line,$result)==1 && count($result)==2 && $result[1]!='')
        $albumDir = $result[1];
      if(preg_match('/\$gallery *-> *app *-> *albumDirURL *= *["\']([^"\']*)["\']/i',$line,$result)==1 && count($result)==2 && $result[1]!='')
        $albumUrl = $result[1];
      if(preg_match('/\$gallery *-> *app *-> *photoAlbumURL *= *["\']([^"\']*)["\']/i',$line,$result)==1 && count($result)==2 && $result[1]!='')
        $galleryUrl = $result[1];
      if(preg_match('/\$gallery *-> *app *-> *default\[ *["\']thumb_size["\'] *\] *= *["\']([^"\']*)["\']/i',$line,$result)==1 && count($result)==2 && $result[1]!='')
        $thumbSize = $result[1];
      if(preg_match('/\$gallery *-> *app *-> *default\[ *["\']resize_size["\'] *\] *= *["\']([^"\']*)["\']/i',$line,$result)==1 && count($result)==2 && $result[1]!='')
        $resizeSize = $result[1];
    }

    if ($albumDir=='' || !is_dir($albumDir) || $albumUrl=='' || $galleryUrl=='') {
      return 'broken';
    }

    self::$album_dir = $albumDir;
    self::$album_url = $albumUrl;
    self::$gallery_url = $galleryUrl;
    self::$thumb_size = $thumbSize;
    self::$resize_size = $resizeSize;
    self::$gallery_dir = dirname($albums_path);


    $version = '';
    $version_path = dirname($albums_path).DIRECTORY_SEPARATOR.'Version.php';
    if (!is_file($version_path)) {
      $version_path = dirname($albums_path).DIRECTORY_SEPARATOR.'version.php';
    }
    if (is_file($version_path)) {
      foreach(file($version_path) as $line) {
        //look for a line like: $gallery->version = '1.5.10';
        if(preg_match('/\$gallery *-> *version *= *["\']([^"\']*)["\']/i',$line,$result)==1 && count($result)==2 && $result[1]!='')
          $version = $result[1];
      }
    }
    else {
      $version = '1.x';
    }
    self::$version = $version;


    return 'ok';
  }

  /**
   * Return the version of Gallery 1 (eg "1.5.10")
   */
  static function version() {
    if(!self::is_initialized())
      return '1';
    return self::$version;
  }

  static function recursiveCountGallery($albumDir, &$array, $level) {
    $countAlbum = 0;
    
    foreach($array as $key => &$valdummy) {
      $converted = utf8_encode($key);
      if( $converted != $key )
        self::$warn_utf8[] = $converted;
    }
    
    foreach($array as $key => $value) {
      if($key!='') {
        $countAlbum++;
        self::$albums_flat[] = $level.':'.$key;

        require_once('Gallery1DataParser.php');
        list($result, $items) = Gallery1DataParser::getPhotos($albumDir.$key.DIRECTORY_SEPARATOR);

        if($result==null)
        foreach($items as $object) {
          
          if(isset($object->image) && is_a($object->image, 'G1Img')) {
            
            $item = $key.DIRECTORY_SEPARATOR.$object->image->name;
            self::$queued_items[] = $item;
            if(isset($object->comments) && is_array($object->comments) && count($object->comments)) {

              $comments = array();
              foreach ($object->comments as $comment) {
                 if (is_a($comment, 'Comment')) {
                   $comments[] = array( 'commentText' => $comment->commentText
                                       ,'name' => $comment->name
                                       ,'UID' => $comment->UID
                                       ,'datePosted' => $comment->datePosted
                                       ,'IPNumber' => $comment->IPNumber
                                      );
                }
              }
              self::$queued_comments[] = array( $item => $comments );
            }
            
            if(isset($object->highlight) && $object->highlight==1 && isset($object->highlightImage) && is_a($object->highlightImage, 'G1Img')) {
              self::$queued_highlights[] = $level.':'.$key.DIRECTORY_SEPARATOR.$object->highlightImage->name;
            }
          }
          else if(isset($object->isAlbumName) && $object->isAlbumName!='') {

            if(isset($object->highlight) && $object->highlight==1 && isset($object->highlightImage) && is_a($object->highlightImage, 'G1Img')) {
              self::$queued_highlights[] = $level.':'.$key.DIRECTORY_SEPARATOR.$object->highlightImage->name;
            }
            if(isset($object->hidden) && $object->hidden==1) {
              self::$albums_hidden[$object->isAlbumName] = 1;
            }
          }
          else {
            g1_import::log('Invalid object found: '.print_r($object, true));
          }
          
        }
      }
      if(is_array($value) && count($value)>0) {
        $countAlbum += g1_import::recursiveCountGallery($albumDir, $value, $level+1);
      }
    }
    return $countAlbum;
  }

  /**
   * Return a set of statistics about the number of users, groups, albums, photos, movies and
   * comments available for import from the Gallery 1 instance.
   */
  static function g1_stats() {
    $stats['users'] = 0;
    $stats['groups'] = 0;
    $stats['albums'] = 0;
    $stats['photos'] = 0;
    $stats['movies'] = 0;
    $stats['comments'] = 0;
    $stats['tags'] = 0;

    $albumDir = self::$album_dir;
    if(substr($albumDir,-1)!=DIRECTORY_SEPARATOR) $albumDir.=DIRECTORY_SEPARATOR;

    require_once('Gallery1DataParser.php');
    if(Gallery1DataParser::isValidAlbumsPath($albumDir)) {
      if(count(self::$tree)==0) {
        list($result, $tree) = Gallery1DataParser::getAlbumHierarchy($albumDir);
        if($result==null) self::$tree = $tree;
      }

      list($result, $uids) = Gallery1DataParser::getUserUids($albumDir);
      if($result==null) $stats['users'] = count($uids);
      
      self::$queued_items = array();
      self::$queued_comments = array();
      self::$queued_highlights = array();
      self::$albums_flat = array();
      self::$albums_hidden = array();
      self::$warn_utf8 = array();
      if(count(self::$tree)) $stats['albums'] = 1 /* <= THE ROOT ALBUM!!!*/ + g1_import::recursiveCountGallery($albumDir, self::$tree, 0);
      
      $stats['photos'] = count(self::$queued_items);
      foreach(self::$queued_comments as $element) {
        foreach($element as $item => $comments) {
          $stats['comments'] += count($comments);
        }
      }
      arsort(self::$queued_highlights);
      $stats['highlights'] = count(self::$queued_highlights);
      
      arsort(self::$albums_flat);
      foreach(self::$albums_flat as $key => $value) {
        $pos = strpos($value, ':');
        if($pos!==false) self::$albums_flat[$key] = substr($value, $pos+1);
      }
    }

    return $stats;
  }

  /**
   * Return a set of statistics about the number of users, groups, albums, photos, movies and
   * comments already imported into the Gallery 3 instance.
   */
  static function g3_stats() {
    $g3_stats = array(
      'album' => 0, 'comment' => 0, 'item' => 0, 'user' => 0, 'group' => 0, 'tag' => 0);
    foreach (db::build()
             ->select('resource_type')
             ->select(array('C' => 'COUNT("*")'))
             ->from('g1_maps')
             ->where('resource_type', 'IN', array('album', 'comment', 'item', 'user', 'group'))
             ->group_by('resource_type')
             ->execute() as $row) {
      $g3_stats[$row->resource_type] = $row->C;
    }
    return $g3_stats;
  }

  /**
   * Import a single album.
   */
  static function import_album(&$queue) {
    $messages = array();

    // The queue is a set of nested associative arrays where the key is the album id and the
    // value is an array of similar arrays.  We'll do a breadth first tree traversal using the
    // queue to keep our state.  Doing it breadth first means that the parent will be created by
    // the time we get to the child.

    // Dequeue the current album and enqueue its children
    list($album, $tree) = each($queue);
    unset($queue[$album]);
    g1_import::debug( t('Dequeued album %album.', array('album' => $album)) );

    foreach($tree as $key => $value) {
      $queue[$album.'/'.$key] = $value;
      g1_import::debug( t('Enqueued album %album.', array('album' => $album.'/'.$key)) );
    }

    // Special handling for the root album
    if ($album == '') {
      if (!self::map('', '', 'album')) {
        $album = item::root();
        self::set_map($album->id, '', '', 'album');
      }
      return $messages;
    }

    // Album names come in as /Folder1/Folder2/FolderX
    $pos = strrpos($album, '/');
    if($pos===false) {
      return $messages;
    }

    // Get FolderX into g1_album
    $parent = substr($album,0,$pos);
    $g1_album = substr($album,$pos+1);

    // Reduce parent to Folder2
    $pos = strrpos($parent, '/');
    if($pos!==false) {
      $parent = substr($parent,$pos+1);
    }

    // Skip already-existing albums
    if (self::map($g1_album, '', 'album')) {
      $messages[] = t('Skipping already existing album %album.', array('album' => $parent.'/'.$g1_album));
      return $messages;
    }

    $album_id = self::map($parent, '', 'album');
    if (!$album_id) {
      $messages[] = t('Album %name not found', array('name' => $parent));
      return $messages;
    }

    g1_import::debug( t('Now importing album %album.', array('album' => $parent.'/'.$g1_album)) );


    $albumDir = self::$album_dir;
    if(substr($albumDir,-1)!=DIRECTORY_SEPARATOR) $albumDir.=DIRECTORY_SEPARATOR;
    $importDir = $albumDir.$g1_album.DIRECTORY_SEPARATOR;


    $parent = ORM::factory('item', $album_id);

    $album = ORM::factory('item');
    $album->type = 'album';
    $album->parent_id = $album_id;
    g1_import::set_album_values($album, $g1_album);

    try {
      $album->validate();
    } catch (ORM_Validation_Exception $e) {
      throw new G1_Import_Exception(
          t('Failed to validate Gallery 1 album with name %name.',
            array('name' => $g1_album)),
          $e);
    }

    try {
      $album->save();
      self::set_map($album->id, $g1_album, '', 'album');
    } catch (Exception $e) {
      throw new G1_Import_Exception(
          t('Failed to import Gallery 1 album with name %name.',
            array('name' => $g1_album)),
          $e);
    }

    try {
      require_once('Gallery1DataParser.php');
      list($result, $items) = Gallery1DataParser::getPhotos($importDir);
      if($result==null)
      foreach($items as $object) {
        if(isset($object->highlight) && $object->highlight==1 && isset($object->highlightImage) && is_a($object->highlightImage, 'G1Img')) {
          $g1_path = $importDir.$object->highlightImage->name.'.'.$object->highlightImage->type;
          if (is_file($g1_path) && @copy($g1_path, $album->thumb_path())) {
            $album->thumb_height = $object->highlightImage->height;
            $album->thumb_width = $object->highlightImage->width;
            $album->thumb_dirty = false;
            $album->save();
          }
        }
      }
    } catch (Exception $e) {
      throw new G1_Import_Exception(
          t('Failed to copy thumb for album %name.',
            array('name' => $g1_album)),
          $e);
    }

    try {
      if(isset(self::$albums_hidden[$g1_album])) {
        access::deny(identity::everybody(), 'view', $album);
      }
    } catch (Exception $e) {
      throw new G1_Import_Exception(
          t('Failed to set access permission for hidden album %name.',
            array('name' => $g1_album)),
          $e);
    }

    return $messages;
  }

  /**
   * Transfer over all the values from a G1 album to a G3 album.
   */
  static function set_album_values($album, $g1_album) {
    $albumDir = self::$album_dir;
    if(substr($albumDir,-1)!=DIRECTORY_SEPARATOR) $albumDir.=DIRECTORY_SEPARATOR;
    $albumDir .= $g1_album;
    if(substr($albumDir,-1)!=DIRECTORY_SEPARATOR) $albumDir.=DIRECTORY_SEPARATOR;

    require_once('Gallery1DataParser.php');
    list($result, $fields) = Gallery1DataParser::loadAlbumFields($albumDir);

    $album->name = $fields['name'];
    $album->slug = item::convert_filename_to_slug($fields['name']); // <= verification fails if this property has not been set!!!
    $album->title = utf8_encode(self::_decode_html_special_chars(trim($fields['title'])));
    $album->title or $album->title = $album->name;
    $album->description = utf8_encode(self::_decode_html_special_chars(trim($fields['description'])));
    //$album->owner_id = self::map($g1_album->getOwnerId());

    if(strlen($album->title)>255) {
        if(strlen($album->description)==0) {
            $album->description = $album->title;
        }
        $album->title = substr($album->title, 0, 252).'...';
    }

    try {
      $album->view_count = (int) $fields['clicks'];
    } catch (Exception $e) {
      // @todo log
      $album->view_count = 0;
    }
    $album->created = $fields['clicks_date'];
    $album->sort_column = 'weight'; //G1 was always sorted manually
    $album->sort_order = 'ASC';
  }

  /**
   * Set the highlight properly for a single album
   */
  static function set_album_highlight(&$queue) {
    $messages = array();
    if(count($queue)==0) {
      $messages[] = t('Empty highlights queue');
      return $messages;
    }

    $item = array_shift($queue);
    if (substr($item, -10) == '.highlight') {
      $item = substr($item, 0, strlen($item)-10);
    }
    g1_import::debug( t('Now importing highlight %item', array('item' => $item)) );

    // Item names come in as Level:FolderX/ItemX
    $pos = strpos($item, ':');
    if($pos===false) {
      $messages[] = t('Invalid item %item', array('item' => $item));
      return $messages;
    }
    $item = substr($item, $pos+1);


    // Item names come in as FolderX/ItemX
    $pos = strrpos($item, '/');
    if($pos===false) {
      $messages[] = t('Invalid item %item', array('item' => $item));
      return $messages;
    }

    // Get ItemX into g1_item
    $g1_item = substr($item,$pos+1);
    // Get FolderX into g1_item
    $g1_album = substr($item,0,$pos);


    if (self::map($g1_album, '', 'highlight')) {
      return $messages;
    }

    $album_id = self::map($g1_album, '', 'album');
    if (!$album_id) {
      $messages[] = t('Album %name not found', array('name' => $g1_album));
      return $messages;
    }

    $item_id = self::map($g1_album, $g1_item, 'item');
    if (!$item_id) {
      $item_id = self::map($g1_item, '', 'album');
    }
    if (!$item_id) {
      $messages[] = t('Item/Album %name not found', array('name' => $item));
      return $messages;
    }

    $album = ORM::factory('item', $album_id);
    $album->album_cover_item_id = $item_id;
    $album->thumb_dirty = 1;
    try {
      $album->save();
      graphics::generate($album);
      g1_import::debug( t('Added highlight %item to %album', array('item' => $item, 'album' => $album->name)) );
    } catch (Exception $e) {
      $messages[] = (string) new G1_Import_Exception(
          t("Failed to generate an album highlight for album '%name'.",
            array('name' => $album->name)),
          $e);
      return $messages;
    }

    $album_id = self::map($g1_album, '', 'album');
    self::set_map($album_id, $g1_album, '', 'highlight');

    g1_import::debug( t('Added highlight %item to %album', array('item' => $item, 'album' => $album->name)) );
    return $messages;
  }

  static function hotfix_all() {
    $messages = array();
    $messages[] = t('Running Hotfix');
    
    /* ON THE LAST RUN WE NEED TO RE-FIX ALL DAMAGED ALBUM THUMBS! */

    $albumDir = self::$album_dir;
    if(substr($albumDir,-1)!=DIRECTORY_SEPARATOR) $albumDir.=DIRECTORY_SEPARATOR;
    
    foreach(self::$albums_flat as $g1_album) {
      $album_id = self::map($g1_album, '', 'album');
      if (!$album_id) {
        $messages[] = t('Album %name not found', array('name' => $g1_album));
        continue;
      }

      $album = ORM::factory('item', $album_id);
      $importDir = $albumDir.$g1_album.DIRECTORY_SEPARATOR;

      try {
        require_once('Gallery1DataParser.php');
        list($result, $items) = Gallery1DataParser::getPhotos($importDir);
        if($result==null)
        foreach($items as $object) {
          if(isset($object->highlight) && $object->highlight==1 && isset($object->highlightImage) && is_a($object->highlightImage, 'G1Img')) {
            $g1_path = $importDir.$object->highlightImage->name.'.'.$object->highlightImage->type;
            if (is_file($g1_path) && @copy($g1_path, $album->thumb_path())) {
              $album->thumb_height = $object->highlightImage->height;
              $album->thumb_width = $object->highlightImage->width;
              $album->thumb_dirty = false;
              $album->save();
            }
          }
        }
      } catch (Exception $e) {
        $messages[] = (string) new G1_Import_Exception(
            t('Failed to copy thumb for album %name.',
              array('name' => $g1_album)),
            $e);
      }
    }
    
    /* ON THE LAST RUN WE NEED TO RE-FIX ALL ALBUM PERMISSIONS */

    foreach(self::$albums_hidden as $g1_album => $dummy) {
      try {
        $album_id = self::map($g1_album, '', 'album');
        $album = ORM::factory('item', $album_id);
        access::deny(identity::everybody(), 'view', $album);
        $messages[] = t('Denying access to %album', array('album' => $g1_album));
      } catch (Exception $e) {
        $messages[] = (string) new G1_Import_Exception(
            t('Failed to set access permission for hidden album %name.',
              array('name' => $g1_album)),
            $e);
      }
    }
    
    return $messages;
  }

  /**
   * Import a single photo or movie.
   */
  static function import_item(&$queue) {
    $messages = array();
    if(count($queue)==0) {
      $messages[] = t('Empty item queue');
      return $messages;
    }

    $item_id = array_shift($queue);
    g1_import::debug( t('Now importing item %item', array('item' => $item_id)) );

    // Item names come in as FolderX/ItemX
    $pos = strrpos($item_id, '/');
    if($pos===false) {
      return $messages;
    }

    // Get ItemX into g1_item
    $g1_item = substr($item_id,$pos+1);
    // Get FolderX into g1_item
    $g1_album = substr($item_id,0,$pos);


    if (self::map($g1_album, $g1_item, 'item')) {
      return $messages;
    }

    $album_id = self::map($g1_album, '', 'album');
    if (!$album_id) {
      $messages[] = t('Album %name not found', array('name' => $g1_album));
      return $messages;
    }


    $album_item = null;

    $albumDir = self::$album_dir;
    if(substr($albumDir,-1)!=DIRECTORY_SEPARATOR) $albumDir.=DIRECTORY_SEPARATOR;

    require_once('Gallery1DataParser.php');
    list($result, $items) = Gallery1DataParser::getPhotos($albumDir.$g1_album.DIRECTORY_SEPARATOR);
    if($result==null)
    foreach($items as $object) {
      if(isset($object->image) && is_a($object->image, 'G1Img') && isset($object->image->name) && ($object->image->name==$g1_item)) {
        $album_item = $object;
      }
    }

    if ($album_item==null) {
      $messages[] = t('Failed to import Gallery 1 item: %item', array('item' => $item_id));
      return $messages;
    }

    $corrupt = 0;
    self::$current_g1_item = array( $item_id => $album_item );
    $g1_path = $albumDir.$g1_album.DIRECTORY_SEPARATOR.$album_item->image->name.'.'.$album_item->image->type;

    $parent = ORM::factory('item', $album_id);

    switch($album_item->image->type) {
      case 'jpg':
      case 'jpeg':
      case 'gif':
      case 'png':
        $g1_type = 'GalleryPhotoItem'; break;
      case 'wmv':
      case '3gp':
      case 'avi':
      case 'mp4':
      case 'mov':
      case 'flv':
        $g1_type = 'GalleryMovieItem'; break;
      default:
        $g1_type = 'GalleryPhotoItem'; break;
    }

    if (!file_exists($g1_path)) {
      // If the Gallery 1 source image isn't available, this operation is going to fail.  That can
      // happen in cases where there's corruption in the source Gallery 1.  In that case, fall
      // back on using a broken image.  It's important that we import *something* otherwise
      // anything that refers to this item in Gallery 1 will have a dangling pointer in Gallery 3
      //
      // Note that this will change movies to be photos, if there's a broken movie.  Hopefully
      // this case is rare enough that we don't need to take any heroic action here.
      g1_import::log(
        t('%path missing in import; replacing it with a placeholder', array('path' => $g1_path)));
      $g1_path = MODPATH . 'g1_import/data/broken-image.gif';
      $g1_type = 'GalleryPhotoItem';
      $corrupt = 1;
    }

    $item = null;

    switch ($g1_type) {
    case 'GalleryPhotoItem':

      if(function_exists('mime_content_type'))
        $type = mime_content_type($g1_path);
      else
        $type = self::get_mime_type($g1_path);
      
      if ($type!='' && !in_array($type, array('image/jpeg', 'image/gif', 'image/png'))) {
        Kohana_Log::add('alert', "$g1_path is an unsupported image type $type; using a placeholder gif");
        $messages[] = t("'%path' is an unsupported image type '%type', using a placeholder",
                        array('path' => $g1_path, 'type' => $type));
        $g1_path = MODPATH . 'g1_import/data/broken-image.gif';
        $corrupt = 1;
      }

      try {
        $item = ORM::factory('item');
        $item->type = 'photo';
        $item->parent_id = $album_id;
        $item->set_data_file($g1_path);
        $item->name = $g1_item.'.'.$album_item->image->type;
        $item->slug = item::convert_filename_to_slug($g1_item);
        $item->mime_type = $type;
        $item->title = utf8_encode(self::_decode_html_special_chars(trim($album_item->caption)));
        $item->title or $item->title = ' '; //don't use $item->name as this clutters up the UI
        if(isset($album_item->description) && $album_item->description!='')
          $item->description = utf8_encode(self::_decode_html_special_chars(trim($album_item->description)));
        //$item->owner_id = self::map($g1_item->getOwnerId());

        try {
          $item->view_count = (int) $album_item->clicks;
        } catch (Exception $e) {
          $item->view_count = 1;
        }

        if(strlen($item->title)>255) {
            if(strlen($item->description)==0) {
                $item->description = $item->title;
            }
            $item->title = substr($item->title, 0, 252).'...';
        }

      } catch (Exception $e) {
        $exception_info = (string) new G1_Import_Exception(
            t("Corrupt image '%path'", array('path' => $g1_path)),
            $e, $messages);
        Kohana_Log::add('alert', "Corrupt image $g1_path\n" . $exception_info);
        $messages[] = $exception_info;
        $corrupt = 1;
        $item = null;
        return $messages;
      }

      try {
        $item->validate();
      } catch (ORM_Validation_Exception $e) {
        $exception_info = (string) new G1_Import_Exception(
            t('Failed to validate Gallery 1 item %item.',
              array('item' => $item_id)),
            $e, $messages);
        Kohana_Log::add('alert', "Failed to validate Gallery 1 item $item_id.\n" . $exception_info);
        $messages[] = $exception_info;
        $corrupt = 1;
        $item = null;
        return $messages;
      }

      try {
        $item->save();
      } catch (Exception $e) {
        $exception_info = (string) new G1_Import_Exception(
            t('Failed to import Gallery 1 item %item.',
              array('item' => $item_id)),
            $e, $messages);
        Kohana_Log::add('alert', "Failed to import Gallery 1 item $item_id.\n" . $exception_info);
        $messages[] = $exception_info;
        $corrupt = 1;
        $item = null;
      }

      break;

    case 'GalleryMovieItem':
      // @todo we should transcode other types into FLV

      if(function_exists('mime_content_type'))
        $type = mime_content_type($g1_path);
      else
        $type = self::get_mime_type($g1_path);
        
      if ($type=='' || in_array($type, array('video/mp4', 'video/x-flv'))) {
        try {
          $item = ORM::factory('item');
          $item->type = 'movie';
          $item->parent_id = $album_id;
          $item->set_data_file($g1_path);
          $item->name = $g1_item.'.'.$album_item->image->type;
          $item->slug = item::convert_filename_to_slug($g1_item);
          $item->mime_type = $type;
          $item->title = utf8_encode(self::_decode_html_special_chars(trim($album_item->caption)));
          $item->title or $item->title = ' '; //$item->name;
          if(isset($album_item->description) && $album_item->description!='')
            $item->description = utf8_encode(self::_decode_html_special_chars(trim($album_item->description)));
          //$item->owner_id = self::map($g1_item->getOwnerId());
          try {
            $item->view_count = (int) $album_item->clicks;
          } catch (Exception $e) {
            $item->view_count = 1;
          }
        } catch (Exception $e) {
          $exception_info = (string) new G1_Import_Exception(
              t("Corrupt movie '%path'", array("path" => $g1_path)),
              $e, $messages);
          Kohana_Log::add('alert', "Corrupt movie $g1_path\n" . $exception_info);
          $messages[] = $exception_info;
          $corrupt = 1;
          $item = null;
          return $messages;
        }

        try {
          $item->validate();
        } catch (ORM_Validation_Exception $e) {
          $exception_info = (string) new G1_Import_Exception(
              t('Failed to validate Gallery 1 item %item.',
                array('item' => $item_id)),
              $e, $messages);
          Kohana_Log::add('alert', "Failed to validate Gallery 1 item $item_id.\n" . $exception_info);
          $messages[] = $exception_info;
          $corrupt = 1;
          $item = null;
          return $messages;
        }
  
        try {
          $item->save();
        } catch (Exception $e) {
          $exception_info = (string) new G1_Import_Exception(
              t('Failed to import Gallery 1 item %item.',
                array('item' => $item_id)),
              $e, $messages);
          Kohana_Log::add('alert', "Failed to import Gallery 1 item $item_id.\n" . $exception_info);
          $messages[] = $exception_info;
          $corrupt = 1;
          $item = null;
        }
      } else {
        Kohana_Log::add('alert', "$g1_path is an unsupported movie type $type");
        $messages[] = t("'%path' is an unsupported movie type '%type'", array('path' => $g1_path, 'type' => $type));
        $corrupt = 1;
      }

      break;

    default:
      // Ignore
      break;
    }

    if (isset($item)) {
      self::set_map($item->id, $g1_album, $g1_item, 'item');

      if(isset($album_item->keywords) && $album_item->keywords!='') {
        $keywords = utf8_encode(self::_decode_html_special_chars(trim($album_item->keywords)));
        if($keywords!='') {
          self::import_keywords_as_tags($keywords, $item);
        }
      }
    }

    if ($corrupt) {
      $title = utf8_encode(self::_decode_html_special_chars(trim($album_item->caption)));
      $title or $title = $g1_item;
      if (!empty($item)) {
        $messages[] =
          t('<a href="%g1_url">%title</a> from Gallery 1 could not be processed; (imported as <a href="%g3_url">%title</a>)',
            array('g1_url' => $gallery_url.'/'.$item_id,
                  'g3_url' => $item->url(),
                  'title' => $title));
      } else {
        $messages[] =
          t('<a href="%g1_url">%title</a> from Gallery 1 could not be processed',
            array('g1_url' => $gallery_url.'/'.$item_id, 'title' => $title));
      }
    }

    self::$current_g1_item = null;
    return $messages;
  }

  function get_mime_type($filename, $mimePath = '/etc') {
    $fileext = substr(strrchr($filename, '.'), 1);
    if (empty($fileext)) return (false);
    $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
    $lines = file("$mimePath/mime.types");
    foreach($lines as $line) {
      if (substr($line, 0, 1) == '#') continue; // skip comments
      $line = rtrim($line) . ' ';
      if (!preg_match($regex, $line, $matches)) continue; // no match to the extension
      return ($matches[1]);
    }
    return (false); // no match at all
  }

  /**
   * Import a single comment.
   */
  static function import_comment(&$queue) {
    $messages = array();
    if(count($queue)==0) {
      //this case happens if more than one comment is found on one or more items
      $messages[] = t('Empty comments queue');
      return $messages;
    }

    $element = array_shift($queue);

    list($item, $comments) = each($element);
    g1_import::debug( t('Now importing %$comments comment(s) for item %$item', array('album' => $item, 'comments' => count($comments))) );

    // Item names come in as FolderX/ItemX
    $pos = strrpos($item, '/');
    if($pos===false) {
      $messages[] = t('Invalid item %item', array('item' => $item));
      return $messages;
    }

    // Get ItemX into g1_item
    $g1_item = substr($item,$pos+1);
    // Get FolderX into g1_item
    $g1_album = substr($item,0,$pos);


    if (self::map($g1_album, $g1_item, 'comment')) {
      return $messages;
    }

    $item_id = self::map($g1_album, $g1_item, 'item');
    if (empty($item_id)) {
      $messages[] = t('Could not find item %item', array('item' => $item));
      return;
    }

    foreach ($comments as $g1comment) {

      // Just import the fields we know about.  Do this outside of the comment API for now so that
      // we don't trigger spam filtering events
      $comment = ORM::factory('comment');
      $comment->author_id = identity::guest()->id;
      $comment->guest_name = utf8_encode(self::_decode_html_special_chars(trim($g1comment['name'])));
      $comment->guest_name or $comment->guest_name = (string) t('Anonymous coward');
      $comment->guest_email = 'unknown@nobody.com';
      $comment->item_id = $item_id;
      $comment->text = utf8_encode(self::_decode_html_special_chars(trim($g1comment['commentText'])));
      $comment->state = 'published';
      $comment->server_http_host = utf8_encode(self::_decode_html_special_chars(trim($g1comment['IPNumber'])));

      try {
        $comment->save();
      } catch (Exception $e) {
        $messages[] = (string) new G1_Import_Exception(
            t('Failed to import comment for item: %item.',
              array('item' => $item)),
            $e);
        return $messages;
      }
  
      // Backdate the creation date.  We can't do this at creation time because
      // Comment_Model::save() will override it.
      db::update('comments')
        ->set('created', utf8_encode(self::_decode_html_special_chars(trim($g1comment['datePosted']))))
        ->set('updated', utf8_encode(self::_decode_html_special_chars(trim($g1comment['datePosted']))))
        ->where('id', '=', $comment->id)
        ->execute();
    }

    self::set_map($item_id, $g1_album, $g1_item, 'comment');
    return $messages;
  }

  /**
   * g1 encoded'&', '"', '<' and '>' as '&amp;', '&quot;', '&lt;' and '&gt;' respectively.
   * This function undoes that encoding.
   */
  private static function _decode_html_special_chars($value) {
    return str_replace(array('&amp;', '&quot;', '&lt;', '&gt;', '&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig;'),
                       array('&', '"', '<', '>', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'), $value);
  }

  static function import_keywords_as_tags($keywords, $item) {
    // FIXME check if this is true for G1 (copied from G2 import module):
    // Keywords in G1 are free form.  So we don't know what our user used as a separator.  Try to
    // be smart about it.  If we see a comma or a semicolon, expect the keywords to be separated
    // by that delimeter.  Otherwise, use space as the delimiter.
    if (strpos($keywords, ';')) {
      $delim = ';';
    } else if (strpos($keywords, ',')) {
      $delim = ',';
    } else {
      $delim = ' ';
    }

    foreach (preg_split("/$delim/", $keywords) as $keyword) {
      $keyword = trim($keyword);
      if ($keyword) {
        tag::add($item, $keyword);
      }
    }
  }

  /**
   * If the thumbnails and resizes created for the Gallery 1 photo match the dimensions of the
   * ones we expect to create for Gallery 3, then copy the files over instead of recreating them.
   */
  static function copy_matching_thumbnails_and_resizes($item) {

    // We only operate on items that are being imported
    if (empty(self::$current_g1_item)) {
      return;
    }

    // Precaution: if the Gallery 1 item was watermarked, or we have the Gallery 3 watermark module
    // active then we'd have to do something a lot more sophisticated here.  For now, just skip
    // this step in those cases.
    // @todo we should probably use an API here, eventually.
    if (module::is_active('watermark') && module::get_var('watermark', 'name')) {
      return;
    }

    // For now just do the copy for photos and movies.  Albums are tricky because we're may not
    // yet be setting their album cover properly.
    // @todo implement this for albums also
    if (!$item->is_movie() && !$item->is_photo()) {
      return;
    }

    list( $item_id, $album_item ) = each(self::$current_g1_item);
    
    // Item names come in as FolderX/ItemX
    $pos = strrpos($item_id, '/');
    if($pos===false) {
      return;
    }

    // Get ItemX into g1_item
    $g1_item = substr($item_id,$pos+1);
    // Get FolderX into g1_item
    $g1_album = substr($item_id,0,$pos);


    $target_thumb_size = module::get_var('gallery', 'thumb_size');
    $target_resize_size = module::get_var('gallery', 'resize_size');
    if ( isset($album_item->thumbnail) && is_a($album_item->thumbnail, 'G1Img') ) {

      if($item->thumb_dirty &&
         ($album_item->thumbnail->width == $target_thumb_size ||
          $album_item->thumbnail->height == $target_thumb_size)) {

        $g1_path = $albumDir.$g1_album.DIRECTORY_SEPARATOR.$album_item->thumbnail->name.'.'.$album_item->thumbnail->type;
        if (@copy($g1_path, $item->thumb_path())) {
          $item->thumb_height = $album_item->thumbnail->height;
          $item->thumb_width = $album_item->thumbnail->width;
          $item->thumb_dirty = false;
        }
        /**
         * No use to try to take over the highlight images here as they will be overwritten anyway.
         */
      }
    }

    try {
      $item->save();
    } catch (Exception $e) {
      return (string) new G1_Import_Exception(
          t("Failed to copy thumbnails and resizes for item '%name' (Gallery 1 id: %id)",
            array('name' => $item->name, 'id' => $item_id)),
          $e);
    }
  }

  /**
   * Figure out the most common resize and thumb sizes in Gallery 1 so that we can tell the admin
   * what theme settings to set to make the import go faster.  If we match up the sizes then we
   * can just copy over derivatives instead of running graphics toolkit operations.
   */
  static function common_sizes() {
    return array(  'resize' => array( 'size' => self::$resize_size, 'count' => 1 )
                  ,'thumb' => array( 'size' => self::$thumb_size, 'count' => 1 )
                  ,'total' => 1
                );
  }

  /**
   * Look in our map to find the corresponding Gallery 3 id for the given Gallery 1 id.
   */
  static function map($album, $item, $resource_type) {
    if (!array_key_exists($resource_type.':'.$album.'/'.$item, self::$map)) {
      $mapping = ORM::factory('g1_map')->where('album', '=', $album)->where('item', '=', $item)->where('resource_type', '=', $resource_type)->find();
      self::$map[$resource_type.':'.$album.'/'.$item] = $mapping->loaded() ? $mapping->id : null;
    }

    return self::$map[$resource_type.':'.$album.'/'.$item];
  }

  /**
   * Associate a Gallery 1 id with a Gallery 3 item id.
   */
  static function set_map($id, $album, $item, $resource_type) {
    $mapping = ORM::factory('g1_map');
    $mapping->id = $id;
    $mapping->album = $album;
    $mapping->item = $item;
    $mapping->resource_type = $resource_type;
    $mapping->save();
    self::$map[$resource_type.':'.$album.'/'.$item] = $id;
  }

  static function log($msg) {
    message::warning($msg);
    Kohana_Log::add('alert', $msg);
  }

  static function debug($msg) {
    Kohana_Log::add('debug', $msg);
  }
}
