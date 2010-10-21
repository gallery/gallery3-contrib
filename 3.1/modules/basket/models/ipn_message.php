<?php defined("SYSPATH") or die("No direct script access.");

class Ipn_message_Model extends ORM {

  public function formatedTime(){
    return date("D jS F H:i", $this->date);
  }

  public function json_encode(){
    $toReturn = array(
      'id' => $this->id,
      'date' => $this->formatedTime(),
      'text' => text::convertText($this->text));
    return $toReturn;
  }
}