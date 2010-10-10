<?php defined("SYSPATH") or die("No direct script access.") ?>
<?= "<?php defined(\"SYSPATH\") or die(\"No direct script access.\");" ?>
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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
class Admin_<?= $class_name ?>_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      // @todo process the admin form

      message::success(t("<?= $name ?> Adminstration Complete Successfully"));

      url::redirect("admin/<?= $module ?>");
    }

    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_<?=$module ?>.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
    return $v;
  }
  
  private function _get_form() {
    $form = new Forge("admin/<?= $module ?>/handler", "", "post",
                      array("id" => "g-adminForm"));
    $group = $form->group("group");
    $group->input("text")->label(t("Text"))->rules("required");
    $group->submit("submit")->value(t("Submit"));

    return $form;
  }
}