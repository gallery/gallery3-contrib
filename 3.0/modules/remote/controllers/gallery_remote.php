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
class Gallery_Remote_Controller extends Controller {
  public function index() {

    $input = Input::instance();
    $reply = GalleryRemoteReply::factory(gallery_remote::GR_STAT_SUCCESS);
    
    if($this->_check_protocol($input, $reply)) {
      $reply->set('debug_gallery_version', gallery::version_string());
      $reply->set('debug_user', identity::active_user()->name);
      $reply->set('debug_user_type', 'Gallery_User');
      $reply->set('debug_user_already_logged_in', identity::active_user()->id != identity::guest()->id ? '1':'');
      $reply->set('server_version', '2.15');

      $cmd = trim($input->post('cmd'));
      if($cmd == 'login') {
        $this->_login($input, $reply);
      }
      else if( self::isloggedin() ) {
        switch($cmd) {
          case 'no-op':
            $reply->set('status_text', 'Noop command successful.');
            $reply->send();
            break;
          case 'fetch-albums':
          case 'fetch-albums-prune':
            $this->_fetch_albums_prune($input, $reply);
            break;
          case 'new-album':
            $this->_new_album($input, $reply);
            break;
          case 'album-properties':
            $this->_album_properties($input, $reply);
            break;
          case 'add-item':
            $this->_add_item($input, $reply);
            break;
          case 'move-album':
            $this->_move_album($input, $reply);
            break;
          case 'increment-view-count':
            $this->_increment_view_count($input, $reply);
            break;
          case 'image-properties':
            $this->_image_properties($input, $reply);
            break;
          case 'fetch-album-images':
            $this->_fetch_album_images($input, $reply);
            break;
          default:
            $reply->send(gallery_remote::UNKNOWN_CMD);
        }
      }
      else {
        $reply->send(gallery_remote::LOGIN_MISSING);
      }
    }
  }

  private function _check_protocol(&$input, &$reply) {
    $version = trim($input->post('protocol_version'));
    $reply->set('status_text', 'Minimum protocol version required: '.gallery_remote::GR_PROT_MAJ.'.'.gallery_remote::GR_PROT_MIN.' - your client\'s protocol version: '.$version);
    if($version=='') {
      $reply->send(gallery_remote::PROTO_VER_MISSING);
      return false;
    }
    else if(!is_numeric($version)) {
      $reply->send(gallery_remote::PROTO_MAJ_FMT_INVAL);
      return false;
    }
    else if($version<gallery_remote::GR_PROT_MAJ) {
      $reply->send(gallery_remote::PROTO_MAJ_VER_INVAL);
      return false;
    }
    else if(strpos($version, '.')===false) {
      $reply->send(gallery_remote::PROTO_MAJ_FMT_INVAL);
      return false;
    }
    else {
      $ver = explode('.', $version);
      if($ver[0]==gallery_remote::GR_PROT_MAJ && $ver[1]<gallery_remote::GR_PROT_MIN) {
        $reply->send(gallery_remote::PROTO_MIN_VER_INVAL);
        return false;
      }
    }

    return true;
  }

  private static function isloggedin()
  {
    return identity::active_user()->id != identity::guest()->id;
  }

  private static function get_mime_type($filename, $mimePath = '/etc') {
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

  private static function decode($input) {
    return html_entity_decode(trim($input), ENT_COMPAT, 'UTF-8');
  }

  private function _login(&$input, &$reply) {
    $uname = trim($input->post('uname'));
    if (empty($uname)) {
      $reply->send(gallery_remote::LOGIN_MISSING);
    } else {
      $user = user::lookup_by_name($uname);
      $password = trim($input->post('password'));
      if ($user && user::is_correct_password($user, $password)) {
        auth::login($user);
        Session::instance()->regenerate();

        $reply->set('debug_user', $user->name);
        $reply->set('status_text', 'Login successful.');
        $reply->send();
        
      } else {
        $reply->send(gallery_remote::PASSWD_WRONG);
      }
    }
  }

  private function _fetch_albums_prune(&$input, &$reply) {
    $root = item::root();
    $perms = trim($input->post('no_perms'));
    $use_permissions = ($perms != 'no');
    
    $thumb_size = module::get_var('gallery', 'thumb_size');
    $resize_size = module::get_var('gallery', 'resize_size');

    //* <FIXME duplication>
    $count = 1;
    $item = &$root;
    $reply->set('album.name.'.$count, $item->id);
    $reply->set('album.title.'.$count, $item->title);
    $reply->set('album.summary.'.$count, 'Gallery Remote Interface by Thomas E. Horner');
    $reply->set('album.parent.'.$count, '0');
    $reply->set('album.resize_size.'.$count, $resize_size);
    $reply->set('album.max_size.'.$count, '0');
    $reply->set('album.thumb_size.'.$count, $thumb_size);
    if($use_permissions) {
      $reply->set('album.perms.add.'.$count, access::can('add', $item) ? 'true':'false');
      $reply->set('album.perms.write.'.$count, access::can('add', $item) ? 'true':'false');
      $reply->set('album.perms.del_item.'.$count, access::can('edit', $item) ? 'true':'false');
      $reply->set('album.perms.del_alb.'.$count, access::can('edit', $item) ? 'true':'false');
      $reply->set('album.perms.create_sub.'.$count, access::can('add', $item) ? 'true':'false');
    }
    $reply->set('album.info.extrafields.'.$count, 'Summary');
    // </FIXME> */

    foreach( $root->descendants(null, null, array(array("type", "=", "album"))) as $item )
    {
      if(!$use_permissions || access::can('view', $item))
      {
        $count++;
      
        $reply->set('album.name.'.$count, $item->id);
        $reply->set('album.title.'.$count, $item->title);
        $reply->set('album.summary.'.$count, $item->description);
        $reply->set('album.parent.'.$count, $item->parent()->id == $root->id ? '0' : $item->parent()->id);
        $reply->set('album.resize_size.'.$count, $resize_size);
        $reply->set('album.max_size.'.$count, '0');
        $reply->set('album.thumb_size.'.$count, $thumb_size);
        if($use_permissions) {
          $reply->set('album.perms.add.'.$count, access::can('add', $item) ? 'true':'false');
          $reply->set('album.perms.write.'.$count, access::can('add', $item) ? 'true':'false');
          $reply->set('album.perms.del_item.'.$count, access::can('edit', $item) ? 'true':'false');
          $reply->set('album.perms.del_alb.'.$count, access::can('edit', $item) ? 'true':'false');
          $reply->set('album.perms.create_sub.'.$count, access::can('add', $item) ? 'true':'false');
        }
        $reply->set('album.info.extrafields.'.$count, 'Summary');
      }
    }
    $reply->set('album_count', $count);
    if($use_permissions) {
      $reply->set('can_create_root', access::can('add', $root) ? 'yes':'no');
    }
    $reply->set('status_text', 'Fetch albums successful.');
    $reply->send();
  }
  
  private function _new_album(&$input, &$reply) {
    $album = trim($input->post('set_albumName'));
    $name = $this->decode($input->post('newAlbumName'));
    $title = $this->decode($input->post('newAlbumTitle'));
    $desc = $this->decode($input->post('newAlbumDesc'));

    if($album=='0') $parent = item::root();
    else $parent = ORM::factory("item")->where("id", "=", $album)->find();

    if(isset($parent) && $parent->loaded() && $parent->id!='') {
      $album = ORM::factory('item');
      $album->type = 'album';
      $album->parent_id = $parent->id;

      $album->name = $name;
      $album->slug = item::convert_filename_to_slug($name); // <= verification fails if this property has not been set!!!
      $album->title = $title;
      $album->title or $album->title = $album->name;
      $album->description = $desc;
      $album->view_count = 0;
      $album->sort_column = 'weight';
      $album->sort_order = 'ASC';

      try {
        $album->validate();

        try {
          $album->save();

          $reply->set('album_name', $album->id);
          $reply->set('status_text', 'New album created successfuly.');
          $reply->send();

        } catch (Exception $e) {
          $reply->set('status_text', t('Failed to save album with name %name.', array('name' => $name)));
          $reply->send(gallery_remote::CREATE_ALBUM_FAILED);
        }

      } catch (ORM_Validation_Exception $e) {
        $reply->set('status_text', t('Failed to validate album with name %name.', array('name' => $name)));
        $reply->send(gallery_remote::CREATE_ALBUM_FAILED);
      }
    }
    else {
      $reply->set('status_text', t('Failed to load album with name %name.', array('name' => $album)));
      $reply->send(gallery_remote::CREATE_ALBUM_FAILED);
    }
  }
  
  private function _album_properties(&$input, &$reply) {
    $album = trim($input->post('set_albumName'));
    $resize_size = module::get_var('gallery', 'resize_size');

    if($album=='0') $parent = item::root();
    else $parent = ORM::factory("item")->where("id", "=", $album)->find();

    if(isset($parent) && $parent->loaded() && $parent->id!='') {      
      $reply->set('auto_resize', $resize_size); //resize size is the same for all g3 albums
      $reply->set('max_size', '0'); //not supported by g3
      $reply->set('add_to_beginning', 'no'); //g3 will add images to the end
      $reply->set('extrafields', 'Summary');
      $reply->set('title', $parent->title);
      $reply->set('status_text', 'Album properties queried successfuly.');
      $reply->send();
    }
    else {
      $reply->set('status_text', t('Failed to load album with name %name.', array('name' => $album)));
      $reply->send(gallery_remote::NO_VIEW_PERMISSION);
    }
  }
  
  private function _add_item(&$input, &$reply) {
    $album = trim($input->post('set_albumName'));
    $userfilename = $this->decode($input->post('userfile_name'));
    $title = $this->decode($input->post('caption'));
    $forcefilename = $this->decode($input->post('force_filename'));
    $autorotate = trim($input->post('auto_rotate'));

    if($album=='0') $parent = item::root();
    else $parent = ORM::factory("item")->where("id", "=", $album)->find();

    if(isset($parent) && $parent->loaded() && $parent->id!='') {

      if(function_exists('mime_content_type'))
        $type = mime_content_type($_FILES['userfile']['tmp_name']);
      else
        $type = self::get_mime_type($_FILES['userfile']['name']);
      
      
      /* <any ugly idea is welcome here> */
      if($type=='')
      {
        if(function_exists('getimagesize')) {
          $size = getimagesize($_FILES['userfile']['tmp_name']);
          $type = $size['mime'];
        }
        else if(function_exists('exif_imagetype') && function_exists('image_type_to_mime_type')) {
          $type = image_type_to_mime_type(exif_imagetype($_FILES['userfile']['tmp_name']));
        }
      }
      /* </any ugly idea is welcome here> */
      
            
      if ($type!='' && !in_array($type, array('image/jpeg', 'image/gif', 'image/png'))) {
        $reply->set('status_text', t("'%path' is an unsupported image type '%type'", array('path' => $_FILES['userfile']['tmp_name'], 'type' => $type)));
        $reply->send(gallery_remote::UPLOAD_PHOTO_FAIL);
        return;
      }

      if($forcefilename!='') $filename = $forcefilename;
      else if($userfilename!='') $filename = $userfilename;
      else $filename = $_FILES['userfile']['name'];

      $slug = $filename;
      $pos = strpos($slug, '.');
      if($pos!==false)
        $slug = substr($slug, 0, $pos);

      //*/ fix for a gallery remote bug...
      $filename = str_replace('.JPG.jpeg', '.jpeg', $filename);
      //*/

      //*/ suddenly gallery fails because the uploaded(!) file (of cause!) doesn't contain a file extension
      if(strpos($_FILES['userfile']['tmp_name'], '.')===false) {
        $moveto = $_FILES['userfile']['tmp_name'].'.'.substr($type, strpos($type, '/')+1);
        rename($_FILES['userfile']['tmp_name'], $moveto);
        $_FILES['userfile']['tmp_name'] = $moveto;
      }
      //*/

      try {
        $item = ORM::factory('item');
        $item->type = 'photo';
        $item->parent_id = $parent->id;
        $item->set_data_file($_FILES['userfile']['tmp_name']);
        $item->name = $filename;
        $item->slug = item::convert_filename_to_slug($slug);
        $item->mime_type = $type;
        $item->title = $title;
        $item->title or $item->title = ' '; //don't use $item->name as this clutters up the UI
        //$item->description = 
        $item->view_count = 0;

        try {
          $item->validate();

          try {
            $item->save();

            $reply->set('item_name', $item->id);
            $reply->set('status_text', 'New item created successfuly.');
            $reply->send();

          }
          catch (ORM_Validation_Exception $e) {
            $validation = $e->validation;
            //print_r($validation->errors()); exit;
            $reply->set('status_text', t('Failed to validate item %item: %errors', array('item' => $filename, 'errors' => str_replace("\n", ' ', print_r($validation->errors(),true))) ));
            $reply->send(gallery_remote::UPLOAD_PHOTO_FAIL); //FIXME gallery remote ignores this return value and continues to wait
          }
          catch (Exception $e) {
            $reply->set('status_text', t('Failed to add item %item.', array('item' => $filename)));
            $reply->send(gallery_remote::UPLOAD_PHOTO_FAIL); //FIXME gallery remote ignores this return value and continues to wait
          }

        } catch (ORM_Validation_Exception $e) {
          $validation = $e->validation;
          //print_r($validation->errors()); exit;
          $reply->set('status_text', t('Failed to validate item %item: %errors', array('item' => $filename, 'errors' => str_replace("\n", ' ', print_r($validation->errors(),true))) ));
          $reply->send(gallery_remote::UPLOAD_PHOTO_FAIL); //FIXME gallery remote ignores this return value and continues to wait
        }

      } catch (Exception $e) {
        $reply->set('status_text', t("Corrupt image '%path'", array('path' => $_FILES['userfile']['tmp_name'])));
        $reply->send(gallery_remote::UPLOAD_PHOTO_FAIL); //FIXME gallery remote ignores this return value and continues to wait
      }

    }
    else {
      $reply->set('status_text', t('Failed to load album with name %name.', array('name' => $album)));
      $reply->send(gallery_remote::UPLOAD_PHOTO_FAIL); //FIXME gallery remote ignores this return value and continues to wait
    }
  }

  private function _move_album(&$input, &$reply) {
    $name = trim($input->post('set_albumName'));
    $destination = trim($input->post('set_destalbumName'));

    $album = ORM::factory("item")->where("id", "=", $name)->find();

    if($destination=='0') $parent = item::root();
    else $parent = ORM::factory("item")->where("id", "=", $destination)->find();

    if(isset($parent) && $parent->loaded() && $parent->id!='' && isset($album) && $album->loaded() && $album->id!='') {
      
      $album->parent_id = $parent->id;
      try {
        $album->validate();

        try {
          $album->save();

          $reply->set('status_text', 'Album moved successfuly.');
          $reply->send();

        } catch (Exception $e) {
          $reply->set('status_text', t('Failed to save album with name %name.', array('name' => $name)));
          $reply->send(gallery_remote::MOVE_ALBUM_FAILED);
        }

      } catch (ORM_Validation_Exception $e) {
        $reply->set('status_text', t('Failed to validate album with name %name.', array('name' => $name)));
        $reply->send(gallery_remote::MOVE_ALBUM_FAILED);
      }
    }
    else {
      $reply->set('status_text', t('Failed to load album with name %album or destination with name %dest.', array('name' => $name, 'dest' => $destination)));
      $reply->send(gallery_remote::MOVE_ALBUM_FAILED);
    }
  }

  private function _increment_view_count(&$input, &$reply) {
    $name = trim($input->post('itemId'));

    if($name=='0') $item = item::root();
    else $item = ORM::factory("item")->where("id", "=", $name)->find();

    if(isset($item) && $item->loaded() && $item->id!='') {      

      $item->view_count = $item->view_count + 1;

      try {
        $item->validate();

        try {
          $item->save();

          $reply->set('item_name', $item->id);
          $reply->set('status_text', 'Item view count incremented successfuly.');
          $reply->send();

        } catch (Exception $e) {
          $reply->set('status_text', t('Failed to save item %item.', array('item' => $name)));
          $reply->send(gallery_remote::NO_WRITE_PERMISSION);
        }

      } catch (ORM_Validation_Exception $e) {
        $validation = $e->validation;
        //print_r($validation->errors()); exit;
        $reply->set('status_text', t('Failed to validate item %item.', array('item' => $name)).str_replace("\n", ' ', print_r($validation->errors(),true)) );
        $reply->send(gallery_remote::NO_WRITE_PERMISSION);
      }

    }
    else {
      $reply->set('status_text', t('Failed to load album with name %name.', array('name' => $name)));
      $reply->send(gallery_remote::NO_WRITE_PERMISSION);
    }
  }

  private function _image_properties(&$input, &$reply) {
    $name = trim($input->post('itemId'));

    if($name=='0') $item = item::root();
    else $item = ORM::factory("item")->where("id", "=", $name)->find();

    if(isset($item) && $item->loaded() && $item->id!='') {      
      $info = pathinfo($item->file_path());

      $reply->set('status_text', 'Item properties queried successfuly.');
      $reply->set('image.name', $item->id);
      $reply->set('image.raw_width', $item->width);
      $reply->set('image.raw_height', $item->height);
      $reply->set('image.raw_filesize', filesize($item->file_path()));
      $reply->set('image.resizedName', $item->name); //g3 stores resizes and thumbs different than g1
      $reply->set('image.resized_width', $item->resize_width);
      $reply->set('image.resized_height', $item->resize_height);
      $reply->set('image.thumbName', $item->name); //g3 stores resizes and thumbs different than g1
      $reply->set('image.thumb_width', $item->thumb_width);
      $reply->set('image.thumb_height', $item->thumb_height);
      $reply->set('image.caption', $item->title);
      $reply->set('image.title', $item->name);
      $reply->set('image.forceExtension', $info['extension']);
      $reply->set('image.hidden', access::user_can(identity::guest(), 'view', $item) ? 'no' : 'yes');
      $reply->send();
    }
    else {
      $reply->set('status_text', t('Failed to load album with name %name.', array('name' => $name)));
      $reply->send(gallery_remote::NO_VIEW_PERMISSION);
    }
  }

  private function _fetch_album_images(&$input, &$reply) {
    $name = trim($input->post('set_albumName'));
    $albums = trim($input->post('albums_too')); //yes/no [optional, since 2.13]
    $random = trim($input->post('random')); //yes/no [optional, G2 since ***]
    $limit = trim($input->post('limit')); //number-of-images [optional, G2 since ***]
    $extra = trim($input->post('extrafields')); //yes/no [optional, G2 since 2.12]
    $sizes = trim($input->post('all_sizes')); //yes/no [optional, G2 since 2.14]

    if($name=='0') $album = item::root();
    $album = ORM::factory("item")->where("id", "=", $name)->find();

    if(isset($album) && $album->loaded() && $album->id!='' && access::can('view', $album)) {
      
      if($albums!='no') $iterator = ORM::factory("item")->where("parent_id", "=", $album->id)->find_all();
      else $iterator = ORM::factory("item")->where("parent_id", "=", $album->id)->where("type", "<>", "album")->find_all();

      $reply->set('status_text', 'Album images query successful.');
      $reply->set('album.caption', $album->title);
      $reply->set('album.extrafields', 'Summary');

      /*
      $reply->set('image_count', '0');
      $reply->send();
      return;
      //*/
      
      $count = 0;
      foreach($iterator as $item) {

        if(access::can('view', $item)) {
        
          $count++;
          if($item->type != "album") {
            $info = pathinfo($item->file_path());
            
            $reply->set('image.name.'.$count, $item->id);
            $reply->set('image.raw_width.'.$count, $item->width);
            $reply->set('image.raw_height.'.$count, $item->height);
            $reply->set('image.raw_filesize.'.$count, filesize($item->file_path()));
            $reply->set('image.resizedName.'.$count, $item->name); //g3 stores resizes and thumbs different than g1
            $reply->set('image.resized_width.'.$count, $item->resize_width);
            $reply->set('image.resized_height.'.$count, $item->resize_height);
            /*
            $reply->set('image.resizedNum.'.$count, 'the number of resized versions for this image [since 2.14]');
              $reply->set('image.resized.resized-num.name.'.$count, 'filename of the resized-numth resize [G2 since 2.14]');
              $reply->set('image.resized.resized-num.width.'.$count, 'the width of the resized-numth resize [G2 since 2.14]');
              $reply->set('image.resized.resized-num.height.'.$count, 'the height of the resized-numth resize [G2 since 2.14]');
            //*/
            $reply->set('image.thumbName.'.$count, $item->name); //g3 stores resizes and thumbs different than g1
            $reply->set('image.thumb_width.'.$count, $item->thumb_width);
            $reply->set('image.thumb_height.'.$count, $item->thumb_height);
            $reply->set('image.caption.'.$count, $item->title);
            $reply->set('image.title.'.$count, $item->name);
              //$reply->set('image.extrafield.fieldname.'.$count, 'value of the extra field of key fieldname');
              $reply->set('image.extrafield.summary.'.$count, $item->description);
            $reply->set('image.clicks.'.$count, $item->view_count);
            $reply->set('image.capturedate.year.'.$count, date("Y", $item->captured));
            $reply->set('image.capturedate.mon.'.$count, date("m", $item->captured));
            $reply->set('image.capturedate.mday.'.$count, date("d", $item->captured));
            $reply->set('image.capturedate.hours.'.$count, date("H", $item->captured));
            $reply->set('image.capturedate.minutes.'.$count, date("i", $item->captured));
            $reply->set('image.capturedate.seconds.'.$count, date("s", $item->captured));
            $reply->set('image.forceExtension.'.$count, $info['extension']);
            $reply->set('image.hidden.'.$count, access::user_can(identity::guest(), 'view', $item) ? 'no' : 'yes');
           }
           else {
            $reply->set('album.name.'.$count, $item->id);
          }
          
        }
      }
      
      $reply->set('image_count', $count);
      //*  The baseurl contains a fully-qualified URL. A URL to each image
      //   can be obtained by appending the filename of the image to this.
      if(isset($item) && $item->loaded()) {
        $url = $item->file_url(true);
        $pos = strrpos($url, '/');
        $reply->set('baseurl', ($pos!==false ? substr($url, 0, $pos+1) : $url) );
      }
      else {
        $reply->set('baseurl', $album->abs_url());
      }
      //*/
      $reply->send();
      
    }
    else {
      $reply->set('status_text', t('Failed to load album with name %name.', array('name' => $name)));
      $reply->send(gallery_remote::NO_VIEW_PERMISSION);
    }
  }
      
}
