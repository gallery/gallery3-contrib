<?php

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