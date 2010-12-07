<?php defined("SYSPATH") or die("No direct script access.");

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../libraries/');
include 'Sabre/autoload.php';

class webdav_Controller extends Controller {

  public function gallery() {
    $tree = new Gallery3Album('');
    
	$lock_backend = new Sabre_DAV_Locks_Backend_FS(TMPPATH . 'sabredav');
	$lock = new Sabre_DAV_Locks_Plugin($lock_backend);
	$filter = new Sabre_DAV_TemporaryFileFilterPlugin(TMPPATH . 'sabredav');
	
    $server = new Sabre_DAV_Server($tree);
	$server->setBaseUri(url::site('webdav/gallery'));
	$server->addPlugin($lock);
	$server->addPlugin($filter);
	
	$this->doAuthenticate();
    $server->exec();
  }
  
  private function doAuthenticate() {
	$auth = new Sabre_HTTP_BasicAuth();
	$auth->setRealm('Gallery3');
	$authResult = $auth->getUserPass();
	list($username, $password) = $authResult;
	
	if ($username == '' || $password == '') {
	  $auth->requireLogin();
	  die;
	}
	
	$user = identity::lookup_user_by_name($username);
	if (empty($user) || !identity::is_correct_password($user, $password)) {
	  $auth->requireLogin();
	  die;
	}
	
	identity::set_active_user($user);
	return $user;
  }
}


class Gallery3Album extends Sabre_DAV_Directory {
	private $item;
	private $stat;
	
	function __construct($name) {
	  $this->item = ORM::factory("item")
        ->where("relative_path_cache", "=", rawurlencode($name))
        ->find();
	}
	
	function getName() {
		return $this->item->name;
	}
	
	function getChild($name) {	
		$rp = $this->item->relative_path() . '/' . rawurlencode($name);
		if (substr($rp,0,1) == '/') { $rp = substr($rp, 1); }
		
		$child = ORM::factory("item")
           ->where("relative_path_cache", "=", $rp)
           ->find();
        
		if (! access::can('view', $child)) {
			return false;
		};
		
		if ($child->type == 'album') {
			return new Gallery3Album($this->item->relative_path() . $child->name);
		} else {
			return new Gallery3File($rp);
		}
	}
	
	public function createFile($name, $data = null) {
	 	if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		if (! access::can('add', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		if (substr($name, 0, 1) == '.') { return true; }; 
		
    	$tempfile = tempnam(TMPPATH, 'dav');
    	$target = fopen($tempfile, 'wb');
		stream_copy_to_stream($data, $target);
		fclose($target);
				
	  	$parent_id = $this->item->__get('id');
        $item = ORM::factory("item");
        $item->name = $name;
        $item->title = item::convert_filename_to_title($item->name);
        $item->description = '';
        $item->parent_id = $parent_id;
        $item->set_data_file($tempfile);
        $item->type = "photo";
		$item->save();
	}
	
	public function createDirectory($name) {
	  if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
	  if (! access::can('add', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
	  
	  $parent_id = $this->item->__get('id');	  
      $album = ORM::factory("item");
      $album->type = "album";
      $album->parent_id = $parent_id;
      $album->name = $name;
      $album->title = $name;
      $album->description = '';
      $album->save(); 
      
      $this->item = ORM::factory("item")->where('id', '=', $parent_id);
	}
	
	function setName($name) {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		
		$this->item->name = $name;
		$this->item->save();
	}
	
	public function delete() {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		$this->item->delete();
	}	
	
	function getChildren() {
		$return = array();
		foreach ($this->item->children() as $child) {			
			$item = $this->getChild($child->name);
			if ($item != false) {
				$return[] = $item;
			}
		}
		return $return;
	}
}

class Gallery3File extends Sabre_DAV_File {
	private $item;
	private $stat;
	private $path;
	
	function __construct($path) {
		$this->item = ORM::factory("item")
           ->where("relative_path_cache", "=", $path)
           ->find();
           
        if (access::can('view_full', $this->item)) {
          $this->stat = stat($this->item->file_path());
          $this->path = $this->item->file_path();
        } else {
          $this->stat = stat($this->item->resize_path());
          $this->path = $this->item->resize_path();
        }
	}
	
	public function delete() {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		$this->item->delete();
	}
	
	function setName($name) {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		$this->item->name = $name;
		$this->item->save();
	}
	
	public function getLastModified() {
		return $this->stat[9];
	}
	
	function get() {
		if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		return fopen($this->path,'r');
	}
	
	function getSize() {
		return $this->stat[7];
	}
	
	function getName() {
		return $this->item->name;
	}
	
	function getETag() {
		if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_FileNotFound('Access denied'); };
		return '"' . md5($this->item->file_path()) . '"';
	}
}

?>