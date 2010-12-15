<?php defined("SYSPATH") or die("No direct script access.");

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../libraries/');
include 'Sabre/autoload.php';

class webdav_Controller extends Controller {

  public function gallery() {
    $root = new Gallery3Album('');
    $tree = new Gallery3DAVTree($root);
    
    // Skip the lock plugin for now, we don't want Finder to get write support for the time being.
	//$lock_backend = new Sabre_DAV_Locks_Backend_FS(TMPPATH . 'sabredav');
	//$lock = new Sabre_DAV_Locks_Plugin($lock_backend);
	$filter = new Sabre_DAV_TemporaryFileFilterPlugin(TMPPATH . 'sabredav');
	
    $server = new Sabre_DAV_Server($tree);
    #$server = new Gallery3DAV($tree);
	
	$server->setBaseUri(url::site('webdav/gallery'));
	//$server->addPlugin($lock);
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

class Gallery3DAVCache {
	protected static $cache;
	private static $instance;
	
	private function __construct() {
		$this->cache = array();
	}

    private function encodePath($path)
	{
	  $path = trim($path, '/');
	  $encodedArray = array();
	  foreach (split('/', $path) as $part)
	  {
	  	$encodedArray[] = rawurlencode($part);
	  }
	  
	  $path = join('/', $encodedArray);
	  
	  return $path;
	}

    public function getAlbumOf($path) {
      $path = substr($path, 0, strrpos($path, '/'));	  
	  
	  return $this->getItemAt($path);	  
    }
    
    public function getItemAt($path)
    {
      $path = trim($path, '/');
	  $path = $this->encodePath($path);
	  
	  if (isset($this->cache[$path])) {
	  	return $this->cache[$path];
	  }
	  
	  $item = ORM::factory("item")
        ->where("relative_path_cache", "=", $path)
        ->find();
	  
	  $this->cache[$path] = $item;
	  return $item;
    }
		
	public static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        
        return self::$instance;
	}
	
	public function __clone() {}
	
}

class Gallery3DAVTree extends Sabre_DAV_Tree {
    protected $rootNode;
	
    public function __construct(Sabre_DAV_ICollection $rootNode) {
    	$this->cache = Gallery3DAVCache::singleton();
        $this->rootNode = $rootNode;
    }
    	
    
    public function move($source, $target) {
      $sourceItem = $this->cache->getItemAt($source);
      $targetItem = $this->cache->getAlbumOf($target);
      
	  if (! access::can('view', $sourceItem)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
	  if (! access::can('edit', $sourceItem)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
	  if (! access::can('view', $targetItem)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
	  if (! access::can('edit', $targetItem)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
      
      $sourceItem->parent_id = $targetItem->id;
      $sourceItem->save();
      return true;
    }
	
    public function getNodeForPath($path) {
    	
        $path = trim($path,'/');
        
        $currentNode = $this->rootNode;		
	    $item = $this->cache->getItemAt($path);
		
    	if (! $item->id) {
    		throw new Sabre_DAV_Exception_FileNotFound('Could not find node at path: ' . $path);
    	}
		
    	if ($item->type == 'album') { $currentNode = new Gallery3Album($path); }
		else { $currentNode = new Gallery3File($path); }
		
        return $currentNode;
    }
}

class Gallery3Album extends Sabre_DAV_Directory {
	private $item;
	private $stat;
	private $path;
	
	function __construct($path) {
	  $this->cache = Gallery3DAVCache::singleton();
	  $this->path = $path;
	  $this->item = $this->cache->getItemAt($path);
	}
	
	function getName() {
		return $this->item->name;
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
	
	function getChild($name) {
		$rp = $this->path . '/' . $name;
		
		$child = $this->cache->getItemAt($rp);
        
        if (! $child->id) {
			throw new Sabre_DAV_Exception_FileNotFound('Access denied');        	
        }
        
		if (! access::can('view', $child)) {
        	return false;
		};
		
		if ($child->type == 'album') {
			return new Gallery3Album($rp);
		} else {
			return new Gallery3File($rp);
		}
	}
		
	public function createFile($name, $data = null) {
	 	if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		if (! access::can('add', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
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
	  if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
	  if (! access::can('add', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
	  
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
	
	function getLastModified() {
		return $this->item->updated;
	}
	
	function setName($name) {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		
		$this->item->name = $name;
		$this->item->save();
	}
	
	public function delete() {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		$this->item->delete();
	}	
}

class Gallery3File extends Sabre_DAV_File {
	private $item;
	private $stat;
	private $path;
	
	function __construct($path) {
	  $this->cache = Gallery3DAVCache::singleton();
	  $this->item = $this->cache->getItemAt($path);
      
      if (access::can('view_full', $this->item)) {
        $this->stat = stat($this->item->file_path());
        $this->path = $this->item->file_path();
      } else {
        $this->stat = stat($this->item->resize_path());
        $this->path = $this->item->resize_path();
      }
	}
	
	public function delete() {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		$this->item->delete();
	}
	
	function setName($name) {
		if (! access::can('edit', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		$this->item->name = $name;
		$this->item->save();
	}
	
	public function getLastModified() {
		return $this->item->updated;
	}
	
	function get() {
		if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		return fopen($this->path,'r');
	}
	
	function getSize() {
		return $this->stat[7];
	}
	
	function getName() {
		return $this->item->name;
	}
	
	function getETag() {
		if (! access::can('view', $this->item)) { throw new Sabre_DAV_Exception_Forbidden('Access denied'); };
		return '"' . md5($this->item->file_path()) . '"';
	}
}

?>