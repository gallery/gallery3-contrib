<?php defined("SYSPATH") or die("No direct script access.");

class strip_exif_Core {
    static function run_exiv($base_cmd, $file) {
        if (module::get_var("strip_exif", "verbose", 0) >= 1)
            $logfile = VARPATH . "modules/strip_exif/log/strip_exif.log";
        else
            $logfile = "/dev/null";

        self::log(1, "Stripping '" . $file . "'");

        $cmd = $base_cmd . " \"" . $file . "\" >> " . $logfile . " 2>&1";
        self::log(3, "  " . $cmd);
        exec($cmd);
    }

    static function strip($item) {
        if (!($item->is_photo() && $item->mime_type == "image/jpeg"))
            return;

        $tags = "";
        if (module::get_var("strip_exif", "exif_remove", false)) {
            $tags .= "Exif.";
            $tags .= preg_replace("/[\s,]+/", " Exif.", module::get_var("strip_exif", "exif_tags", "")) . " ";
        }
        if (module::get_var("strip_exif", "iptc_remove", false)) {
            $tags .= "Iptc.";
            $tags .= preg_replace("/[\s,]+/", " Iptc.", module::get_var("strip_exif", "iptc_tags", "")) . " ";
        }
        $tagList = preg_split("/[\s]+/", $tags, -1, PREG_SPLIT_NO_EMPTY);

        $base_cmd = module::get_var("strip_exif", "exiv_path") . " ";
        self::log(1, "Stripping tags from item id " . $item->id);
        foreach ($tagList as $tag) {
            $base_cmd .= "-M\"del " . $tag . "\"" . " ";
            self::log(2, "  " . $tag);
        }
        self::log(2, "Using command:");
        self::log(2, "  " . $base_cmd);

        foreach(array($item->file_path(), $item->resize_path(), $item->thumb_path()) as $file) {
            self::run_exiv($base_cmd, $file);
        }

        $parent = $item->parent();
        if ($parent->album_cover_item_id == $item->id) {
            self::run_exiv($base_cmd, $parent->thumb_path());
        }

        self::log(1, "Successfully stripped tags from item id " . $item->id);
    }

    static function whereis($app) {
        $op = @shell_exec("whereis " . $app);
        if ($op != "") {
            $op = explode(" ", $op);
            for ($i = 1; $i < count($op); $i++) {
                if (file_exists($op[$i]) && !is_dir($op[$i]))
                    return $op[$i];
            }
        }
        return false;
    }

    static function verify_path($path) {
        if ($path == "")
            return 0;
        else if (!file_exists($path))
            return -1;
        else if (is_dir($path))
            return -2;

        return 1;
    }

    static function verify_exiv_path($field) {
        $v = self::verify_path($field->value);
        switch ($v) {
            case 0: $field->add_error("required", 1); break;
            case -1: $field->add_error("invalid", 1); break;
            case -2: $field->add_error("is_dir", 1); break;
        }
    }

    static function log($verbosity, $item) {
        if (module::get_var("strip_exif", "verbose", 0) < $verbosity)
            return;

        if (is_string($item) || is_numeric($item)) {}
        else
            $item = print_r($item, true);

        $fh = fopen(VARPATH . "modules/strip_exif/log/strip_exif.log", "a");
        fwrite($fh, date("Y-m-d H:i:s") . ": " . $item .  "\n");
        fclose($fh);
    }
}

# vim: tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
