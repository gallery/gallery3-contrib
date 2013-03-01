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
class Favourites_Controller extends Controller {
  public function index() {

    if (favourites_configuration::isUsersOnly() && identity::active_user()->name =="guest"){
      //login required.
      url::redirect("login/html");
      return;
    }

    $album = Favourites::getOrCreate()->get_as_album();

    $page_size = module::get_var("gallery", "page_size", 9);
    $input = Input::instance();
    $show = $input->get("show");

    if ($show) {
      $child = ORM::factory("item", $show);
      $index = $album->get_position($child);
      if ($index) {
        $page = ceil($index / $page_size);
        if ($page == 1) {
          //url::redirect("favourites");
        } else {
          //url::redirect("favourites?page=$page");
        }
      }
    }


    $page = $input->get("page", "1");
    $children_count = $album->viewable()->children_count();
    $offset = ($page - 1) * $page_size;
    $max_pages = max(ceil($children_count / $page_size), 1);


    // Make sure that the page references a valid offset
    if ($page < 1) {
      //url::redirect($album->abs_url());
    } else if ($page > $max_pages) {
      //url::redirect($album->abs_url("page=$max_pages"));
    }



    $template = new Theme_View("page.html", "collection", "favourites");
    $template->set_global("page", $page);
    $template->set_global("page_title", null);
    $template->set_global("max_pages", $max_pages);
    $template->set_global("page_size", $page_size);
    $template->set_global("children", $album->viewable()->children($page_size, $offset));
    $template->set_global("children_count", $children_count);
    $template->content = new View("dynamic.html");

    print $template;
  }

  public function view(){
    if (favourites_configuration::isUsersOnly() && identity::active_user()->name =="guest"){
      //login required.
      Session::instance()->set("continue_url", url::current(true));
      $template = new Theme_View("page.html", "collection", "album");
      $template->content = new View("login_required.html");
      $template->content->login_form = new View("login_ajax.html");
      $template->content->login_form->form = auth::get_login_form("login/auth_html");
      print $template;
      return;
    }

    // extract details from url
    $favourites = Favourites::getOrCreate();
    $favourites->clear();
    $array = func_get_args();
    foreach($array as $i=>$item){
      $favourites->toggle($item);
    }
    url::redirect("favourites");
  }

  private function getSaveForm(){

    $form = new Forge("favourites/save_favourites", "", "post", array("id" => "gAddToBasketForm"));
    $group = $form->group("save")->label(t("Save Favourites"));
    $group->hidden("id");
    $group->input("fullname")->label(t("Name"))->id("gname")
      ->error_messages("required", t("You must provide your name"))
      ->error_messages("not_logged_in", t("You must be logged in to send favourites."))
      ->rules("required");
    $group->input("email")->label(t("Email Address"))->id("gemail")
      ->error_messages("required", t("You must provide an email address"))
      ->error_messages("valid_email", t("You must provide a valid email address"))
      ->rules("valid_email")
      ->rules("required");
    $group->textarea("details")->label(t("Comments"))->id("gdetails");

    $group->submit("")->value(t("save"));
    return $form;
  }
  public function save(){
    $view = new View("save_dialog.html");

    // get the basket to add to
    $form = self::getSaveForm();
    $view->form = $form;

    print $view;

  }

  public function save_favourites($id){

    access::verify_csrf();

    $form = self::getSaveForm();
    $valid = $form->validate();
    $name = $form->save->fullname->value;
    $email_address = $form->save->email->value;
    $comments = $form->save->details->value;


    if (!isset($email_address ) || strlen($email_address) == 0) {
      $valid=false;
      $form->save->email->add_error("required", 1);
    }

    if (!isset($name ) || strlen($name) == 0) {
      $valid=false;
      $form->save->fullname->add_error("required", 1);
    }

    if (favourites_configuration::isUsersOnly() && identity::active_user()->name =="guest"){
      $valid=false;
      $form->save->fullname->add_error("not_logged_in", 1);
    }

    if ($valid){

      $favourites = Favourites::getOrCreate();

      $from = "From: ".favourites_configuration::getFromEmailAddress();

      if (favourites_configuration::isEmailAdmin())
      {
        $admin_email = $name." has chosen following photo as his or her favourites.\n";

        // create the order items
        $items = ORM::factory("item")->where("id","in", $favourites->contents)->find_all();
        foreach ($items->contents as $id=>$item){
          $admin_email = $admin_email."
            ".$item->title." - ".$item->url()."";
        }
        $admin_email = $admin_email."\n you can view this favourite list at \n".$favourites->getUrl()
          ."\n\n He or she has included the additional comments. \n".$comments
          ."\n You can e-mail him or her with the following e-mail address ".$email_address;

        mail(favourites_configuration::getEmailAddress(), $name."'s favourites.", $admin_email, $from);
      }

      $email = favourites_configuration::replaceStrings(
        favourites_configuration::getEmailTemplate(),
        Array(
          "name"=>$name,
          "comments"=>$comments,
          "url"=>$favourites->getUrl(),
          "owner"=>favourites_configuration::getOwner()));

      mail($email_address,$name."'s Favourites",$email, $from);

      json::reply(array("result" => "success", "location" => url::site("favourites")));
      return;
    }
    json::reply(array("result" => "error", "html" => (string)$form));
  }

  public function toggle_favourites($id){
    $favourites = Favourites::getOrCreate();
    $infavour = $favourites ->toggle($id);
    $title = $infavour?t("Remove from favourites"):t("Add to favourites");
    json::reply(array("result" => "success",
                      "favourite" => $infavour,
                      "hasfavourites" => $favourites->hasFavourites(),
                      "title" => (string)$title));
  }

  public function clear_favourites(){
    Favourites::getOrCreate()->clear();
  }
}
