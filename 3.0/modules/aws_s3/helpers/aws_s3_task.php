<?php defined("SYSPATH") or die("No direct script access.");

class aws_s3_task_Core {

    static function available_tasks() {
        if (aws_s3::can_schedule())
            return array(Task_Definition::factory()
                             ->callback("aws_s3::schedule_full_sync")
                             ->name(t("Synchronise with Amazon S3"))
                             ->description(t("Schedule a task to synchronise your Gallery 3 data/images with your Amazon S3 bucket"))
                             ->severity(log::SUCCESS)
                             ->set_flags(Task_Definition::CAN_RUN_NOW));
        else
            return array(Task_Definition::factory()
                             ->callback("aws_s3_task::manual_sync")
                             ->name(t("Synchronise with Amazon S3"))
                             ->description(t("Synchronise your Gallery 3 data/images with your Amazon S3 bucket"))
                             ->severity(log::SUCCESS));

    }

    static function upload_item($task) {
        aws_s3::log("aws_s3_task::upload_item called");
        
        $item = ORM::factory("item", $task->get("item_id"));

        aws_s3::log("Commencing upload task for item " . $item->id);

        $task->status = "Commencing upload";
        $task->percent_complete = 0;
        $task->save();
        if (aws_s3::upload_item($item, aws_s3::get_upload_flags())) {
            $task->percent_complete = 100;
            $task->done = true;
            $task->state = "success";
            $task->status = "Upload complete";
        }
        else {
            $task->done = false;
            $task->state = "error";
            $task->status = "Upload failed";
        }
        $task->save();
    }

    static function manual_sync($task) {
        aws_s3::log("Amazon S3 manual re-sync started.");
        
        if (!$task->get("mode")) {
            $task->set("mode", "init");
        }

        aws_s3::log("mode: " . $task->get("mode"));
        switch ($task->get("mode")) {
            case "init": {
                batch::start();
                $items = ORM::factory("item")->find_all();
                $task->set("total_count", count($items));
                
                if (count($items) <= 50)
                    $task->set("batch", 1);
                else if (count($items) > 50 && count($items) <= 500)
                    $task->set("batch", 5);
                else if (count($items) > 500 && count($items) <= 5000)
                    $task->set("batch", 10);
                else if (count($items) > 5000)
                    $task->set("batch", 25);
                
                $task->set("completed", 0);
                $task->state = "running";
                
                if (!module::get_var("aws_s3", "synced", false)) {
                    $task->set("mode", "clean");
                    $task->status = "Emptying contents of bucket";
                }
                else {
                    $task->status = "Uploading items...";
                    $task->percent_complete = 10;
                    $task->set("mode", "upload");
                }
            } break;
            case "clean": {
                aws_s3::log("Emptying contents of bucket");

                require_once(MODPATH . "aws_s3/lib/s3.php");
                $s3 = new S3(module::get_var("aws_s3", "access_key"), module::get_var("aws_s3", "secret_key"));

                $bucket = module::get_var("aws_s3", "bucket_name");
                $resource = aws_s3::get_resource_url("");
                $stuff = array_reverse(S3::getBucket($bucket, $resource));
                $i = 0;
                foreach ($stuff as $uri => $item) {
                    $i++;
                    aws_s3::log("Removing " . $uri . " from S3");
                    S3::deleteObject($bucket, $uri);
                    $task->percent_complete = round(20 * ($i / count($stuff)));
                    $task->save();
                }
                $task->set("mode", "upload");
                $task->status = "Uploading items...";
            } break;
            case "upload": {
                $items = ORM::factory("item")->find_all($task->get("batch"), $task->get("completed"));
                foreach ($items as $item) {
                    aws_s3::upload_item($item, aws_s3::get_upload_flags());
                    $task->set("completed", $task->get("completed") + 1);
                }
                $task->percent_complete = (90 * ($task->get("completed") / $task->get("total_count"))) + 10;
                $task->status = "Uploaded " . $task->get("completed") . " of " . $task->get("total_count") . " items...";

                if ($task->get("completed") == $task->get("total_count")) {
                    $task->set("mode", "complete");
                }
            } break;
            case "complete": {
                $task->done = true;
                $task->state = "success";
                $task->percent_complete = 100;
                $task->status = "Completed.";
                module::set_var("aws_s3", "synced", true);
                site_status::clear("aws_s3_not_synced");
                batch::stop();
            } break;
        }
        aws_s3::log("End of function..");
        $task->save();
    }

    static function sync($task) {
        aws_s3::log("Amazon S3 Re-sync task started..");

        batch::start();
        $items = ORM::factory("item")->find_all();

        $task->set("total_count", count($items));
        $task->set("completed", 0);

        if (!module::get_var("aws_s3", "synced", false)) {
            aws_s3::log("Emptying contents of bucket");
            $task->status = "Emptying contents of bucket";
            $task->save();

            require_once(MODPATH . "aws_s3/lib/s3.php");
            $s3 = new S3(module::get_var("aws_s3", "access_key"), module::get_var("aws_s3", "secret_key"));

            $bucket = module::get_var("aws_s3", "bucket_name");
            $resource = aws_s3::get_resource_url("");
            $stuff = array_reverse(S3::getBucket($bucket, $resource));
            $i = 0;
            foreach ($stuff as $uri => $item) {
                $i++;
                aws_s3::log("Removing " . $uri . " from S3");
                S3::deleteObject($bucket, $uri);
                $task->percent_complete = round(20 * ($i / count($stuff)));
                $task->save();
            }
        }

        $task->percent_complete = 20;
        aws_s3::log("Commencing upload tasks");
        $task->state = "Commencing upload...";
        $task->save();

        $completed = $task->get("completed", 0);

        $items = ORM::factory("item")->find_all();
        foreach ($items as $item) {
            try {
                if ($item->id > 1)
                    aws_s3::upload_item($item, aws_s3::get_upload_flags());
            }
            catch (Exception $err) {}
            $completed++;

            $task->set("completed", $completed);
            $task->percent_complete = round(80 * ($completed / $task->get("total_count"))) + 20;
            $task->status = $completed . " of " . $task->get("total_count"). " uploaded.";
            $task->save();
        }

        $task->percent_complete = 100;
        $task->state = "success";
        $task->done = true;
        aws_s3::log("Sync task completed successfully");
        $task->status = "Sync task completed successfully";
        module::set_var("aws_s3", "synced", true);
        site_status::clear("aws_s3_not_synced");
        batch::stop();
        
        $task->save();
    }

}