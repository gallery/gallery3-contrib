<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class Admin_Strip_Exif_Controller extends Admin_Controller {
    public static $defExifTags = "GPSInfo.GPSVersionID GPSInfo.GPSSatellites GPSInfo.GPSStatus GPSInfo.GPSMeasureMode GPSInfo.GPSDOP GPSInfo.GPSMapDatum GPSInfo.GPSLatitudeRef GPSInfo.GPSLatitude GPSInfo.GPSLongitudeRef GPSInfo.GPSLongitude GPSInfo.GPSAltitude GPSInfo.GPSAltitudeRef GPSInfo.GPSImgDirectionRef GPSInfo.GPSImgDirection GPSInfo.GPSDestLatitudeRef GPSInfo.GPSDestLatitude GPSInfo.GPSDestLongitudeRef GPSInfo.GPSDestLongitude GPSInfo.GPSDestBearingRef GPSInfo.GPSDestBearing";
    public static $defIptcTags = "Application2.LocationName";

    public function verify() {
        $data = array();
        $data['success'] = false;

        if (($val = strip_exif::verify_path($_REQUEST['exiv_path'])) > 0) {
            module::set_var("strip_exif", "exiv_path", $_REQUEST['exiv_path']);
            $data['success'] = true;
        } else {
            $error = "";
            switch ($val) {
                case 0: $error = "Empty file path provided"; break;
                case -1: $error = "File does not exist"; break;
                case -2: $error = "Path is a directory"; break;
                default: $error = "Unspecified error";
            }
            $data['error'] = $error;
        }

        echo json_encode($data);
    }

    public function index() {
        $form = $this->_get_form();

        if (request::method() == "post") {
            access::verify_csrf();

            if ($form->validate()) {
                module::set_var("strip_exif", "exiv_path", $_POST['exiv_path']);

		if ($_POST['exif_tags'] != "") {
		    module::set_var("strip_exif", "exif_remove", (isset($_POST['exif_remove']) ? $_POST['exif_remove'] : false));
		    module::set_var("strip_exif", "exif_tags", $_POST['exif_tags']);
		} else {
		    module::set_var("strip_exif", "exif_remove", false);
		    module::set_var("strip_exif", "exif_tags", self::$defExifTags);
		}

		if ($_POST['iptc_tags'] != "") {
		    module::set_var("strip_exif", "iptc_remove", (isset($_POST['iptc_remove']) ? $_POST['iptc_remove'] : false));
		    module::set_var("strip_exif", "iptc_tags", $_POST['iptc_tags']);
		} else {
		    module::set_var("strip_exif", "iptc_remove", false);
		    module::set_var("strip_exif", "iptc_tags", self::$defIptcTags);
		}

                if (isset($_POST['verbose']))
                    module::set_var("strip_exif", "verbose", $_POST['verbose']);

                message::success(t("Settings have been saved"));
                url::redirect("admin/strip_exif");
            } else {
                message::error(t("There was a problem with the submitted form. Please check your values and try again."));
            }
        }

        print $this->_get_view();
    }

    private function _get_view($form = null) {
        $v = new Admin_View("admin.html");
        $v->page_title = t("Gallery 3 :: Manage Strip EXIF/IPTC Settings");

        $v->content = new View("admin_strip_exif.html");
        $v->content->form = empty($form) ? $this->_get_form() : $form;

        return $v;
    }

    private function _get_form() {
        $form = new Forge("admin/strip_exif", "", "post", array("id" => "g-admin-strip_exif-form"));

        $group = $form->group("system")->label(t("System"));

        $exivPath = strip_exif::whereis("exiv2");
        $group  ->input("exiv_path")
                ->id("exiv_path")
                ->label(t("Path to exiv2 binary:"))
                ->value(module::get_var("strip_exif", "exiv_path", $exivPath))
                ->callback("strip_exif::verify_exiv_path")
                ->error_messages("required", t("You must enter the path to exiv2"))
                ->error_messages("invalid", t("File does not exist"))
                ->error_messages("is_dir", t("File is a directory"))
                ->message("Auto detected exiv2 here: " . $exivPath);


        $group = $form->group("tags")->label(t("Tags"));

        $group  ->checkbox("exif_remove")
                ->label(t("Strip these EXIF tags:"))
                ->checked(module::get_var("strip_exif", "exif_remove", true) == 1);
	if (($exifTags = module::get_var("strip_exif", "exif_tags", self::$defExifTags)) == "")
	    $exifTags = self::$defExifTags;
        $group  ->input("exif_tags")
                ->id("exif_tags")
                ->value($exifTags);

        $group  ->checkbox("iptc_remove")
                ->label(t("Strip these IPTC tags:"))
                ->checked(module::get_var("strip_exif", "iptc_remove", true) == 1);
	if (($iptcTags = module::get_var("strip_exif", "iptc_tags", self::$defIptcTags)) == "")
	    $iptcTags = self::$defIptcTags;
        $group  ->input("iptc_tags")
                ->id("iptc_tags")
                ->value($iptcTags);

        $form->submit("submit")->value(t("Save"));
        return $form;
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
