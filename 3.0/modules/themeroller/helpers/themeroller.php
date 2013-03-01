<?php defined("SYSPATH") or die("No direct script access.");/**
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
class themeroller {
  static function extract_zip_file($zipfile) {
    $extract_path = VARPATH . trim($zipfile, "/") . ".d";
    if (extension_loaded("zip")) {
      $zip = new ZipArchive();
      if ($zip->open($zipfile) === true) {
        Session::instance()->set("theme_extract_path", $extract_path);
        $zip->extractTo($extract_path);
        $zip->close();
        return $extract_path;
      }
    } else if (extension_loaded("zlib")) {
      require_once(MODPATH . "themeroller/libraries/pclzip.lib.php");
      $archive = new PclZip($zipfile);
      $list = $archive->extract(PCLZIP_OPT_PATH, $extract_path);
      if (!empty($list)) {
        Session::instance()->set("theme_extract_path", $extract_path);
        return $extract_path;
      }
    }
    return false;
  }

  static function recursive_directory_delete($path) {
    if (is_dir($path)) {
      $objects = scandir($path);
      foreach ($objects as $object) {
        if ($object[0] != ".") {
          $object_path = "$path/$object";
          if (filetype($object_path) == "dir") {
            self::recursive_directory_delete($object_path);
          } else {
            unlink($object_path);
          }
        }
      }
    }
  }

  static function get_theme_name($extract_path) {
    $theme_name = null;
    if ($handle = opendir($extract_path . "/css")) {
      while (false !== ($file = readdir($handle))) {
        if ($file[0] !== ".") {
          $theme_name = basename($file);
          break;
        }
      }
      if (empty($theme_name)) {
        Kohana_Log::add("error", "zip file: no theme name");
        $post->add_error($field, "invalid zipfile");
      }
      closedir($handle);
    }

    return $theme_name;
  }

  static function get_theme_parameters($original_name, $css_path, $is_admin) {
    $parameters = array();
    $css_files = glob("$css_path/css/$original_name/jquery*.css");
    $css_contents = file_get_contents($css_files[0]);
    $parameters["colors"] = $parameters["icons"] = array();
    if (preg_match("/[?|&](.*)/", $css_contents, $matches)) {
      if (preg_match_all("/&{0,1}(\w+)=([a-zA-Z0-9\-_\%\.,]*)/", $matches[1], $colors, PREG_SET_ORDER)) {
        foreach ($colors as $color) {
          $parameters["colors"][$color[1]] = $color[2];
          if (strpos($color[1], "icon") === 0) {
            $parameters["icons"][] = $color[2];
          }
        }
      }
    }
    if (empty($parameters["colors"]["bgColorOverlay"])) {
      $parameters["colors"]["bgColorOverlay"] = $parameters["colors"]["bgColorDefault"];
      /* @todo go find the .ui-widget-overlay { background: #aaaaaa */
    }
    // The jquery themeroller has no warning style so lets generate the appropriate colors.
    // We'll do this by averaging the color components of highlight and error colors
    foreach (array("borderColor", "fc", "bgColor", "iconColor") as $type) {
      $highlight = self::_rgb(hexdec($parameters["colors"]["{$type}Highlight"]));
      $error = self::_rgb(hexdec($parameters["colors"]["{$type}Error"]));

      $warning = 0;
      foreach (array("red", "green", "blue") as $color) {
        $warning = ($warning << 8) | (int)floor(($highlight[$color] + $error[$color]) / 2);
      }
      $parameters["colors"]["{$type}Warning"] = dechex($warning);
      if ($type == "iconColor") {
        $parameters["icons"][] = $parameters["colors"]["{$type}Warning"];
      }
    }

    $parameters["js"] = $is_admin ? glob(MODPATH . "themeroller/data/js/admin_*.js") :
      glob(MODPATH . "themeroller/data/js/site_*.js");
    $parameters["standard_css"] = glob(MODPATH . "themeroller/data/css/*.css");
    $parameters["masks"] = glob(MODPATH . "themeroller/data/masks/images/*.png");
    $parameters["icon_mask"] = MODPATH . "themeroller/data/masks/css/themeroller/ui-icons_mask_256x240.png";
    $parameters["views"] = $is_admin ? glob(MODPATH . "themeroller/data/admin_views/*.html.php") :
      glob(MODPATH . "themeroller/data/views/*.html.php");
    $parameters["css_files"] = $css_files;
    $parameters["gifs"] = glob(MODPATH . "themeroller/data/images/*.gif");
    $parameters["images"] =
      glob("$css_path/development-bundle/themes/$original_name/images/ui-bg*.png");
    $thumb_dir = $is_admin ? "admin_thumbnail" : "site_thumbnail";
    $parameters["thumbnail"] = MODPATH . "themeroller/data/masks/$thumb_dir/thumbnail.png";
    $parts = glob(MODPATH . "themeroller/data/masks/$thumb_dir/thumbnail_*.png");
    $parameters["thumbnail_parts"] = array();
    foreach ($parts as $thumb_file) {
      if (preg_match("/thumbnail_(.*)\.png$/", $thumb_file, $matches)) {
        $parameters["thumbnail_parts"][] = array("file" => $thumb_file,
                                                 "color" => $parameters["colors"][$matches[1]]);
      }
    }

    return $parameters;
  }

  static function generate_image($mask_file, $output, $color) {
    $mask = imagecreatefrompng($mask_file);
    $image = imagecreatetruecolor(imagesx($mask), imagesy($mask));
    $icon_color = self::_rgb(hexdec($color));

    $transparent = imagecolorallocatealpha($image,
       $icon_color['red'], $icon_color['green'], $icon_color['blue'], 127);
    imagefill($image, 0, 0, $transparent);

    for ($y=0; $y < imagesy($mask); $y++) {
      for ($x=0; $x < imagesx($mask); $x++) {
        $pixel_color = imagecolorsforindex($mask, imagecolorat($mask, $x, $y));
        $mask_color = self::_grayscale_pixel($pixel_color);
        $mask_alpha = 127 - floor($mask_color["red"] * 127 / 256);
        $new_color = imagecolorallocatealpha($image,
          $icon_color['red'], $icon_color['green'], $icon_color['blue'], $mask_alpha);
       imagesetpixel($image, $x, $y, $new_color);
      }
    }

    imagesavealpha($image, true);
    imagealphablending($image, false);
    imagepng($image, $output);
    imagedestroy($image);
    imagedestroy($mask);
  }

  static function generate_thumbnail($base, $parts, $target) {
    $image = imagecreatefrompng($base);

    $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
    imagefill($image, 0, 0, $transparent);

    $width = imagesx($image);
    $height = imagesy($image);

    foreach ($parts as $thumb_part) {
      $color = self::_rgb(hexdec($thumb_part["color"]));
      $image_part = imagecreatefrompng($thumb_part["file"]);
      for ($y=0; $y < imagesy($image_part); $y++) {
        for ($x=0; $x < imagesx($image_part); $x++) {
          $pixel_color = imagecolorsforindex($image_part, imagecolorat($image_part, $x, $y));
          $new_color = imagecolorallocatealpha($image,
                                               $color['red'], $color['green'], $color['blue'], $pixel_color["alpha"]);
          imagesetpixel($image, $x, $y, $new_color);
        }
      }
      imagedestroy($image_part);
    }

    imagesavealpha($image, true);
    imagealphablending($image, false);
    imagepng($image, $target);
    imagedestroy($image);
  }

  private static function _rgb($color) {
    $r = ($color >> 16) & 0xff;
    $g = ($color >> 8) & 0xff;
    $b = $color & 0xff;
    return array("red" => $r, "green" => $g, "blue" => $b, "alpha" => 0);
  }

  private static function _grayscale_pixel($color) {
    $gray = round(($color['red'] * 0.299) + ($color['green'] * 0.587) + ($color['blue'] * 0.114));
    return array("red" => $gray, "green" => $gray, "blue" => $gray, "alpha" => 0);
  }
}
