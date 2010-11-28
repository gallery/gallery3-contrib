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

class aws_s3_task_Core {

    static function available_tasks() {
          return array(Task_Definition::factory()
                   ->callback("aws_s3_task::sync")
                   ->name(t("Syncronise with Amazon S3"))
                   ->description(t("Syncronise your Gallery 3 data/images with your Amazon S3 bucket"))
                   ->severity(log::SUCCESS));
    }

    static function sync($task) {
        require_once(MODPATH . "aws_s3/lib/s3.php");
        $s3 = new S3(module::get_var("aws_s3", "access_key"), module::get_var("aws_s3", "secret_key"));
        
        $mode = $task->get("mode", "init");
        switch ($mode) {
            case "init": {
                aws_s3::log("re-sync task started..");
                batch::start();
                $items = ORM::factory("item")->find_all();
                aws_s3::log("items to sync: " . count($items));
                $task->set("total_count", count($items));
                $task->set("completed", 0);
                $task->set("mode", "empty");
                $task->status = "Emptying contents of bucket";
            } break;
            case "empty": { // 0 - 10%
                aws_s3::log("emptying bucket contents (any files that may already exist in the bucket/prefix path)");
                $bucket = module::get_var("aws_s3", "bucket_name");

                $resource = aws_s3::get_resource_url("");
                $stuff = array_reverse(S3::getBucket($bucket, $resource));
                foreach ($stuff as $uri => $item) {
                    aws_s3::log("removing: " . $uri);
                    S3::deleteObject($bucket, $uri);
                }
                $task->percent_complete = 10;
                $task->set("mode", "upload");
                $task->state = "Commencing upload...";
            } break;
            case "upload": { // 10 - 100%
                $completed = $task->get("completed", 0);
                $items = ORM::factory("item")->find_all(1, $completed);
                foreach ($items as $item) {
                    if ($item->id > 1) {
                        aws_s3::log("uploading item " . $item->id . " (" . ($completed + 1)  . "/" . $task->get("total_count") . ")");
                        if ($item->is_album())
                            aws_s3::upload_album_cover($item);
                        else
                            aws_s3::upload_item($item);
                    }
                    $completed++;
                }
                $task->set("completed", $completed);
                $task->percent_complete = round(90 * ($completed / $task->get("total_count"))) + 10;
                $task->status = $completed . " of " . $task->get("total_count"). " uploaded.";

                if ($completed == $task->get("total_count")) {
                    $task->set("mode", "finish");
                }
            } break;
            case "finish": {
                aws_s3::log("completing upload task..");
                $task->percent_complete = 100;
                $task->state = "success";
                $task->done = true;
                $task->status = "Sync task completed successfully";
                batch::stop();
                module::set_var("aws_s3", "synced", true);
                site_status::clear("aws_s3_not_synced");
            } break;
        }
        
    }

}