<?php defined("SYSPATH") or die("No direct script access.");
/**
 */
?>
<?
  $view = new View("admin_include.html");

  $view->is_module = FALSE;
  $view->name = module::get_var("gallery", "active_site_theme");
  $view->form = $form;
  $view->help = $help;
  print $view;
?>   

