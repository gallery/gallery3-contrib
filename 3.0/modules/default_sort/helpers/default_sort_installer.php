<?php defined("SYSPATH") or die("No direct script access.");

class default_sort_installer {
    private static function getversion() { return 1; }
    private static function setversion() { module::set_version("default_sort", self::getversion()); }

    static function install() {
        self::setversion();
    }

    static function upgrade($version) {
        if ($version < self::getversion())
            self::setversion();
    }

    static function can_activate() {
    }

    static function activate() {
    }

    static function deactivate() {
    }

    static function uninstall() {
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
