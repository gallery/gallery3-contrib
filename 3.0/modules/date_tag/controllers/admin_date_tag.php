<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class Admin_Date_Tag_Controller extends Admin_Controller {
  public function index() {
    $form = $this->_get_form();

    if (request::method() == "post") {
      access::verify_csrf();

      if ($form->validate()) {
        module::set_var("date_tag", "template", $_POST['template']);
        message::success(t("Settings have been saved"));
        url::redirect("admin/date_tag");
      } else {
        message::error(t("There was a problem with the submitted form. Please check your values and try again."));
      }
    }

    print $this->_get_view();
  }

  private function _get_view($form = null) {
    $v = new Admin_View("admin.html");
    $v->page_title = t("Gallery 3 :: Set Template for New Item Tags");

    $v->content = new View("admin_date_tag.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;

    return $v;
  }

  private function _get_form() {
    $form = new Forge("admin/date_tag", "", "post", array("id" => "g-admin-date_tag-form"));

    $group = $form->group("date_tag")->label(t("Default Tag (php's <a href=\"http://php.net/manual/en/function.date.php\">date() format</a>)"));

    $group->input("template")
      ->id("template")
      ->label(t("Template:"))
      ->value(module::get_var("date_tag", "template"));

    $form->submit("submit")->value(t("Save"));
    return $form;
  }
}
