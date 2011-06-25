<?php defined("SYSPATH") or die("No direct script access.");

class aws_s3_installer {

    static function install() {
        self::upgrade(0);
    }
    
    static function uninstall() {
        dir::unlink(VARPATH . "modules/aws_s3");
        Database::instance()->query("DROP TABLE {aws_s3_meta}");
    }

    static function upgrade($version) {
        log::info("aws_s3", "Commencing module upgrade (" . $version . ")");
        switch ($version) {
            case 0: {
                log::info("aws_s3", "Installing version 1");

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
                
                module::set_version("aws_s3", 1);
            }
            case 1: {
                log::info("aws_s3", "Upgrading to version 2");
                $db = Database::instance();
                $db->query("CREATE TABLE {aws_s3_meta} (
                                `item_id` int(9) NOT NULL,
                                `item_hash` varchar(32) NOT NULL DEFAULT '',
                                `thumb_uploaded` smallint(1) NOT NULL DEFAULT 0,
                                `resize_uploaded` smallint(1) NOT NULL DEFAULT 0,
                                `fullsize_uploaded` smallint(1) NOT NULL DEFAULT 0,
                                `local_deleted` smallint(1) NOT NULL DEFAULT 0,
                                PRIMARY KEY (`item_id`)
                ) DEFAULT CHARSET=utf8;");

                module::set_var("aws_s3", "upload_thumbs", true);
                module::set_var("aws_s3", "upload_resizes", true);
                module::set_var("aws_s3", "upload_fullsizes", true);
                module::set_var("aws_s3", "s3_storage_only", false);

                if (module::get_var("aws_s3", "synced")) {
                    // v1 has already synced this installation to s3. mark all the items with the relevant meta data
                    $items = ORM::factory("item")->find_all();
                    foreach ($items as $item) {
                        aws_s3::log("Updating S3 meta for item ID: " . $item->id);
                        $item->s3_thumb_uploaded = true;
                        if (!$item->is_album()) {
                            $item->s3_resize_uploaded = true;
                            $item->s3_fullsize_uploaded = true;
                        }
                        $item->s3_local_deleted = false;
                        $item->s3_item_hash = md5($item->relative_path());
                        $item->save_s3_meta();
                    }
                }
                else {
                    // check various states after upgrade from v1..

                    if (module::get_var("aws_s3", "access_key") != "" &&
                        module::get_var("aws_s3", "secret_key") != "" &&
                        module::get_var("aws_s3", "bucket_name") != "" &&
                        aws_s3::validate_access_details(module::get_var("aws_s3", "access_key"),
                                                        module::get_var("aws_s3", "secret_key"),
                                                        module::get_var("aws_s3", "bucket_name"))
                    ) {
                        // details are correct but hasn't been synced.
                        if (aws_s3::can_schedule()) {
                            // i can schedule this task
                            aws_s3::schedule_full_sync2();
                            site_status::warning(
                                "Your site has been scheduled for full Amazon S3 re-synchronisation. This message will clear when this has been completed.",
                                "aws_s3_not_synced"
                            );
                        }
                        else {
                            // i CAN'T schedule it..
                            site_status::warning(
                                t('Your site has not been synchronised to Amazon S3. Until it has, your server will continue to serve image content to your visitors.<br />Click <a href="%url" class="g-dialog-link">here</a> to start the synchronisation task.',
                                  array("url" => html::mark_clean(url::site("admin/maintenance/start/aws_s3_task::manual_sync?csrf=__CSRF__")))
                                ),
                                "aws_s3_not_synced"
                            );
                        }
                    }
                    else {
                        site_status::warning(
                            t('Amazon S3 module needs configuration. Click <a href="%url">here</a> to go to the configuration page.',
                              array("url" => html::mark_clean(url::site("admin/aws_s3")))
                            ),
                            "aws_s3_not_configured"
                        );
                    }
                }

                module::set_version("aws_s3", 2);
            }
        }
        log::info("aws_s3", "Module upgrade complete");
    }

    static function deactivate() {}
    static function activate() {}
    static function can_activate() {
        $messages = array();
        if (!function_exists("curl_init")) {
            $messages['error'][] = "The S3 library (and this module) depend on the php5-curl extension. Please install this extension and try again.";
        }
        if (!module::is_active("scheduler")) {
            $messages['warn'][] = "The 'Scheduler' module is not installed/active. Scheduled maintenance tasks such as synchronisation will not be available.";
        }
        return $messages;
    }

}