<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
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
class image_optimizer_Core {

  /**
   * These functions deal with the toolkit installations
   */
   
  // define the tool names
  static function tool_name($type) {
    $type = strtolower($type);
    switch($type) {
      case "jpg":
        $tool = "jpegtran";
        break;
      case "png":
        $tool = "optipng";
        break;
      case "gif":
        $tool = "gifsicle";
        break;
      default:
        $tool = "";
    }
    return $tool;
  }

  // find server-installed versions of the tools
  static function tool_installed_path($type) {
    $type = strtolower($type);
    $path = exec('which '.image_optimizer::tool_name($type));
    $path = is_file($path) ? $path : "(not found)";
    return $path;
  }

  // return status of tool path and attempt to fix permissions if not executable
  static function tool_status($type) {
    $type = strtolower($type);
    $path = module::get_var("image_optimizer","path_".$type);
    if (is_file($path) && !is_link($path)) {
      if (is_executable($path)) {
        $status = true;
      } else {
        // try to fix its permissions. return false if it doesn't work.
        $status = chmod($path,0755);
      }
    } else {
      $status = false;
    }
    // set module variable
    module::set_var("image_optimizer", "configstatus_".$type, $status);
    return $status;
  }

  // return tool version as string
  static function tool_version($type) {
    $type = strtolower($type);
    if (image_optimizer::tool_status($type)) {
      switch($type) {
        case "jpg":
          $path = module::get_var("image_optimizer","path_".$type);
          // jpegtran is weird as it doesn't have a version flag.  so, we run in verbose mode with a fake file and catch stderr, which exec() can't do.
          // this is sort of a hack, but since there's no clean way available...
          $cmd = escapeshellcmd($path)." -verbose ".MODPATH."image_optimizer/this_file_does_not_exist.jpg";
          $output = image_optimizer::get_pipe($cmd, 2);
          $output = "Correctly configured! " . substr($output, 0, strpos($output, "\n"));
          break;
        case "png":
        case "gif":
          $path = module::get_var("image_optimizer","path_".$type);
          exec(escapeshellcmd($path)." --version", $output);
          $output = "Correctly configured!  " . $output[0];
          break;
        default:
          $output = t("Only jpg/png/gif supported");
      }
    } else {
      $output = t("Invalid configuration");
    }
    return $output;
  }

  /**
   * These functions supplement the rule functions in modules/gallery/helpers/graphics.php
   * Note that all rule-changing functions in graphics.php trigger all images to be marked as dirty for rebuild, which we often want to avoid.
   */

  // add image_optimizer rules without marking for dirty (based on add_rule)
  static function add_image_optimizer_rule($target) {
    // to prevent duplicates, remove any existing instances first
    image_optimizer::remove_image_optimizer_rule($target);
    // then add the new one
    $rule = ORM::factory("graphics_rule");
    $rule->module_name = "image_optimizer";
    $rule->target = $target;
    $rule->operation = 'image_optimizer::optimize';
    $rule->priority = 999999999; // this MUST be larger than all others to be last
    $rule->args = serialize($target); // this isn't typical for other graphics rules
    $rule->active = true;
    $rule->save();
  }

  // remove image_optimizer rules without marking for dirty (based on remove_rule)
  static function remove_image_optimizer_rule($target) {
    db::build()
      ->delete("graphics_rules")
      ->where("target", "=", $target)
      ->where("module_name", "=", "image_optimizer")
      ->execute();
  }

  // activate update mode - disactivates all currently-active rules except those of image_optimizer without marking for dirty
  // sets update_mode_thumb/resize variable with serialized list of deactivated rule ids
  static function activate_update_mode($target) {
    // find all currently active non-image-optimizer rules
    $rules = db::build()
        ->from("graphics_rules")
        ->select("id")
        ->where("active", "=", true)
        ->where("target", "=", $target)
        ->where("module_name", "!=", "image_optimizer")
        ->execute();
    // use found rules to build ids array and deactivate rules
    $ids = array();
    foreach ($rules as $rule) {
      $ids[] = $rule->id;
      db::build()
        ->update("graphics_rules")
        ->where("id", "=", $rule->id)
        ->set("active", false) // deactivation!
        ->execute();
    }
    // set module variable as deactivated rule ids
    module::set_var("image_optimizer", "update_mode_".$target, serialize($ids));
    // display a warning that we're in update mode
    site_status::warning(
      t("Image optimizer is in thumb/resize update mode - remember to exit <a href=\"%url\">here</a> after rebuild!",
        array("url" => html::mark_clean(url::site("admin/image_optimizer")))),
      "image_optimizer_update_mode");
  }

  // deactivate update mode - re-activates rules marked in the update_mode_thumb/resize variable as previously deactivated
  static function deactivate_update_mode($target) {
    // get deactivated rule ids
    $ids = unserialize(module::get_var("image_optimizer", "update_mode_".$target));
    // activate them
    foreach ($ids as $id) {
      db::build()
        ->update("graphics_rules")
        ->where("id", "=", $id)
        ->set("active", true) // activation!
        ->execute();
    }
    // reset module variable
    module::set_var("image_optimizer", "update_mode_".$target, false);
    // clear update mode warning
    if (!module::get_var("image_optimizer", "update_mode_thumb") && !module::get_var("image_optimizer", "update_mode_resize")) {
      site_status::clear("image_optimizer_update_mode");
    }
  }

  // mark all as dirty (in similar syntax to above)
  static function dirty($target) {
    graphics::mark_dirty($target == "thumb", $target == "resize");
  }

  /**
   * the main optimize function
   *
   * the function arguments are the same format as other graphics rules.  the only "option" is $target, hence why it's renamed in the function def.
   *
   * NOTE: unlike other graphics transformations, this only uses the output file!  if it isn't already there, we don't do anything.
   * among other things, this means that the original, full-size images are never touched.
   */
  static function optimize($input_file, $output_file, $target, $item=null) {
    
		// see if output file exists and is writable
    if (is_writable($output_file)) {
      // see if input is a supported file type.  if not, return without doing anything.
      $image_info = getimagesize($input_file); // [0]=w, [1]=h, [2]=type (1=GIF, 2=JPG, 3=PNG)
      switch ($image_info[2]) {
        case 1:
          $type_old = "gif";
          $convert = module::get_var("image_optimizer", "convert_".$target."_gif");
          break;
        case 2:
          $type_old = "jpg";
          $convert = 0; // no conversion possible here...
          break;
        case 3:
          $type_old = "png";
          $convert = module::get_var("image_optimizer", "convert_".$target."_png");
          break;
        default:
          // not a supported file type
          return;
      }
    } else {
      // file doesn't exist or isn't writable
      return;
    }
    // set new file type
    $type = $convert ? $convert : $type_old;

    // convert image type (if applicable).  this isn't necessarily lossless.
    if ($convert) {
      $output_file_new = legal_file::change_extension($output_file, $type);
      // perform conversion using standard Gallery toolkit (GD/ImageMagick/GraphicsMagick)
      // note: if input was a GIF, this will kill animation
      $image = Image::factory($output_file)
        ->quality(module::get_var("gallery", "image_quality"))
        ->save($output_file_new);
      // if filenames are different, move the new on top of the old
      if ($output_file != $output_file_new) {
        /**
         * HACK ALERT!  Gallery3 is still broken with regard to treating thumb/resizes with proper extensions.  This doesn't try to fix that.
         *   Normal Gallery setup:
         *     photo thumb -> keep photo type, keep photo extension
         *     album thumb -> keep source photo thumb type, change extension to jpg (i.e. ".album.jpg" even for png/gif)
         * Also, missing_photo.png is similarly altered...
         *
         * Anyway, to avoid many rewrites of core functions (and not-easily-reversible database changes), this module also forces the extension to stay the same.
         *   With image optimizer conversion:
         *     photo thumb -> change type, keep photo extension (i.e. "photo.png" photo becomes "photo.png" thumb even if type has changed)
         *     album thumb -> keep source photo thumb type, change extension to jpg (i.e. ".album.jpg" even for png/gif)
         */
        rename($output_file_new, $output_file);
      }
    }

    // get module variables
    $configstatus = module::get_var("image_optimizer", "configstatus_".$type);
    $path = module::get_var("image_optimizer", "path_".$type);
    $opt = module::get_var("image_optimizer", "optlevel_".$target."_".$type);
    $meta = module::get_var("image_optimizer", "metastrip_".$target);
    $prog = module::get_var("image_optimizer", "progressive_".$target);
    
    // make sure the toolkit is configured correctly and we want to use it - if not, return without doing anything.
    if ($configstatus) {
      if (!$prog && !$meta && !$opt) {
        // nothing to do!
        return;
      }
    } else {
      // not configured correctly
      return;
    }

    /**
     * do the actual optimization
     */

     // set parameters
     switch ($type) {
      case "jpg":
        $exec_args  = $opt ? " -optimize" : "";
        $exec_args .= $meta ? " -copy none" : " -copy all";
        $exec_args .= $prog ? " -progressive" : "";
        $exec_args .= " -outfile ";
        break;
      case "png":
        $exec_args  = $opt ? " -o".$opt : "";
        $exec_args .= $meta ? " -strip all" : "";
        $exec_args .= $prog ? " -i 1" : "";
        $exec_args .= " -quiet -out ";
        break;
      case "gif":
        $exec_args  = $opt ? " --optimize=3" : ""; // levels 1 and 2 don't really help us
        $exec_args .= $meta ? " --no-comments --no-extensions --no-names" : " --same-comments --same-extensions --same-names";
        $exec_args .= $prog ? " --interlace" : " --same-interlace";
        $exec_args .= " --careful --output ";
        break;
    }

    // run it - from output_file to tmp_file.
    $tmp_file = image_optimizer::make_temp_name($output_file);
    exec(escapeshellcmd($path) . $exec_args . escapeshellarg($tmp_file) . " " . escapeshellarg($output_file), $exec_output, $exec_status);
    if ($exec_status || !filesize($tmp_file)) {
      // either a blank/nonexistant file or an error - do nothing to the output, but log an error and delete the temp (if any)
      Kohana_Log::add("error", "image_optimizer optimization failed on ".$output_file);
      unlink($tmp_file);
    } else {
      // worked - move temp to output
      rename($tmp_file, $output_file);
    }
  }
  
  /**
   * make a unique temporary filename.  this bit is inspired/copied from
   * the system/libraries/Image.php save function and the system/libraries/drivers/Image/ImageMagick.php process function
   */
  static function make_temp_name($file) {
    // Separate the directory and filename
		$dir  = pathinfo($file, PATHINFO_DIRNAME);
		$file = pathinfo($file, PATHINFO_BASENAME);

		// Normalize the path
		$dir = str_replace('\\', '/', realpath($dir)).'/';

		// Unique temporary filename
		$tmp_file = $dir.'k2img--'.sha1(time().$dir.$file).substr($file, strrpos($file, '.'));
    
    return $tmp_file;
  }
  
  /** 
   * get stdin, stdout, or stderr from shell command.  php commands like exec() don't do this.
   * this is only used to get jpegtran's version info in the admin screen.
   * 
   * see http://stackoverflow.com/questions/2320608/php-stderr-after-exec
   */
  static function get_pipe($cmd, $pipe) {
    $descriptorspec = array(
      0 => array("pipe", "r"),  // stdin
      1 => array("pipe", "w"),  // stdout
      2 => array("pipe", "w"),  // stderr
    );
    $process = proc_open($cmd, $descriptorspec, $pipes);
    $output = stream_get_contents($pipes[$pipe]);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
    return $output;
  }
}