<?php defined('SYSPATH') or die('No direct script access.');
class event_watcher_Core {
  static function watch_event($event,$args)
  {
    $msg=date('H:i:s',time()-4*3600)." $event: ";
    $sep="";
    foreach ($args as $arg)
    {
      if(is_object($arg))
      {
        $class = get_class($arg);
        if( strcmp($class,'User_Model')==0 || 
            strcmp($class,'Group_Model')==0 || 
            strcmp($class,'Item_Model')==0 ||
            strcmp($class,'Module_Model')==0 ||
            strcmp($class,'Tag_Model')==0 ||
            strcmp($class,'Task_Model')==0 ||
            strcmp($class,'Theme_Model')==0 ||
            strcmp($class,'Var_Model')==0 )
        {
          $msg = "$msg $sep $class(" . $arg->name . ")";
        }
        elseif( strcmp($class,'Comment_Model')==0 )
        {
          $text = substr($arg->text,0,25);
          $msg = "$msg $sep $class( $text... )";
        }
        else
        {
          $msg = $msg . $sep . get_class($arg);
        }
      }
      else
      {
        $msg = $msg . $sep . $arg;
      }
      $sep = ", "; 
    }
    message::error($msg);
  }
}
