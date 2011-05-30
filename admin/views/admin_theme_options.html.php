<?php defined("SYSPATH") or die("No direct script access.");
/**
 */
?>
<?
  $view = new View("admin_include.html");

  $view->is_module = FALSE;
  $view->name = "pear4gallery3";
  $view->form = $form;
  $view->help = $help;
  print $view;
?>   

