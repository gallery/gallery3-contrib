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

class Admin_User_info_Controller extends Admin_Controller {
  public function index() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->page_title = t("User Information Settings");
    $view->content = new View("admin_user_info.html");
    $view->content->user_info_form = $this->_get_admin_form();
    print $view;
  }

  public function saveprefs() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out the values of the text boxes    
    $str_per_page = Input::instance()->post("per_page");
    $str_default_sort_column = Input::instance()->post("default_sort_column");
    $str_default_sort_order = Input::instance()->post("default_sort_order");    
    $str_use_default_gallery_date_format = Input::instance()->post("use_default_gallery_date_format");
    $str_date_format = Input::instance()->post("date_format");
    $str_log_logins = Input::instance()->post("log_logins");
    $str_color_login = Input::instance()->post("color_login");
    $str_log_logouts = Input::instance()->post("log_logouts");
    $str_color_logout = Input::instance()->post("color_logout");
    $str_log_failed_logins = Input::instance()->post("log_failed_logins");
    $str_color_failed_login = Input::instance()->post("color_failed_login");
    $str_log_re_authenticate_logins = Input::instance()->post("log_re_authenticate_logins");
    $str_color_re_authenticate_login = Input::instance()->post("color_re_authenticate_login");
    $str_log_user_created = Input::instance()->post("log_user_created");
    $str_color_user_created = Input::instance()->post("color_user_created");

    // Save Settings.
    module::set_var("user_info", "per_page", $str_per_page);
    module::set_var("user_info", "default_sort_column", $str_default_sort_column);
    module::set_var("user_info", "default_sort_order", $str_default_sort_order);
    module::set_var("user_info", "use_default_gallery_date_format", $str_use_default_gallery_date_format);
    module::set_var("user_info", "date_format", $str_date_format);
    module::set_var("user_info", "log_logins", $str_log_logins);
    module::set_var("user_info", "color_login", $str_color_login);
    module::set_var("user_info", "log_logouts", $str_log_logouts);
    module::set_var("user_info", "color_logout", $str_color_logout);
    module::set_var("user_info", "log_failed_logins", $str_log_failed_logins);
    module::set_var("user_info", "color_failed_login", $str_color_failed_login);
    module::set_var("user_info", "log_re_authenticate_logins", $str_log_re_authenticate_logins);
    module::set_var("user_info", "color_re_authenticate_login", $str_color_re_authenticate_login);
    module::set_var("user_info", "log_user_created", $str_log_user_created);
    module::set_var("user_info", "color_user_created", $str_color_user_created);
    message::success(t("Your Settings Have Been Saved."));
    
    // Load Admin page.
    $view = new Admin_View("admin.html");
    $view->page_title = t("User Information Settings");
    $view->content = new View("admin_user_info.html");
    $view->content->user_info_form = $this->_get_admin_form();
    print $view;
  }

  private function _get_admin_form() {
    // Make a new Form.
    $form = new Forge("admin/user_info/saveprefs", "", "post",
                      array("id" => "g-user_info-admin-form"));
                      
    // Create the input boxes for the User Information Settings                      
    $user_infoGroup = $form->group("UserInformationSettings");
    $user_infoGroup->dropdown("per_page")
      ->label(t("Number of records to display per page"))
      ->options(array("5" => t("5"),
                      "10" => t("10"),
                      "15" => t("15"),
                      "25" => t("25"),
                      "50" => t("50"),
                      "75" => t("75"),
                      "100" => t("100"),
                      "125" => t("125")))
      ->selected(module::get_var("user_info", "per_page"));
    $user_infoGroup->dropdown("default_sort_column")
      ->label(t("Default Column to Sort By"))
      ->options(array("id" => t("id"),
                      "user_id" => t("user_id"),
                      "user_name" => t("user_name"),
                      "ip_address" => t("ip_address"),
                      "time_stamp" => t("time_stamp"),
                      "action" => t("action")))
      ->selected(module::get_var("user_info", "default_sort_column"));
    $user_infoGroup->dropdown("default_sort_order")
      ->label(t("Default Sort Order"))
      ->options(array("ASC" => t("Ascending"),
                      "DESC" => t("Descending")))
      ->selected(module::get_var("user_info", "default_sort_order"));
    $user_infoGroup->dropdown("use_default_gallery_date_format")
      ->label(t("Use Default Gallery Date/Time Format"))
      ->options(array("Yes" => t("Yes"),
                      "No" => t("No")))
      ->selected(module::get_var("user_info", "use_default_gallery_date_format"));
    $user_infoGroup->input("date_format")
                   ->label(t("Format of the Date & Time - <a href='http://php.net/manual/en/function.date.php' target='_blank'>PHP Date</a>"))
                   ->value(module::get_var("user_info", "date_format"));
    $user_infoGroup->dropdown("log_logins")
      ->label(t("Log Logins"))
      ->options(array("Yes" => t("Yes"),
                      "No" => t("No")))
      ->selected(module::get_var("user_info", "log_logins"));
    $user_infoGroup->input("color_login")
                   ->label(t("<font color='%color_login'>Login Color</font> - Hex Only - <a href='http://www.w3schools.com/HTML/html_colornames.asp' target='_blank'>HTML Colors</a>",array("color_login" => module::get_var("user_info", "color_login"))))
                   ->value(module::get_var("user_info", "color_login"));
    $user_infoGroup->dropdown("log_logouts")
      ->label(t("Log Logouts"))
      ->options(array("Yes" => t("Yes"),
                      "No" => t("No")))
      ->selected(module::get_var("user_info", "log_logouts"));
    $user_infoGroup->input("color_logout")
                   ->label(t("<font color='%color_logout'>Logout Color</font> - Hex Only - <a href='http://www.w3schools.com/HTML/html_colornames.asp' target='_blank'>HTML Colors</a>",array("color_logout" => module::get_var("user_info", "color_logout"))))
                   ->value(module::get_var("user_info", "color_logout"));
    $user_infoGroup->dropdown("log_failed_logins")
      ->label(t("Log Failed Logins"))
      ->options(array("Yes" => t("Yes"),
                      "No" => t("No")))
      ->selected(module::get_var("user_info", "log_failed_logins"));
    $user_infoGroup->input("color_failed_login")
                   ->label(t("<font color='%color_failed_login'>Failed Login Color</font> - Hex Only - <a href='http://www.w3schools.com/HTML/html_colornames.asp' target='_blank'>HTML Colors</a>",array("color_failed_login" => module::get_var("user_info", "color_failed_login"))))
                   ->value(module::get_var("user_info", "color_failed_login"));
    $user_infoGroup->dropdown("log_re_authenticate_logins")
      ->label(t("Log Re-Authenticate Logins"))
      ->options(array("Yes" => t("Yes"),
                      "No" => t("No")))
      ->selected(module::get_var("user_info", "log_re_authenticate_logins"));
    $user_infoGroup->input("color_re_authenticate_login")
                   ->label(t("<font color='%color_re_authenticate_login'>Re-Authenticate Login Color</font> - Hex Only - <a href='http://www.w3schools.com/HTML/html_colornames.asp' target='_blank'>HTML Colors</a>",array("color_re_authenticate_login" => module::get_var("user_info", "color_re_authenticate_login"))))
                   ->value(module::get_var("user_info", "color_re_authenticate_login"));
    $user_infoGroup->dropdown("log_user_created")
      ->label(t("Log User Created"))
      ->options(array("Yes" => t("Yes"),
                      "No" => t("No")))
      ->selected(module::get_var("user_info", "log_user_created"));
    $user_infoGroup->input("color_user_created")
                   ->label(t("<font color='%color_user_created'>User Created Color</font> - Hex Only - <a href='http://www.w3schools.com/HTML/html_colornames.asp' target='_blank'>HTML Colors</a>",array("color_user_created" => module::get_var("user_info", "color_user_created"))))
                   ->value(module::get_var("user_info", "color_user_created"));

    // Add a save button to the form.
    $form->submit("SaveSettings")->value(t("Save"));

    // Return the newly generated form.
    return $form;
  }






  public function lookupip() {
    // Generate a new admin page.
    $view = new Admin_View("admin.html");
    $view->page_title = t("User Info: Lookup IP Address");
    $view->content = new View("admin_user_info_lookupip.html");
//    $view->content->block_ip_address = $this->_get_block_ip_address_form();
    print $view;
  }






  public function blockip() {
    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Figure out the values of the text boxes    
    // Figure out the ip address to block    
    $str_per_page = Input::instance()->post("per_page");
    $str_default_sort_column = Input::instance()->post("default_sort_column");
            
    // Block IP Addresss.
    module::set_var("user_info", "per_page", $str_per_page);
    message::success(t("Your Settings Have Been Saved."));
    
    // Load Admin page.
 //   $view = new Admin_View("admin.html");
 //   $view->page_title = t("User Information Settings");
 //   $view->content = new View("admin_user_info.html");
 //   $view->content->user_info_form = $this->_get_admin_form();
 //   print $view;
 
    $view = new Admin_View("admin.html");
    $view->page_title = t("User Info: Lookup IP Address");
    $view->content = new View("admin_user_info_lookupip.html");
    $view->content->block_ip_address = $this->_get_block_ip_address_form();

    print $view;
 
  }


  private function _get_block_ip_address_form() {
    // Make a new Form.
//    $form = new Forge("admin/user_info/blockip", "", "post",
//                      array("id" => "g-user_info-block-ip-address-form"));
//                      
//    // Create the input boxes for the User Information Settings                      
//    $block_ipGroup = $form->group("BlockIPAddress");
//    $block_ipGroup->dropdown("per_page")
//      ->label(t("Number of records to display per page"))
//      ->options(array("25" => t("25"),
//                      "50" => t("50"),
//                      "75" => t("75"),
//                      "100" => t("100"),
//                      "125" => t("125"),
//                      "150" => t("150")))
//      ->selected(module::get_var("user_info", "per_page"));
//    $block_ipGroup->input("date_format")
//                   ->label(t("Format of the Date & Time - <a href='http://php.net/manual/en/function.date.php' target='_blank'>PHP Date</a>"))
//                   ->value(module::get_var("user_info", "date_format"));
//    $block_ipGroup->input("color_login")
//                   ->label(t("Login Color - <a href='http://www.w3schools.com/HTML/html_colornames.asp' target='_blank'>HTML Colors</a>"))
//                   ->value(module::get_var("user_info", "color_login"));
//                   ->value(module::get_var("user_info", "color_failed_login"));
//
//    // Add a save button to the form.
//    $form->submit("SaveSettings")->value(t("Block IP Address"));
//
//    // Return the newly generated form.
//    return $form;
  }




}

