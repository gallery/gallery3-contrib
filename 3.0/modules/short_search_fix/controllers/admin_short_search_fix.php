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
class Admin_Short_Search_Fix_Controller extends Admin_Controller {
  public function index() {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Short search fix settings");
    $view->content = new View("admin_short_search_fix.html");
    $view->content->form = $this->_get_admin_form();
    print $view;
  }

  public function save() {
    access::verify_csrf();
    $form = $this->_get_admin_form();
    $form->validate();
    module::set_var("short_search_fix", "search_prefix",
                    $form->short_search_fix_settings->search_prefix->value);
    message::success(t("Short search fix settings updated"));
    if ($form->short_search_fix_settings->mark_rebuild_search_records->value) {
      $db = Database::instance();
      $db->query("UPDATE {search_records} SET dirty=1;");
    }
    if ($form->short_search_fix_settings->mark_uptodate_search_records->value) {
      $db = Database::instance();
      $db->query("UPDATE {search_records} SET dirty=0;");
    }
    url::redirect("admin/short_search_fix");
  }

  private function _get_admin_form() {
    $form = new Forge("admin/short_search_fix/save", "", "post",
                      array("id" => "g-short-search-fix-admin-form"));
    $short_search_fix_settings = $form->group("short_search_fix_settings")->label(t("Prefix and search record rebuild"));
    $short_search_fix_settings->input("search_prefix")
      ->label(t("Enter the prefix to be added to the start of every search word (Default: 1Z)"))
      ->value(module::get_var("short_search_fix", "search_prefix"));
    $short_search_fix_settings->checkbox("mark_rebuild_search_records")
      ->label(t("Mark all search records for rebuild.  This is needed when the prefix is changed.  Afterward, go to Maintenace | Update search records."))
      ->checked(false);
    $short_search_fix_settings->checkbox("mark_uptodate_search_records")
      ->label(t("Mark all search records as up-to-date.  This is a pseudo-undo of the above."))
      ->checked(false);
    $short_search_fix_settings->submit("save")->value(t("Save"));
    return $form;
  }
}