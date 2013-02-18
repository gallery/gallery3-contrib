<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class Admin_Auto_Date_Controller extends Admin_Controller {
  public function index() {
    $form = $this->_get_form();

    if (request::method() == "post") {
      access::verify_csrf();

      if ($form->validate()) {
        module::set_var("auto_date", "template", $_POST['template']);
        message::success(t("Settings have been saved"));
        url::redirect("admin/auto_date");
      } else {
        message::error(t("There was a problem with the submitted form. Please check your values and try again."));
      }
    }

    print $this->_get_view();
  }

  private function _get_view($form = null) {
    $v = new Admin_View("admin.html");
    $v->page_title = t("Gallery 3 :: Set Template for unknown items");

    $v->content = new View("admin_auto_date.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;

    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/auto_date", "", "post", array("id" => "g-admin-auto_date-form"));

    $group = $form->group("auto_date")->label(t("Default filename convention(php's <a href=\"http://php.net/manual/en/function.strptime.php\">strptime() format</a>)"));

    $group->input("template")
      ->id("template")
      ->label(t("Template:"))
      ->value(module::get_var("auto_date", "template"));

    $form->submit("submit")->value(t("Save"));
    return $form;
  }
}
