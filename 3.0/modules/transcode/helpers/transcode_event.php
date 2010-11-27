<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class transcode_event_Core {

    static function admin_menu($menu, $theme) {
        $menu
            ->get("settings_menu")
            ->append(Menu::factory("link")
            ->id("transcode_menu")
            ->label(t("Video Transcoding"))
            ->url(url::site("admin/transcode")));
    }

    static function item_deleted($item) {
        if ($item->is_movie()) {
            transcode::log("Deleting transcoded files for item " . $item->id);
            if (is_dir(VARPATH . "modules/transcode/flv/" . $item->id)) {
                self::rrmdir(VARPATH . "modules/transcode/flv/" . $item->id);
            }
            db::build()->delete("transcode_resolutions")->where("item_id", "=", $item->id)->execute();
        }
    }

    static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        self::rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    private static function _getVideoInfo($file) {
        $ffmpegPath = module::get_var("transcode", "ffmpeg_path");

        $op = array();
        @exec($ffmpegPath . ' -i "' . $file . '" 2>&1', $op);

        $file = new stdclass();
        $file->video = new stdclass();
        $file->audio = new stdclass();
        $file->audio->has = false;

        foreach ($op as $line) {
            transcode::log($line);

	    if (preg_match('/Duration\: (\d{2}):(\d{2}):(\d{2})\.(\d{2})/', $line, $matches)) {
		$file->video->duration = $matches[3] . "." . $matches[4];
		$file->video->duration += $matches[2] * 60;
		$file->video->duration += $matches[1] * 3600;
	    }
            else if (preg_match('/Stream #0\.\d(\(\w*\))?\: Video\:/', $line)) {
                $bits = preg_split('/[\s]+/', $line);

                for ($i = 0; $i < count($bits); $i++) {
                    if ($bits[$i] == "fps,") $file->video->fps = $bits[$i - 1];
                    else if ($bits[$i] == "kb/s,") $file->video->bitrate = $bits[$i - 1] * 1024;
                }

                $file->video->codec = $bits[4];
                list($file->video->width, $file->video->height) = explode('x', $bits[6]);
            }
            else if (preg_match('/Stream #0\.\d(\(\w*\))?+\: Audio\: (\w*)\,/', $line, $matches)) {
                $file->audio->has = true;
                $file->audio->codec = $matches[2];

                if (preg_match('/(\d*) Hz/', $line, $hz))
                    $file->audio->samplerate = $hz[1];
                if (preg_match('/(\d*) channels?/', $line, $channels))
                    $file->audio->channels = $channels[1];
                if (preg_match('/(\d*) kb\/s/', $line, $bitrate))
                    $file->audio->bitrate = $bitrate[1];
            }
        }

        return $file;
    }

    static function item_created($item) {
        if ($item->is_movie()) {
            transcode::log("Item created - is a movie. Let's create a transcode task or 2..");
            $ffmpegPath = module::get_var("transcode", "ffmpeg_path");

            $fileObj = self::_getVideoInfo($item->file_path());
            transcode::log($fileObj);

            // Save our needed variables
            $srcFile = $item->file_path();
            $srcWidth = transcode::makeMultipleTwo($fileObj->video->width);
            $srcHeight = transcode::makeMultipleTwo($fileObj->video->height);
            $aspect = $srcWidth / $srcHeight;

            $srcFPS = $fileObj->video->fps;

            $srcAR = $fileObj->audio->samplerate;
            if ($srcAR > 44100) $srcAR = 44100;

            $accepted_sample_rates = array(11025, 22050, 44100);

            if (!in_array($srcAR, $accepted_sample_rates)) {
                // if the input sample rate isn't an accepted rate, find the next lowest rate that is
                $below = true; $rate = 0;
                if ($srcAR < 11025) {
                    $rate = 11025;
                }
                else {
                    foreach ($accepted_sample_rates as $r) {
                        transcode::log("testing audio rate '" . $r . "' against input rate '" . $srcAR . "'");
                        if ($r < $srcAR) {
                            $rate = $r;
                        }
                    }
                }
                $srcAR = $rate;
            }

            $srcACodec = module::get_var("transcode", "audio_codec");

            $heights = array();
            if (module::get_var("transcode", "resolution_240p"))  array_push($heights, 240);
            if (module::get_var("transcode", "resolution_360p"))  array_push($heights, 360);
            if (module::get_var("transcode", "resolution_480p"))  array_push($heights, 480);
	    if (module::get_var("transcode", "resolution_576p"))  array_push($heights, 576);
            if (module::get_var("transcode", "resolution_720p"))  array_push($heights, 720);
            if (module::get_var("transcode", "resolution_1080p")) array_push($heights, 1080);

            if (!is_dir(VARPATH . "modules/transcode/flv/" . $item->id))
                    @mkdir(VARPATH . "modules/transcode/flv/" . $item->id);

            $xtraFlags = module::get_var("transcode", "ffmpeg_flags", "");

            foreach ($heights as $destHeight) {
		transcode::log("srcHeight: " . $srcHeight . ", destheight: " . $destHeight);

		// don't bother upscaling, there's no advantage to it...
		if ($destHeight > $srcHeight) continue; 

                $destFormat = module::get_var("transcode", "format", "flv");
                $destWidth = floor($destHeight * $aspect);
                if ($destWidth % 2)
                    $destWidth = ceil($destHeight * $aspect);

		transcode::log("destination resolution: " . $destWidth . "x" . $destHeight);

                $destFile = VARPATH . "modules/transcode/flv/" . $item->id . "/" . $destWidth . "x" . $destHeight . ".flv";

		switch ($destHeight) {
			case 240: $destVB = 128; $srcAB = 16 * ($fileObj->audio->channels ? $fileObj->audio->channels : 2); break;
			case 360: $destVB = 384; $srcAB = 16 * ($fileObj->audio->channels ? $fileObj->audio->channels : 2); break;
			case 480: $destVB = 1024; $srcAB = 32 * ($fileObj->audio->channels ? $fileObj->audio->channels : 2); break;
			case 576: $destVB = 2048; $srcAB = 32 * ($fileObj->audio->channels ? $fileObj->audio->channels : 2); break;
			case 720: $destVB = 4096; $srcAB = 64 * ($fileObj->audio->channels ? $fileObj->audio->channels : 2); break;
			case 1080; $destVB = 8192; $srcAB = 64 * ($fileObj->audio->channels ? $fileObj->audio->channels : 2); break;
		}
                $destVB *= 1024;
		$srcAB *= 1024;

                $cmd =
                    $ffmpegPath . " " .
                    "-i \"" . $srcFile  . "\" ";
                if ($fileObj->audio->has)
                    $cmd .=
                        "-y -acodec " . $srcACodec . " " .
                        "-ar " . $srcAR . " " .
                        "-ab " . $srcAB . " ";
                else
                    $cmd .=
                        "-an ";

                $cmd .=
                    "-vb " . $destVB . " " .
                    "-f " . $destFormat . " " .
                    "-s " . $destWidth . "x" . $destHeight . " " .
                    $xtraFlags . " " .
                    "\"" . $destFile . "\"";

                transcode::log($cmd);

                $task_def =
                    Task_Definition::factory()
                        ->callback("transcode_task::transcode")
                        ->name("Video Transcode to " . $destWidth . "x" . $destHeight)
                        ->severity(log::SUCCESS);

                $task =
                    task::create($task_def,
                                 array("ffmpeg_cmd" => $cmd,
                                       "width" => $destWidth,
                                       "height" => $destHeight,
                                       "item_id" => $item->id)
                );

                task::run($task->id);
            }
        }
    }
}
