<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class Admin_Transcode_Controller extends Admin_Controller {
    public function verify() {
        $data = array();
        $data['success'] = false;

        if (($val = transcode::verify_path($_REQUEST['ffmpeg_path'])) > 0) {
            module::set_var("transcode", "ffmpeg_path", $_REQUEST['ffmpeg_path']);
            $data['success'] = true;
            $data['codecs'] = self::_get_supported_audio_codecs($_REQUEST['ffmpeg_path']);
        }
        else {
            $error = "";
            switch ($val) {
                case 0: $error = "Empty file path provided"; break;
                case -1: $error = "File does not exist"; break;
                case -2: $error = "Path is a directory"; break;
                default: $error = "Unspecified error";
            }
            $data['error'] = $error;
        }
        echo json_encode($data);
    }

    public function index() {
        $form = $this->_get_form();

        if (request::method() == "post") {
            access::verify_csrf();

            if ($form->validate()) {
                module::set_var("transcode", "ffmpeg_path",        $_POST['ffmpeg_path']);
                module::set_var("transcode", "ffmpeg_flags",       $_POST['ffmpeg_flags']);
                module::set_var("transcode", "audio_codec",        $_POST['audio_codec']);
                module::set_var("transcode", "ffmpeg_audio_kbits", (isset($_POST['ffmpeg_audio_kbits']) ? $_POST['ffmpeg_audio_kbits'] : false));

                module::set_var("transcode", "resolution_240p",    (isset($_POST['resolution_240p'])    ? $_POST['resolution_240p']    : false));
                module::set_var("transcode", "resolution_360p",    (isset($_POST['resolution_360p'])    ? $_POST['resolution_360p']    : false));
                module::set_var("transcode", "resolution_480p",    (isset($_POST['resolution_480p'])    ? $_POST['resolution_480p']    : false));
		module::set_var("transcode", "resolution_576p",    (isset($_POST['resolution_576p'])    ? $_POST['resolution_576p']    : false));
                module::set_var("transcode", "resolution_720p",    (isset($_POST['resolution_720p'])    ? $_POST['resolution_720p']    : false));
                module::set_var("transcode", "resolution_1080p",   (isset($_POST['resolution_1080p'])   ? $_POST['resolution_1080p']   : false));

                message::success(t("Settings have been saved"));
                url::redirect("admin/transcode");
            }
            else {
                message::error(t("There was a problem with the submitted form. Please check your values and try again."));
            }
        }

        print $this->_get_view();
    }

    private function _get_view($form = null) {
        $v = new Admin_View("admin.html");
        $v->page_title = t("Gallery 3 :: Manage Transcoding Settings");

        $v->content = new View("admin_transcode.html");
        $v->content->form = empty($form) ? $this->_get_form() : $form;

        return $v;
    }

    private function _get_supported_audio_codecs($given_path = null) {

        $flv_compatible_codecs = array("aac" => "aac", "adpcm_swf" => "adpcm_swf", "mp3" => "libmp3lame");

        if ($given_path)
            $ffmpegPath = $given_path;
        else
            $ffmpegPath = module::get_var("transcode", "ffmpeg_path", transcode::whereis("ffmpeg"));

        $legacy = false;
        exec($ffmpegPath . " -codecs", $codecs);
        if (count($codecs) == 0) {
            $legacy = true;
            exec($ffmpegPath . " -formats 2>&1", $codecs);
        }

        $search = true;
        if ($legacy) $search = false;

        $found = array();
        foreach ($codecs as $line) {
            if ($search) {
                if (strpos($line, "DEA")) {
                    $bits = preg_split("/[\s]+/", $line);
                    if (in_array($bits[2], $flv_compatible_codecs)) {
                        $key = array_search($bits[2], $flv_compatible_codecs);
                        $found[$key] = $flv_compatible_codecs[$key];
                    }
               }
            }

            if ($legacy && strpos($line, "Codecs:") !== false) {
                $search = true;
                continue;
            }
            if ($legacy && $search && strpos($line, "Bitstream filters:")) {
                $search = false;
                continue;
            }
        }
        return $found;
    }

    private function _get_form() {
        $form = new Forge("admin/transcode", "", "post", array("id" => "g-admin-transcode-form"));

        $group = $form->group("system")->label(t("System"));

        $ffmpegPath = transcode::whereis("ffmpeg");
        $codecs = $this->_get_supported_audio_codecs();

        $group  ->input("ffmpeg_path")
                ->id("ffmpeg_path")
                ->label(t("Path to ffmpeg binary:"))
                ->value(module::get_var("transcode", "ffmpeg_path", $ffmpegPath))
                ->callback("transcode::verify_ffmpeg_path")
                ->error_messages("required", t("You must enter the path to ffmpeg"))
                ->error_messages("invalid", t("File does not exist"))
                ->error_messages("is_dir", t("File is a directory"))
                ->message("Auto detected ffmpeg here: " . $ffmpegPath . "<br />Click <a href='javascript:verifyffmpeg();'>here</a> to verify ffmpeg path and continue.");

        $group	->input("ffmpeg_flags")
                ->id("ffmpeg_flags")
                ->label(t("Extra ffmpeg flags:"))
                ->value(module::get_var("transcode", "ffmpeg_flags"));

        $group  ->dropdown("audio_codec")
                ->id("audio_codec")
                ->label(t("Audio codec to use:"))
                ->options($codecs)
                ->selected(module::get_var("transcode", "audio_codec"));

        $group	->checkbox("ffmpeg_audio_kbits")
                ->label(t("Send audio bitrate as kbits instead of bits/s"))
                ->checked(module::get_var("transcode", "ffmpeg_audio_kbits"));

        $group = $form->group("resolutions")->label(t("Resolutions"));

        $group	->checkbox("resolution_240p")
                ->label("240p")
                ->checked(module::get_var("transcode", "resolution_240p"));
        $group	->checkbox("resolution_360p")
                ->label("360p")
                ->checked(module::get_var("transcode", "resolution_360p"));
        $group	->checkbox("resolution_480p")
                ->label("480p")
                ->checked(module::get_var("transcode", "resolution_480p"));
        $group	->checkbox("resolution_576p")
                ->label("576p")
                ->checked(module::get_var("transcode", "resolution_576p"));
        $group	->checkbox("resolution_720p")
                ->label("720p")
                ->checked(module::get_var("transcode", "resolution_720p"));
        $group	->checkbox("resolution_1080p")
                ->label("1080p")
                ->checked(module::get_var("transcode", "resolution_1080p"));

        $form->submit("submit")->value(t("Save"));
        return $form;
    }
}
