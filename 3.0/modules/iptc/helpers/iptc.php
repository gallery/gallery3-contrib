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

require_once(MODPATH . "iptc/lib/functions.php");

/**
 * This is the API for handling iptc data.
 */
class iptc_Core {

  protected static $iptc_keys;

  static function extract($item) {
    $keys = array();
    // Only try to extract IPTC from photos
    if ($item->is_photo() && $item->mime_type == "image/jpeg") {
      $info = getJpegHeader($item->file_path());
      if ($info !== FALSE) {
        $iptcBlock = getIptcBlock($info);
	if ($iptcBlock !== FALSE) {
	  $iptc = iptcparse($iptcBlock);
	} else {
          $iptc = array();
        }
      	$xmp = getXmpDom($info);
        
        foreach (self::keys() as $keyword => $iptcvar) {
          $iptc_key = $iptcvar[0];
          $xpath = $iptcvar[2];
          $value = null;
          if ($xpath != null) {
            $value = getXmpValue($xmp, $xpath);
          }
          if ($value == null) {
            if (!empty($iptc[$iptc_key])) {
              $value = implode(";", $iptc[$iptc_key]);
              if (function_exists("mb_detect_encoding") && mb_detect_encoding($value) != "UTF-8") {
                $value = utf8_encode($value);
              }
            }
          }
          if ($value != null) {
            $keys[$keyword] = Input::clean($value);
          }
        }
      }
    }

    $record = ORM::factory("iptc_record")->where("item_id", "=", $item->id)->find();
    if (!$record->loaded()) {
      $record->item_id = $item->id;
    }
    $record->data = serialize($keys);
    $record->key_count = count($keys);
    $record->dirty = 0;
    $record->save();

    if ( array_key_exists('Keywords', $keys) ) {
       $tags = explode(';', $keys['Keywords']);
          foreach ($tags as $tag) {
             try {
                tag::add($item, $tag);
              } catch (Exception $e) {
         	Kohana_Log::add("error", "Error adding tag: $tag\n" 
                              . $e->getMessage() . "\n" 
                              . $e->getTraceAsString());
            	}
	  }
    }
  }

  static function get($item) {
    $iptc = array();
    $record = ORM::factory("iptc_record")
      ->where("item_id", "=", $item->id)
      ->find();
    if (!$record->loaded()) {
      return array();
    }

    $definitions = self::keys();
    $keys = unserialize($record->data);
    foreach ($keys as $key => $value) {
      if (module::get_var("iptc", "show_".$key) == 1)
        $iptc[] = array("caption" => $definitions[$key][1], "value" => $value);
    }

    return $iptc;
  }


  public static function keys() {
    if (!isset(self::$iptc_keys)) {
      self::$iptc_keys = array(
        "ObjectName"             => array("2#005",
                                          t("IPTC Object Name"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/dc:title/rdf:Alt/rdf:li"                                                        ),
        "EditStatus"             => array("2#007",
                                          t("IPTC Edit Status"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@mediapro:Status"                                                               ),
        "Category"               => array("2#015",
                                          t("IPTC Category"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:Category"                                                            ),
        "SupplementalCategories" => array("2#020",
                                          t("IPTC Categories"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/photoshop:SupplementalCategories/rdf:Bag/rdf:li"                                ),
        "FixtureIdentifier"      => array("2#022",
                                          t("IPTC Identifier"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@mediapro:Event"                                                                ),
        "Keywords"               => array("2#025",
                                          t("IPTC Keywords"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/dc:subject/rdf:Bag/rdf:li"                                                      ),
        "LocationCode"           => array("2#026",
                                          t("IPTC Location Code"),
                                          null                                                                                                                ),
        "SpecialInstructions"    => array("2#040",
                                          t("IPTC Instructions"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:Instructions"                                                        ),
        "DateCreated"            => array("2#055",
                                          t("IPTC Created Date"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:DateCreated"                                                         ),
        "ByLine"                 => array("2#080",
                                          t("IPTC Author"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/dc:creator/rdf:Seq/rdf:li"                                                      ),
        "ByLineTitle"            => array("2#085",
                                          t("IPTC Author Title"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:AuthorsPosition"                                                     ),
        "City"                   => array("2#090",
                                          t("IPTC City"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:City"                                                                ),
        "SubLocation"            => array("2#092",
                                          t("IPTC SubLocation"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@Iptc4xmpCore:Location | /x:xmpmeta/rdf:RDF/rdf:Description/@mediapro:Location" ),
        "ProvinceState"          => array("2#095",
                                          t("IPTC Province State"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:State"                                                               ),
        "CountryCode"            => array("2#100",
                                          t("IPTC Country Code"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@Iptc4xmpCore:CountryCode"                                                      ),
        "CountryName"            => array("2#101",
                                          t("IPTC Country Name"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:Country"                                                             ),
        "Transmission"           => array("2#103",
                                          t("IPTC Transmission,"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:TransmissionReference"                                               ),
        "HeadLine"               => array("2#105",
                                          t("IPTC HeadLine"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:Headline"                                                            ),
        "Credit"                 => array("2#110",
                                          t("IPTC Credit"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:Credit | /x:xmpmeta/rdf:RDF/rdf:Description/photoshop:Credit"        ),
        "Source"                 => array("2#115",
                                          t("IPTC Source"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:Source"                                                              ),
        "Copyright"              => array("2#116",
                                          t("IPTC Copyright"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/dc:rights/rdf:Alt/rdf:li"                                                       ),
        "Contacts"               => array("2#118",
                                          t("IPTC Contacts"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/mediapro:People/rdf:Bag/rdf:li"                                                 ),        
        "Caption"                => array("2#120",
                                          t("IPTC Caption"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/dc:description/rdf:Alt/rdf:li"                                                  ),
        "Redactor"               => array("2#122",
                                          t("IPTC Redactor"),
                                          "/x:xmpmeta/rdf:RDF/rdf:Description/@photoshop:CaptionWriter"                                                       )
      );
    }
    return self::$iptc_keys;
  }


  static function stats() {
    $missing_iptc = db::build()
      ->select("items.id")
      ->from("items")
      ->join("iptc_records", "items.id", "iptc_records.item_id", "left")
      ->where("type", "=", "photo")
      ->and_open()
      ->where("iptc_records.item_id", "IS", null)
      ->or_where("iptc_records.dirty", "=", 1)
      ->close()
      ->execute()
      ->count();

    $total_items = ORM::factory("item")->where("type", "=", "photo")->count_all();
    if (!$total_items) {
      return array(0, 0, 0);
    }
    return array($missing_iptc, $total_items,
                 round(100 * (($total_items - $missing_iptc) / $total_items)));
  }

  static function check_index() {
    list ($remaining) = iptc::stats();
    if ($remaining) {
      site_status::warning(
        t('Your Iptc index needs to be updated.  <a href="%url" class="g-dialog-link">Fix this now</a>',
          array("url" => html::mark_clean(url::site("admin/maintenance/start/iptc_task::update_index?csrf=__CSRF__")))),
        "iptc_index_out_of_date");
    }
  }
}
