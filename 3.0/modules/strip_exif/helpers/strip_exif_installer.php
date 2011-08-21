<?php defined("SYSPATH") or die("No direct script access.");

class strip_exif_installer {
    private static function getversion() { return 1; }
    private static function setversion() { module::set_version("strip_exif", self::getversion()); }

    static function install() {
        self::setversion();

        @mkdir(VARPATH . "modules/strip_exif");
        @mkdir(VARPATH . "modules/strip_exif/log");
    }

    static function upgrade($version) {
        if ($version < self::getversion())
            self::setversion();
    }

    static function can_activate() {
        $messages = array();
        if (!function_exists("exec")) {
            $messages["warn"][] = t("exec() is required to auto-detect the exiv2 binary. You must specify the path to the exiv2 binary manually.");
        }
        return $messages;
    }

    static function activate() {
    }

    static function deactivate() {
    }

    static function uninstall() {
        dir::unlink(VARPATH . "modules/strip_exif");
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
