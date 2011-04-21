<?php defined("SYSPATH") or die("No direct script access.");


class Pseudo_album_Core {

  public $favourites;

  public function __construct($favourites) {

    $this->favourites = $favourites;
    // Set reasonable defaults
    $this->created = time();
    $this->rand_key = ((float)mt_rand()) / (float)mt_getrandmax();
    $this->thumb_dirty = 1;
    $this->resize_dirty = 1;
    $this->sort_column = "created";
    $this->sort_order = "ASC";
    $this->owner_id = identity::active_user()->id;
    $this->parent_id = 1;

    $this->id = 1;
    $this->type="album";
    $this->title=t("Favourites");
    $this->description=t("Currently selected favourites");
  }

  public function parent(){
    return ORM::factory("item")->where("id","=",1)->find();
  }

  public static function create($favourites)
  {
    return new Pseudo_album($favourites);
  }

  public function loaded(){
    return true;
  }

  public function children_count(){
    return count($this->favourites->contents);
  }

  public function parents(){
    return ORM::factory("item")->where("id","=", 1)->find_all();
  }

  /**
   * Add a set of restrictions to any following queries to restrict access only to items
   * viewable by the active user.
   * @chainable
   */
  public function viewable() {
    return $this;
  }

  /**
   * Is this item an album?
   * @return true if it's an album
   */
  public function is_album() {
    return true;
  }

  /**
   * Is this item a photo?
   * @return true if it's a photo
   */
  public function is_photo() {
    return false;
  }

  /**
   * Is this item a movie?
   * @return true if it's a movie
   */
  public function is_movie() {
    return false;
  }

  public function delete($ignored_id=null) {
  }

  /**
   * Specify the path to the data file associated with this item.  To actually associate it,
   * you still have to call save().
   * @chainable
   */
  public function set_data_file($data_file) {
  }

  /**
   * Return the server-relative url to this item, eg:
   *   /gallery3/index.php/BobsWedding?page=2
   *   /gallery3/index.php/BobsWedding/Eating-Cake.jpg
   *
   * @param string $query the query string (eg "show=3")
   */
  public function url($query=null) {
    $url = url::site("favourites");
    if ($query) {
      $url .= "?$query";
    }
    return $url;
  }

  /**
   * Return the full url to this item, eg:
   *   http://example.com/gallery3/index.php/BobsWedding?page=2
   *   http://example.com/gallery3/index.php/BobsWedding/Eating-Cake.jpg
   *
   * @param string $query the query string (eg "show=3")
   */
  public function abs_url($query=null) {
    $url = url::abs_site("favourites");
    if ($query) {
      $url .= "?$query";
    }
    return $url;
  }

  /**
   * album: /var/albums/album1/album2
   * photo: /var/albums/album1/album2/photo.jpg
   */
  public function file_path() {
    return VARPATH . "albums/";
  }

  /**
   * album: http://example.com/gallery3/var/resizes/album1/
   * photo: http://example.com/gallery3/var/albums/album1/photo.jpg
   */
  public function file_url($full_uri=false) {
    return;
  }

  /**
   * album: /var/resizes/album1/.thumb.jpg
   * photo: /var/albums/album1/photo.thumb.jpg
   */
  public function thumb_path() {
  }

  /**
   * Return true if there is a thumbnail for this item.
   */
  public function has_thumb() {
    return false;
  }

  /**
   * album: http://example.com/gallery3/var/resizes/album1/.thumb.jpg
   * photo: http://example.com/gallery3/var/albums/album1/photo.thumb.jpg
   */
  public function thumb_url($full_uri=false) {
  }

  /**
   * album: /var/resizes/album1/.resize.jpg
   * photo: /var/albums/album1/photo.resize.jpg
   */
  public function resize_path() {
  }

  /**
   * album: http://example.com/gallery3/var/resizes/album1/.resize.jpg
   * photo: http://example.com/gallery3/var/albums/album1/photo.resize.jpg
   */
  public function resize_url($full_uri=false) {
  }


  /**
   * Return the relative path to this item's file.  Note that the components of the path are
   * urlencoded so if you want to use this as a filesystem path, you need to call urldecode
   * on it.
   * @return string
   */
  public function relative_path() {
    if (!$this->loaded()) {
      return;
    }

    if (!isset($this->relative_path_cache)) {
      $this->_build_relative_caches()->save();
    }
    return $this->relative_path_cache;
  }

  /**
   * Return the relative url to this item's file.
   * @return string
   */
  public function relative_url() {
  }


  /**
   * Handle any business logic necessary to create or modify an item.
   * @see ORM::save()
   *
   * @return ORM Item_Model
   */
  public function save() {
  }

  /**
   * Return the Item_Model representing the cover for this album.
   * @return Item_Model or null if there's no cover
   */
  public function album_cover() {
    return null;
  }

  /**
   * Find the position of the given child id in this album.  The resulting value is 1-indexed, so
   * the first child in the album is at position 1.
   */
  public function get_position($child, $where=array()) {
    /*
    if ($this->sort_order == "DESC") {
      $comp = ">";
    } else {
      $comp = "<";
    }
    $db = db::build();

    // If the comparison column has NULLs in it, we can't use comparators on it and will have to
    // deal with it the hard way.
    $count = $db->from("items")
      ->where("parent_id", "=", $this->id)
      ->where($this->sort_column, "IS", null)
      ->merge_where($where)
      ->count_records();

    if (empty($count)) {
      // There are no NULLs in the sort column, so we can just use it directly.
      $sort_column = $this->sort_column;

      $position = $db->from("items")
        ->where("parent_id", "=", $this->id)
        ->where($sort_column, $comp, $child->$sort_column)
        ->merge_where($where)
        ->count_records();

      // We stopped short of our target value in the sort (notice that we're using a < comparator
      // above) because it's possible that we have duplicate values in the sort column.  An
      // equality check would just arbitrarily pick one of those multiple possible equivalent
      // columns, which would mean that if you choose a sort order that has duplicates, it'd pick
      // any one of them as the child's "position".
      //
      // Fix this by doing a 2nd query where we iterate over the equivalent columns and add them to
      // our base value.
      foreach ($db
               ->select("id")
               ->from("items")
               ->where("parent_id", "=", $this->id)
               ->where($sort_column, "=", $child->$sort_column)
               ->merge_where($where)
               ->order_by(array("id" => "ASC"))
               ->execute() as $row) {
        $position++;
        if ($row->id == $child->id) {
          break;
        }
      }
    } else {
      // There are NULLs in the sort column, so we can't use MySQL comparators.  Fall back to
      // iterating over every child row to get to the current one.  This can be wildly inefficient
      // for really large albums, but it should be a rare case that the user is sorting an album
      // with null values in the sort column.
      //
      // Reproduce the children() functionality here using Database directly to avoid loading the
      // whole ORM for each row.
      $order_by = array($this->sort_column => $this->sort_order);
      // Use id as a tie breaker
      if ($this->sort_column != "id") {
        $order_by["id"] = "ASC";
      }

      $position = 0;
      foreach ($db->select("id")
               ->from("items")
               ->where("parent_id", "=", $this->id)
               ->merge_where($where)
               ->order_by($order_by)
               ->execute() as $row) {
        $position++;
        if ($row->id == $child->id) {
          break;
        }
      }
    }

    return $position;*/
  }

  /**
   * Return an <img> tag for the thumbnail.
   * @param array $extra_attrs  Extra attributes to add to the img tag
   * @param int (optional) $max Maximum size of the thumbnail (default: null)
   * @param boolean (optional) $center_vertically Center vertically (default: false)
   * @return string
   */
  public function thumb_img($extra_attrs=array(), $max=null, $center_vertically=false) {
    return "";
  }

  /**
   * Calculate the largest width/height that fits inside the given maximum, while preserving the
   * aspect ratio.
   * @param int $max Maximum size of the largest dimension
   * @return array
   */
  public function scale_dimensions($max) {
  }

  /**
   * Return an <img> tag for the resize.
   * @param array $extra_attrs  Extra attributes to add to the img tag
   * @return string
   */
  public function resize_img($extra_attrs) {
  }

  /**
   * Return a flowplayer <script> tag for movies
   * @param array $extra_attrs
   * @return string
   */
  public function movie_img($extra_attrs) {
  }

  /**
   * Return all of the children of this album.  Unless you specify a specific sort order, the
   * results will be ordered by this album's sort order.
   *
   * @chainable
   * @param   integer  SQL limit
   * @param   integer  SQL offset
   * @param   array    additional where clauses
   * @param   array    order_by
   * @return array ORM
   */
  function children($limit=null, $offset=null, $where=array(), $order_by=null) {
    if (empty($this->favourites->contents)){
      return null;
    }
    return ORM::factory("item")->where("id","in", $this->favourites->contents)->find_all();
    // get childresn
    /*
    if (empty($order_by)) {
      $order_by = array($this->sort_column => $this->sort_order);
      // Use id as a tie breaker
      if ($this->sort_column != "id") {
        $order_by["id"] = "ASC";
      }
    }
    return parent::children($limit, $offset, $where, $order_by);*/
  }

  /**
   * Return the children of this album, and all of it's sub-albums.  Unless you specify a specific
   * sort order, the results will be ordered by this album's sort order.  Note that this
   * album's sort order is imposed on all sub-albums, regardless of their sort order.
   *
   * @chainable
   * @param   integer  SQL limit
   * @param   integer  SQL offset
   * @param   array    additional where clauses
   * @return object ORM_Iterator
   */
  function descendants($limit=null, $offset=null, $where=array(), $order_by=null) {
    if (empty($this->favourites->contents)){
      return null;
    }
    return ORM::factory("item")->where("id","in", $this->favourites->contents)->find_all();
    /*
    if (empty($order_by)) {
      $order_by = array($this->sort_column => $this->sort_order);
      // Use id as a tie breaker
      if ($this->sort_column != "id") {
        $order_by["id"] = "ASC";
      }
    }
    return parent::descendants($limit, $offset, $where, $order_by);*/
  }

  /**
   * Specify our rules here so that we have access to the instance of this model.
   */
  public function validate(Validation $array=null) {
  }

}