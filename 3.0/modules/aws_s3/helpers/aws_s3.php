<?php defined("SYSPATH") or die("No direct script access.");

class aws_s3_Core {

    const UPLOAD_FULLSIZE = 1;
    const UPLOAD_RESIZE = 2;
    const UPLOAD_THUMB = 4;

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

    static function get_upload_flags() {
        $flags = 0;
        if (module::get_var("aws_s3", "upload_thumbs") == 1)
            $flags += self::UPLOAD_THUMB;
        if (module::get_var("aws_s3", "upload_resizes") == 1)
            $flags += self::UPLOAD_RESIZE;
        if (module::get_var("aws_s3", "upload_fullsizes") == 1)
            $flags += self::UPLOAD_FULLSIZE;
        return $flags;
    }
    
    static function upload_item($item, $flags = 7) {
        self::get_s3();

        $filename = urldecode($item->relative_path());
        $itype = "I";
        if ($item->is_album()) {
            $filename .= "/.album.jpg";
            $itype = "A";
        }

        if (!$item->s3_fullsize_uploaded && $flags & aws_s3::UPLOAD_FULLSIZE && !$item->is_album()) {
            aws_s3::log("[" . $itype . ":" . $item->id . "] Uploading fullsize object");
            $success_fs = S3::putObjectFile(VARPATH . "albums/" . $filename,
                                            module::get_var("aws_s3", "bucket_name"),
                                            self::get_resource_url("fs/" . $filename),
                                            ($item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
            $item->s3_fullsize_uploaded = $success_fs;
        }
        else
            $success_fs = true;

        if (!$item->s3_resize_uploaded && $flags & aws_s3::UPLOAD_RESIZE && !$item->is_album()) {
            aws_s3::log("[" . $itype . ":" . $item->id . "] Uploading resize object");
            $success_rs = S3::putObjectFile(VARPATH . "resizes/" . $filename,
                                            module::get_var("aws_s3", "bucket_name"),
                                            self::get_resource_url("rs/" . $filename),
                                            ($item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
            $item->s3_resize_uploaded = $success_rs;
        }
        else
            $success_rs = true;

        if (!$item->s3_thumb_uploaded && $flags & aws_s3::UPLOAD_THUMB) {
            aws_s3::log("[" . $itype . ":" . $item->id . "] Uploading thumbnail object");
            $success_th = S3::putObjectFile(VARPATH . "thumbs/" . $filename,
                                            module::get_var("aws_s3", "bucket_name"),
                                            self::get_resource_url("th/" . $filename),
                                            ($item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
            $item->s3_thumb_uploaded = $success_th;
        }
        else
            $success_th = true;

        $item->s3_item_hash = md5($item->relative_path());

        $item->save();

        $success = $success_fs && $success_th && $success_rs;
        aws_s3::log("item upload success: " . $success);
        return $success;
    }

    static function move_item($old_item, $new_item) {
        self::get_s3();

        $old_filename = urldecode($old_item->relative_path());
        $new_filename = urldecode($new_item->relative_path());

        aws_s3::log("old filename: " . self::get_resource_url("fs/" . $old_filename) . ", " .
                    "new filename: " . self::get_resource_url("fs/" . $new_filename));

        //aws_s3::log($old_item->get_aws_s3_meta());
        
        if ($old_item->s3_fullsize_uploaded) {
            aws_s3::log("Copying fullsize " . $old_filename . " to " . $new_filename);
            S3::copyObject(module::get_var("aws_s3", "bucket_name"), self::get_resource_url("fs/" . $old_filename),
                           module::get_var("aws_s3", "bucket_name"), self::get_resource_url("fs/" . $new_filename),
                           ($new_item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
            S3::deleteObject(module::get_var("aws_s3", "bucket_name"), self::get_resource_url("fs/" . $old_filename));
        }
        else
            aws_s3::upload_item($new_item, aws_s3::UPLOAD_FULLSIZE);

        if ($old_item->s3_resize_uploaded) {
            aws_s3::log("Copying resized " . $old_filename . " to " . $new_filename);
            S3::copyObject(module::get_var("aws_s3", "bucket_name"), self::get_resource_url("rs/" . $old_filename),
                           module::get_var("aws_s3", "bucket_name"), self::get_resource_url("rs/" . $new_filename),
                           ($new_item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
            S3::deleteObject(module::get_var("aws_s3", "bucket_name"), self::get_resource_url("rs/" . $old_filename));
        }
        else
            aws_s3::upload_item($new_item, aws_s3::UPLOAD_RESIZE);

        if ($old_item->s3_thumb_uploaded) {
            aws_s3::log("Copying thumbnail " . $old_filename . " to " . $new_filename);
            S3::copyObject(module::get_var("aws_s3", "bucket_name"), self::get_resource_url("th/" . $old_filename),
                           module::get_var("aws_s3", "bucket_name"), self::get_resource_url("th/" . $new_filename),
                           ($new_item->view_1 ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE));
            S3::deleteObject(module::get_var("aws_s3", "bucket_name"), self::get_resource_url("th/" . $old_filename));
        }
        else
            aws_s3::upload_item($new_item, aws_s3::UPLOAD_THUMB);
    }

    static function remove_item($item) {
        self::get_s3();

        $filename = urldecode($item->relative_path());
        $itype = "I";
        if ($item->is_album()) {
            $filename .= "/.album.jpg";
            $itype = "A";
        }

        if ($item->s3_fullsize_uploaded && !$item->is_album()) {
            aws_s3::log("[" . $itype . ":" . $item->id . "] Deleting fullsize object");
            $success_fs = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                           self::get_resource_url("fs/" . $filename));
            $item->s3_fullsize_uploaded = !$success_fs;
        }
        else
            $success_fs = true;

        if ($item->s3_resize_uploaded && !$item->is_album()) {
            aws_s3::log("[" . $itype . ":" . $item->id . "] Deleting resize object");
            $success_rs = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                           self::get_resource_url("rs/" . $filename));
            $item->s3_resize_uploaded = !$success_rs;
        }
        else
            $success_rs = true;

        if ($item->s3_thumb_uploaded) {
            aws_s3::log("[" . $itype . ":" . $item->id . "] Deleting thumbnail object");
            $success_th = S3::deleteObject(module::get_var("aws_s3", "bucket_name"),
                                           self::get_resource_url("th/" . $filename));
            $item->s3_thumb_uploaded = !$success_th;
        }
        else
            $success_th = true;
        
        $item->save_s3_meta();

        $success = $success_fs && $success_th && $success_rs;
        aws_s3::log("S3 delete success: " . $success);
        return $success;
    }

    static function getAuthenticatedURL($bucket, $uri) {
        self::get_s3();

        return S3::getAuthenticatedURL($bucket, $uri, 60);
    }

    static function validate_number($field) {
        if (preg_match("/\D/", $field->value))
            $field->add_error("not_numeric", 1);
    }

    static function validate_bucket($field) {
        if (preg_match("/[^a-zA-Z0-9\-\.]/", $field->value))
            $field->add_error("invalid", 1);
    }

    // @TODO: Write validation function (check with S3)
    static function validate_access_details($access_key, $secret_key, $bucket_name) {
        require_once(MODPATH . "aws_s3/lib/s3.php");
        S3::setAuth($access_key, $secret_key);
        S3::$useSSL = false;

        $success_test = S3::putObjectString((string)time(), $bucket_name, ".s3_test");
        if ($success_test)
            S3::deleteObject($bucket_name, ".s3_test");

        return $success_test;
    }

    static function base64_filename(Item_Model $item) {
        $file_path = explode("/", $item->relative_path());
        return base64_encode(end($file_path));
    }

    static function can_schedule() {
        if (!module::is_active("scheduler")) {
            return false;
        }
        
        return true;
    }

    static function schedule_task($task) {
        $schedule = ORM::factory("schedule");
        $schedule->add_task($task);
    }

    static function schedule_full_sync2() {
        $task_def =
            Task_Definition::factory()
                ->callback("aws_s3_task::sync")
                ->name("Amazon S3 bucket synchronisation")
                ->severity(log::SUCCESS);

        $task = task::create($task_def, array());
        self::schedule_task($task);
    }

    static function schedule_full_sync($this_task) {
        if (!self::can_schedule())
            throw new Exception("Unable to initialize schedule");

        try {
            self::schedule_full_sync2();

            $this_task->status = "Scheduled re-sync task";
            $this_task->done = true;
            $this_task->state = "success";
            $this_task->percent_complete = 100;
        }
        catch (Exception $err) {
            $task->done = true;
            $thisSynchronise_task->state = "error";
            $this_task->status = $err->getMessage();
            $this_task->log((string)$err);
        }

        $this_task->save();

        if (!module::get_var("aws_s3", "synced", false)) {
            site_status::warning(
                "Your site has been scheduled for full Amazon S3 re-synchronisation. This message will clear when this has been completed.",
                "aws_s3_not_synced"
            );
        }

        return true;
    }

    static function schedule_item_sync($item) {
        if (!self::can_schedule())
            throw new Exception("Unable to initialize schedule");
        
        $item_id = null;
        if (is_object($item) && $item instanceof Item_Model)
            $item_id = $item->id;
        else if (is_numeric($item))
            $item_id = $item;
        else
            throw new Exception("Un-intelligible item reference passed.");

        $task_def =
            Task_Definition::factory()
                ->callback("aws_s3_task::upload_item")
                ->name("Amazon S3 item upload (ID: " . $item_id . ")")
                ->severity(log::SUCCESS);

        $task = task::create($task_def, array("item_id" => $item_id));

        self::schedule_task($task);
    }


}