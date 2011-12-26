<?php defined("SYSPATH") or die("No direct script access.");

class fittoscreen_installer {
  static function install() {
    module::set_var("fittoscreen", "width",  "150");    
    module::set_var("fittoscreen", "height",  "200");    
    module::set_version("fittoscreen", 10);
  }


}

?>

