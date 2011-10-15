<?php defined("SYSPATH") or die("No direct script access.");

class Admin_fittoscreen_Controller extends Admin_Controller {
  public function index() {
    print $this->_get_view();
  }

  private function _get_view($form=null) {
    $view = new Admin_View("admin.html");
    $view->page_title = t("Fit to Screen parameters");

    $view->content = new View("admin_fittoscreen.html");
    $view->content->form = (empty($form) ? $this->_get_form() : $form) ;

    return $view;
  }

  private function _get_form() {
    $form = new Forge("admin/fittoscreen/save", "", "post", array("id" => "g-admin-form"));

    $form->dropdown("width_unit")->label(t("Image width unit"))->options(array("px"=>"pixel margin","pr"=>"max pourcentage"))->selected(module::get_var("fittoscreen", "width_unit"));
    $form->input("width")->label(t('width'))->rules("required|valid_numeric|length[1,5]")->value(module::get_var("fittoscreen", "width"));
    $form->dropdown("height_unit")->label(t("Image height unit"))->options(array("px"=>"pixel margin","pr"=>"max pourcentage"))->selected(module::get_var("fittoscreen", "height_unit"));
    $form->input("height")->label(t('height'))->rules("required|valid_numeric|length[1,5]")->value(module::get_var("fittoscreen", "height"));

    $form->submit("submit")->value(t("Save"));
    return $form;
  }

  public function save(){
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
      module::set_var("fittoscreen", "width_unit",  $form->width_unit->value);      
      module::set_var("fittoscreen", "width",  $form->width->value);      
      module::set_var("fittoscreen", "height_unit",  $form->height_unit->value);      
      module::set_var("fittoscreen", "height",  $form->height->value);      

    }
    
    print $this->_get_view($form);
  }
}

?>
