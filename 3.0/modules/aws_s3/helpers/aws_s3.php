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

class aws_s3_Core {

    private static $_s3;

    static function get_s3() {
        if (!self::$_s3) {
            require_once(MODPATH . "aws_s3/lib/s3.php");
            S3::setAuth(module::get_var("aws_s3", "access_key"), module::get_var("aws_s3", "secret_key"));
            S3::$useSSL = module::get_var("aws_s3", "use_ssl", false);
        }
        return self::$_s3;
    }

    static function getHash($string) {
        return base64_encode(extension_loaded('hash') ?
            hash_hmac('sha1', $string, module::get_var("aws_s3", "secret_key"), true) : pack('H*', sha1(
            (str_pad(module::get_var("aws_s3", "secret_key"), 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
            pack('H*', sha1((str_pad(module::get_var("aws_s3", "secret_key"), 64, chr(0x00)) ^
            (str_repeat(chr(0x36), 64))) . $string)))));
    }

    static function generate_url($resource, $authenticated = false, $updated = null) {
        $find = array("{guid}", "{bucket}", "{resource}");
        $replace = array(module::get_var("aws_s3", "g3id"), module::get_var("aws_s3", "bucket_name"), $resource);
        $url = str_replace($find, $replace, module::get_var("aws_s3", "url_str"));

        if ($authenticated) {
            preg_match("%https?://([a-zA-Z0-9\.-]*)/(.*)$%", $url, $matches);
            $host = module::get_var("aws_s3" , "bucket_name");
            $resource = $matches[2];
            $url .= "?AWSAccessKeyId=" . module::get_var("aws_s3", "access_key") .
                    "&Expires=" . (time() + module::get_var("aws_s3", "sig_exp")) .
                    "&Signature=" . urlencode(self::getHash("GET\n\n\n" . (time() + module::get_var("aws_s3", "sig_exp")) . "\n/" . $host . "/" . $resource));

            self::get_s3();
            S3::getAuthenticatedURL(module::get_var("aws_s3", "bucket_name"), $resource, module::get_var("aws_s3", "sig_exp"));
        }
        else
            $url .= "?m=" . ($updated ? $updated : time());

        return $url;
    }

    static function get_resource_url($resource) {
        $url = self::generate_url($resource);
        preg_match("%https?://[\w\.\-]*/(.*)\?%", $url, $matches);
        if (count($matches) > 0)
            return $matches[1];
        return false;
    }

    static function log($item) {
        if (is_string($item) || is_numeric($item)) {}
        else
            $item = print_r($item, true);

        $fh = fopen(VARPATH . "modules/aws_s3/log/aws_s3-" . date("Y-m-d") . ".log", "a");
        fwrite($fh, date("Y-m-d H:i:s") . ": " . $item .  "\n");
        fclose($fh);
    }

    static function upload_item($item) {
        self::get_s3();

        $success_fs = S3::putObjectFile(VARPATH . "albums/" . $item->relative_path(),
                                       module::get_var("aws_s3", "bucket_name"),
                                       self::get_resource_url("fs/" . $item->relative_path()),
                                       ($item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        $success_th = S3::putObjectFile(VARPATH . "thumbs/" . $item->relative_path(),
                                       module::get_var("aws_s3", "bucket_name"),
                                       self::get_resource_url("th/" . $item->relative_path()),
                                       ($item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        $success_rs = S3::putObjectFile(VARPATH . "resizes/" . $item->relative_path(),
                                       module::get_var("aws_s3", "bucket_name"),
                                       self::get_resource_url("rs/" . $item->relative_path()),
                                       ($item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));

        $success = $success_fs && $success_th && $success_rs;
        aws_s3::log("item upload success: " . $success);
    }

    static function move_item($old_item, $new_item) {
        self::get_s3();

        S3::copyObject(module::get_var("aws_s3", "bucket_name"),
                       self::get_resource_url("fs/" . $old_item->relative_path()),
                       module::get_var("aws_s3", "bucket_name"),
                       self::get_resource_url("fs/" . $new_item->relative_path()),
                       ($new_item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                         self::get_resource_url("fs/" . $old_item->relative_path()));

        S3::copyObject(module::get_var("aws_s3", "bucket_name"),
                       self::get_resource_url("rs/" . $old_item->relative_path()),
                       module::get_var("aws_s3", "bucket_name"),
                       self::get_resource_url("rs/" . $new_item->relative_path()),
                       ($new_item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                         self::get_resource_url("rs/" . $old_item->relative_path()));

        S3::copyObject(module::get_var("aws_s3", "bucket_name"),
                       self::get_resource_url("th/" . $old_item->relative_path()),
                       module::get_var("aws_s3", "bucket_name"),
                       self::get_resource_url("th/" . $new_item->relative_path()),
                       ($new_item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                         self::get_resource_url("th/" . $old_item->relative_path()));
    }

    static function remove_item($item) {
        self::get_s3();

        $success_fs = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                       self::get_resource_url("fs/" . $item->relative_path()));
        $success_th = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                       self::get_resource_url("th/" . $item->relative_path()));
        $success_rs = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                       self::get_resource_url("rs/" . $item->relative_path()));

        $success = $success_fs && $success_th && $success_rs;
        aws_s3::log("s3 delete success: " . $success);
    }

    static function upload_album_cover($album) {
        self::get_s3();

        if (file_exists(VARPATH . "resizes/" . $album->relative_path() . "/.album.jpg"))
            $success_rs = S3::putObjectFile(VARPATH . "resizes/" . $album->relative_path() . "/.album.jpg",
                                           module::get_var("aws_s3", "bucket_name"),
                                           "g3/" . module::get_var("aws_s3", "g3id") . "/rs/" . $album->relative_path() . "/.album.jpg",
                                           ($album->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        else
            $success_rs = true;

        if (file_exists(VARPATH . "thumbs/" . $album->relative_path() . "/.album.jpg"))
            $success_th = S3::putObjectFile(VARPATH . "thumbs/" . $album->relative_path() . "/.album.jpg",
                                           module::get_var("aws_s3", "bucket_name"),
                                           "g3/" . module::get_var("aws_s3", "g3id") . "/th/" . $album->relative_path() . "/.album.jpg",
                                           ($album->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
        else
            $success_th = true;

        $success = $success_rs && $success_th;
        aws_s3::log("album cover upload success: " . $success);
    }

    static function remove_album_cover($album) {
        self::get_s3();

        $success_th = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                      "g3/" . module::get_var("aws_s3", "g3id") . "/th/" . $album->relative_path() . "/.album.jpg");
        $success_rs = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                      "g3/" . module::get_var("aws_s3", "g3id") . "/rs/" . $album->relative_path() . "/.album.jpg");

        $success = $success_rs && $success_th;
        aws_s3::log("album cover removal success: " . $success);
    }

    static function getAuthenticatedURL($bucket, $uri) {
        self::get_s3();

        return S3::getAuthenticatedURL($bucket, $uri, 60);
    }

    static function validate_number($field) {
        if (preg_match("/\D/", $field->value))
                $field->add_error("not_numeric", 1);
    }


}