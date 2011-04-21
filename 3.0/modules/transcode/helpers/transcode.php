<?php defined("SYSPATH") or die("No direct script access.") ?><?php

class transcode_Core {
	
	static function can_use() {
		return true;
	}

	static function makeMultipleTwo($value) {
		$sType = gettype($value/2);
		if($sType == "integer")
			return $value;
		else
			return ($value-1);
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
	static function verify_ffmpeg_path($field) {
            $v = self::verify_path($field->value);
            switch ($v) {
                case 0: $field->add_error("required", 1); break;
                case -1: $field->add_error("invalid", 1); break;
                case -2: $field->add_error("is_dir", 1); break;
            }
	}

        static function log($item) {

            if (is_string($item) || is_numeric($item)) {}
            else
                $item = print_r($item, true);

            $fh = fopen(VARPATH . "modules/transcode/log/transcode.log", "a");
            fwrite($fh, date("Y-m-d H:i:s") . ": " . $item .  "\n");
            fclose($fh);

        }
			
}
