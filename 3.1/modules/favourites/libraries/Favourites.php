<?php defined("SYSPATH") or die("No direct script access.");


class Favourites_Core {
  public $contents = array();

  public function toggle($id){
    foreach ($this->contents as $i => $value) {
      if ($value==$id){
        unset($this->contents[$i]);
        return false;
      }
    }
    $this->contents[]=$id;
    return true;
  }

  public function contains($id){
    foreach ($this->contents as $i => $value){
      if ($value==$id)  return true;
    }
    return false;
  }

  public function hasFavourites(){
    return !empty($this->contents);
  }

  public function get_as_album(){
    return Pseudo_album::create($this);
  }

  public function clear(){
    $this->contents = array();
  }

  public function getUrl(){

    $toReturn = url::site("favourites/view","http");

    foreach ($this->contents as $i => $value){
      $toReturn = $toReturn."/".$value;
    }
    return $toReturn;
  }

  public static function get(){
    return Session::instance()->get("favourites");
  }


  public static function getOrCreate(){
    $session = Session::instance();

    $favourites = $session->get("favourites");
    if (!$favourites)
    {
      $favourites  = new Favourites();
      $session->set("favourites", $favourites);
    }
    return $favourites;
  }
}
