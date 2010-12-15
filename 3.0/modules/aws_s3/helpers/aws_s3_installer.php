<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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

class aws_s3_installer {

    private static function getversion() { return 1; }
    private static function setversion() { module::set_version("aws_s3", self::getversion()); }

    static function install() {
        @mkdir(VARPATH . "modules/aws_s3");
        @mkdir(VARPATH . "modules/aws_s3/log");

        // installation's unique identifier - allows multiple g3's pointing to the same s3 bucket.
        if (!module::get_var("aws_s3", "g3id"))
            module::set_var("aws_s3", "g3id", md5(time()));

        module::set_var("aws_s3", "synced", false);
        module::set_var("aws_s3", "enabled", false);
        module::set_var("aws_s3", "access_key", "");
        module::set_var("aws_s3", "secret_key", "");
        module::set_var("aws_s3", "bucket_name", "");

        self::setversion();
    }
    static function uninstall() {
        dir::unlink(VARPATH . "modules/aws_s3");
    }

    static function upgrade($version) {
        if ($version < self::getversion())
            self::setversion();
    }

    static function deactivate() {}
    static function activate() {}
    static function can_activate() {
        $messages = array();
        return $messages;
    }

}