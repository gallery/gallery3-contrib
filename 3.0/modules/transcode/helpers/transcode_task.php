<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class transcode_task_Core {
    
    static $duration = 0;

    static function available_tasks() {
        return array();
    }
    static function transcode($task) {
        transcode::log("Transcoding task " . $task->id . " started.");

        $cmd = $task->get("ffmpeg_cmd");

        $logfile = VARPATH . "modules/transcode/log/" . time() . ".log";
        $task->set("logfile", $logfile);

        $output = "";
        $return_val = 0;
        self::fork($cmd, $logfile);

        // wait for ffmpeg to fire up..
        sleep(2);

        while ($time = self::GetEncodedTime($logfile)) {
            transcode::log("encoded time: " . $time);

            if (self::$duration > 0) {
                $task->state = "encoding";
                $task->status = "Encoding...";
                $pct = sprintf("%0.0f", ($time / self::$duration) * 100);
                $task->percent_complete = $pct;
                $task->save();
            }
            usleep(500000);
        }

        $output = @file_get_contents($logfile);

        transcode::log("ffmpeg job completed.");

        if ($output) {
            $task->percent_complete = 100;
            $task->done = true;
            $task->state = "success";
            $task->status = "Transcoding complete.";

            transcode::log("insert into transcode table to indicate success");
            $res = ORM::factory('transcode_resolution');
            $res->resolution = $task->get("width") . "x" . $task->get("height");
            $res->item_id = $task->get("item_id");
            $res->save();
        }
        else {
            transcode::log("Error transcoding. ffmpeg output:");
            transcode::log($output);
            $task->percent_complete = 100;
            $task->done = true;
            $task->state = "error";
            $task->status = "Transcoding error.";
        }
        $task->save();

    }

    static function fork($shellCmd, $logfile) {
        $cmd = "nice " . $shellCmd . " > " . $logfile . " 2>&1 &";
        transcode::log("executing: " . $cmd);
        exec($cmd);
    }

    static function GetEncodedTime($logfile) {
        if (!file_exists($logfile)) {
            transcode::log("can't open FFMPEG-Log '" . $logfile . "'");
            return false;
        }
        else {
            $FFMPEGLog = @file_get_contents($logfile);

            $dPos = strpos($FFMPEGLog, " Duration: ");
            self::$duration = self::durationToSecs(substr($FFMPEGLog, $dPos + 11, 11));

            $FFMPEGLog = str_replace("\r", "\n", $FFMPEGLog);

            $lines = explode("\n", $FFMPEGLog);
            $line = $lines[count($lines) - 2];

            if ($tpos = strpos($line, "time=")) {
                $bpos = strpos($line, " bitrate=");

                $time = substr($line, $tpos + 5, $bpos - ($tpos + 5));
                return $time;
            }
            else {
                return false;
            }
        }
    }

    static function durationToSecs($durstr) {
        list($hr, $min, $sec) = explode(":", $durstr);

        $secs = $hr * 3600;
        $secs += $min * 60;
        $secs += $sec;

        return $secs;
    }
}
