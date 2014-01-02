<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
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
class bp_postage_band_Core {

 static function get_add_form_admin() {
    $form = new Forge("admin/postage_bands/add_postage_band", "", "post", array("id" => "gAddPostageForm"));
    $group = $form->group("add_postage")->label(t("Add Postage Band"));
    $group->input("name")->label(t("Name"))->id("gPostageName")
      ->error_messages("in_use", t("There is already a postage band with that name"));
    $group->input("flat_rate")->label(t("Flat Rate"))->id("gFlatRate");
    $group->input("per_item")->label(t("Per Item"))->id("gPetItem");
    $group->checkbox("via_download")->label(t("Via Download"))->id("gViaDownload");
    $group->submit("")->value(t("Add Postage Band"));
    $postage = ORM::factory("bp_postage_band");
    return $form;
  }

  static function get_edit_form_admin($postage) {
   $form = new Forge("admin/postage_bands/edit_postage_band/$postage->id", "", "post",
        array("id" => "gEditPostageForm"));
    $group = $form->group("edit_postage")->label(t("Edit Postage Band"));
    $group->input("name")->label(t("Name"))->id("gPostageName")->value($postage->name);
    $group->inputs["name"]->error_messages("in_use", t("There is already a postage band with that name"));
    $group->input("flat_rate")->label(t("Flat Rate"))->id("gFlatRate")->value($postage->flat_rate);
    $group->input("per_item")->label(t("Per Item"))->id("gPetItem")->value($postage->per_item);
    $group->checkbox("via_download")->label(t("Via Download"))->id("gViaDownload")->checked($postage->via_download);

    $group->submit("")->value(t("Modify Postage Band"));
    return $form;
  }


  static function get_delete_form_admin($postage) {
    $form = new Forge("admin/postage_bands/delete_postage_band/$postage->id", "", "post",
                      array("id" => "gDeletePostageForm"));
    $group = $form->group("delete_postage")->label(
      t("Are you sure you want to delete postage band %name?", array("name" => $postage->name)));
    $group->submit("")->value(t("Delete postage band %name", array("name" => $postage->name)));
    return $form;
  }

  /**
   * Create a new postage band
   *
   * @param string  $name
   * @param string  $full_name
   * @param string  $password
   * @return User_Model
   */
  static function create($name, $flatrate, $peritemcost, $via_download) {
    $postage = ORM::factory("bp_postage_band")->where("name", "=", $name)->find();
    if ($postage->loaded()) {
      throw new Exception("@todo postage already EXISTS $name");
    }

    $postage->name = $name;
    $postage->flat_rate = $flatrate;
    $postage->per_item = $peritemcost;
    $postage->via_download = $via_download;

    $postage->save();
    return $postage;
  }

  /**
   * returns the array of postage bands
   * @return an array of postage bands
   */
  static function getPostageArray(){
    $postagea = array();

    $postages = ORM::factory("bp_postage_band")->find_all();
    foreach ($postages as $postage){
      $show = true;
      $postagea[$postage->id] = $postage->name;
    }

    return $postagea;
  }

}