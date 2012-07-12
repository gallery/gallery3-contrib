<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class Admin_Default_Sort_Controller extends Admin_Controller {
    public function index() {
        $form = $this->_get_form();

        if (request::method() == "post") {
            access::verify_csrf();

            if ($form->validate()) {
                module::set_var("default_sort", "default_sort_column", $_POST['sort_column']);
                module::set_var("default_sort", "default_sort_direction", $_POST['sort_direction']);

                message::success(t("Settings have been saved"));
                url::redirect("admin/default_sort");
            } else {
                message::error(t("There was a problem with the submitted form. Please check your values and try again."));
            }
        }

        print $this->_get_view();
    }

    private function _get_view($form = null) {
        $v = new Admin_View("admin.html");
        $v->page_title = t("Gallery 3 :: Set Default Sort Order for New Albums");

        $v->content = new View("admin_default_sort.html");
        $v->content->form = empty($form) ? $this->_get_form() : $form;

        return $v;
    }

    private function _get_form() {
        $form = new Forge("admin/default_sort", "", "post", array("id" => "g-admin-default_sort-form"));

        $group = $form->group("sort_order")->label(t("Sort Order"));

        $group  ->dropdown("sort_column")
                ->id(t("sort_column"))
                ->label(t("Sort by"))
                ->options(array_merge(array("none" => t("None")), album::get_sort_order_options()))
                ->selected(module::get_var("default_sort", "default_sort_column", "none"));

        $group  ->dropdown("sort_direction")
                ->id(t("sort_direction"))
                ->label(t("Sort by"))
                ->options(array("none" => t("None"), "ASC" => t("Ascending"), "DESC" => t("Descending")))
                ->selected(module::get_var("default_sort", "default_sort_direction", "none"));

        $form->submit("submit")->value(t("Save"));
        return $form;
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
