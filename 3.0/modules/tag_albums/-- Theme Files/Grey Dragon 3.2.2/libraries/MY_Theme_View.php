<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Grey Dragon Theme - a custom theme for Gallery 3
 * This theme was designed and built by Serguei Dosyukov, whose blog you will find at http://blog.dragonsoft.us
 * Copyright (C) 2009-2012 Serguei Dosyukov
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<?
class Theme_View extends Theme_View_Core {

  protected $viewmode = "default";
  protected $toolbar_large = FALSE;
  protected $paginator_album;
  protected $paginator_photo;
  protected $sidebarvisible;
  protected $sidebarallowed;
  protected $sidebar_hideguest = FALSE;
  protected $logopath;
  protected $favicon = "lib/images/favicon.ico";
  protected $appletouchicon;
  protected $album_descmode = "hide";
  protected $disablephotopage = FALSE;
  protected $hidecontextmenu = FALSE;
  protected $thumb_ratio = "";
  protected $thumb_descmode_a = "overlay";
  protected $thumb_descmode = "overlay";
  protected $photo_descmode = "overlay_top";
  protected $thumb_imgalign = "top";
  protected $thumb_metamode = "default";
  protected $is_blockheader_visible = TRUE;
  protected $is_photometa_visible = FALSE;
  protected $disable_seosupport = FALSE;
  protected $mainmenu_position = "bottom-left";
  protected $breadcrumbs_position = "bottom-right";
  protected $breadcrumbs_showinroot = FALSE;
  protected $copyright = null;
  protected $show_guest_menu = FALSE;
  protected $loginmenu_position = "default";
  protected $visible_title_length = 15;
  protected $title_source = "default";
  protected $desc_allowbbcode = FALSE;
  protected $enable_pagecache = FALSE;
  protected $flex_rows = FALSE;
  protected $photo_popupbox = "default";
  protected $custom_css_path = "";
  protected $thumb_inpage = FALSE;
  protected $thumb_random = FALSE;
  protected $row_count = 3;
  protected $column_count = 3;

  protected $crop_factor = -1;
  protected $crop_class = "";
  protected $_thumb_size_x = 200;
  protected $_thumb_size_y = 200;
  protected $allow_root_page = FALSE;
  protected $show_root_page = FALSE;
  protected $show_root_desc = TRUE;
  protected $root_feed = "gallery/latest";
  protected $root_cyclemode = "fade";
  protected $root_delay = 15;
  protected $root_description;
  protected $permalinks = array("enter" => "?root=no", "root" => "?root=yes");

  protected $last_update = 0;

  protected $colorpack = "greydragon";
  protected $framepack = "greydragon";
  protected $themename = "";
  protected $themeversion = "";
  protected $themecss = array();

  protected $is_rtl = FALSE;

  protected function ensurevalue($value, $default) {
    if ((!isset($value)) or ($value == "")):
      return $default;
    else:
      return $value;
    endif;
  }

  protected function ensureoptionsvalue($key, $default) {
    return ($this->ensurevalue(module::get_var("th_greydragon", $key), $default));
  }

  public function read_session_cmdparam($cmd, $cookie, $issession, $default) {
    try {
      $_cmd = $_GET[$cmd]; 
    } catch (Exception $e) {
    };

    if (isset($_cmd)):
      $_var = strtolower($_cmd);
      $_from_cmd = TRUE;
      if ($_var == "default"):
        $_var = $default;
      endif;
    else:
      $_from_cmd = FALSE;
      if ($cookie):
        try { 
          $_var = $_COOKIE[$cookie];
        } catch (Exception $e) {
        };
      endif;
    endif;

    if (!isset($_var)):
      $_var = $default;
    endif;

    if ($issession):
      if ($_from_cmd):
        setcookie($cookie, $_var, 0);
      endif;
    else:
      setcookie($cookie, $_var, time() + 31536000);
    endif;

    return $_var;
  }

  public function load_sessioninfo() {
    // Sidebar position is kept for 360 days. Can be changed via url
    $this->sidebarallowed = $this->ensureoptionsvalue("sidebar_allowed", "any");
    $_sb_visible = $this->ensureoptionsvalue("sidebar_visible", "right");

    if ($this->sidebarallowed == "default"):
      $this->sidebarallowed = $_sb_visible;
      $this->sidebarvisible = $_sb_visible;
    else:
      $this->sidebarvisible = $this->read_session_cmdparam("sb", "gd_sidebar_pos", FALSE, $_sb_visible);
    endif;
    $this->colorpack = $this->read_session_cmdparam("colorpack", "gd_colorpack", TRUE,  $this->ensureoptionsvalue("color_pack", "greydragon"));
    $this->framepack = $this->read_session_cmdparam("framepack", "gd_framepack", TRUE, $this->ensureoptionsvalue("frame_pack", "greydragon"));
    $this->viewmode  = $this->read_session_cmdparam("viewmode", "gd_viewmode",  TRUE, $this->ensureoptionsvalue("viewmode", "default"));
    $this->is_rtl    = $this->read_session_cmdparam("is_rtl", "gd_rtl", TRUE, "no") == "yes";
		$this->thumb_ratio = $this->read_session_cmdparam("ratio", "gd_ratio", TRUE, $this->ensureoptionsvalue("thumb_ratio", "photo"));

    if ($this->ensureoptionsvalue("allow_root_page", FALSE)):
      $_root = $this->read_session_cmdparam("root", "gd_rootpage", TRUE, "yes"); 

      $this->show_root_page = ($_root == "yes");
      $this->allow_root_page = TRUE;

      if ($this->show_root_page):
        $item = $this->item();
        if (($item) && ($item->id == item::root()->id)):
          if (($this->sidebarvisible == "left") or ($this->sidebarvisible == "right")):
            $this->sidebarvisible = "bottom";
          endif;
        else:
          $this->show_root_page = FALSE;
          setcookie("gd_rootpage", "no", 0);
        endif;

        if ($this->ensureoptionsvalue("hide_root_sidebar", FALSE)):
          $this->sidebarallowed = "none";
          $this->sidebarvisible = "none";
        endif;
      endif;
    endif;

    $this->sidebarvisible = $this->ensurevalue($this->sidebarvisible, "right");

    switch ($this->sidebarallowed):
      case "default":
        break;
      case "right":
        $this->sidebarvisible = "right";
        break;
      case "left": 
        $this->sidebarvisible = "left";
        break;
      case "bottom": 
        $this->sidebarvisible = "bottom";
        break;
      case "top": 
        $this->sidebarvisible = "top";
        break;
    endswitch;

    if ($this->item()):
      if ($this->ensureoptionsvalue("sidebar_albumonly", FALSE)):
        if (!$this->item()->is_album()):
          $this->sidebarvisible = "none";
          $this->sidebarallowed = "none";
        endif;
      endif;
    endif;

    $this->sidebar_hideguest = $this->ensureoptionsvalue("sidebar_hideguest", FALSE);
    if ((identity::active_user()->guest) & ($this->sidebar_hideguest)):
      $this->sidebarvisible = "none";
      $this->sidebarallowed = "none";
    endif;

    if (($this->page_subtype == "login") || ($this->page_subtype == "reauthenticate") || ($this->page_subtype == "error")): 
      $this->sidebarvisible = "none";
      $this->sidebarallowed = "none";
    endif;

    $this->last_update = $this->ensureoptionsvalue("last_update", time());
    $this->toolbar_large = $this->ensureoptionsvalue("toolbar_large", FALSE);
    $this->row_count = $this->ensureoptionsvalue("row_count", 3);
    $this->column_count = $this->ensureoptionsvalue("column_count", 3);
    $this->logopath = $this->ensureoptionsvalue("logo_path", url::file("lib/images/logo.png"));
    $this->favicon = $this->ensurevalue(module::get_var("gallery", "favicon_url"), url::file("lib/images/favicon.ico"));
    $this->appletouchicon = module::get_var("gallery", "apple_touch_icon_url");
    $this->horizontal_crop = $this->ensureoptionsvalue("horizontal_crop", FALSE);
    $this->album_descmode = $this->ensureoptionsvalue("album_descmode", "hide");
    $this->disablephotopage = $this->ensureoptionsvalue("disablephotopage", FALSE);
    $this->hidecontextmenu = $this->ensureoptionsvalue("hidecontextmenu", FALSE);
    $this->visible_title_length = module::get_var("gallery", "visible_title_length", 15);
    $this->title_source = $this->ensureoptionsvalue("title_source", "default");
    $this->thumb_descmode_a = $this->ensureoptionsvalue("thumb_descmode_a", "overlay");
    $this->thumb_descmode = $this->ensureoptionsvalue("thumb_descmode", "overlay");
    $this->photo_descmode = $this->ensureoptionsvalue("photo_descmode", "overlay_top");

    $this->thumb_random = $this->ensureoptionsvalue("thumb_random", FALSE);
    $this->thumb_imgalign = $this->ensureoptionsvalue("thumb_imgalign", "top");
    if (module::is_active("info")):
      $this->thumb_metamode = $this->ensureoptionsvalue("thumb_metamode", "default");
      $this->is_photometa_visible = (!$this->ensureoptionsvalue("hide_photometa", TRUE));
    else:
      $this->thumb_metamode = "hide";
      $this->is_photometa_visible = FALSE;
    endif;
    $this->disable_seosupport = $this->ensureoptionsvalue("disable_seosupport", FALSE);
    $this->is_blockheader_visible = (!$this->ensureoptionsvalue("hide_blockheader", FALSE));

    $this->mainmenu_position = $this->ensureoptionsvalue("mainmenu_position", "default");
    $this->show_guest_menu = $this->ensureoptionsvalue("show_guest_menu", FALSE);
    $this->breadcrumbs_position = $this->ensureoptionsvalue("breadcrumbs_position", "default");
    $this->breadcrumbs_showinroot = $this->ensureoptionsvalue("breadcrumbs_showinroot", FALSE);
    $this->desc_allowbbcode = $this->ensureoptionsvalue("desc_allowbbcode", FALSE);

    $this->loginmenu_position = $this->ensureoptionsvalue("loginmenu_position", "default");
    $this->copyright = $this->ensureoptionsvalue("copyright", null);
    $this->paginator_album = $this->ensureoptionsvalue("paginator_album", "top");
    $this->paginator_photo = $this->ensureoptionsvalue("paginator_photo", "top");
    $this->enable_pagecache = $this->ensureoptionsvalue("enable_pagecache", FALSE);
    $this->flex_rows = $this->ensureoptionsvalue("flex_rows", FALSE);
    $this->show_root_desc = !$this->ensureoptionsvalue("hide_root_desc", FALSE);
    $this->root_feed = $this->ensureoptionsvalue("root_feed", "gallery/latest");
  	$this->root_cyclemode = $this->ensureoptionsvalue("root_cyclemode", "fade");
    $this->root_delay = $this->ensureoptionsvalue("root_delay", "15");
    $this->root_description = module::get_var("th_greydragon", "root_description");
    if ($this->ensureoptionsvalue("use_permalinks", FALSE)):
      $this->permalinks = array("enter" => "enter", "root" => "root");
    endif;
    if (((module::is_active("shadowbox")) and (module::info("shadowbox"))) 
        or ((module::is_active("fancybox")) and (module::info("fancybox")))
        or ((module::is_active("colorbox")) and (module::info("colorbox")))
       ):
      $this->photo_popupbox = $this->ensureoptionsvalue("photo_popupbox", "default");
    else:
      $this->photo_popupbox = "none";
    endif;

    try {
      $theme_info = new ArrayObject(parse_ini_file(THEMEPATH . "greydragon/theme.info"), ArrayObject::ARRAY_AS_PROPS);
      $this->themename = $theme_info->name;
      $this->themeversion = $theme_info->version;
    } catch (Exception $e) {
      $this->themename = "Grey Dragon Theme";
      $this->themeversion = "2.7.+";
    }

    $this->custom_css_path = $this->ensureoptionsvalue("custom_css_path", "");

    switch ($this->thumb_ratio):
      /* case "square":
        $this->crop_factor = 1;
        $this->thumb_type = 'g-thumbtype-sqr';
        break;
      */
      case "digital":
        $this->crop_factor = 4/3;
        $this->thumb_type = 'g-thumbtype-dgt';
        break;
      case "digital_ex":
        $this->crop_factor = 4/3;
        $this->thumb_type = 'g-thumbtype-dgt';
        $this->_thumb_size_x = 300;
        break;
      case "film":
        $this->crop_factor = 3/2;
        $this->thumb_type = 'g-thumbtype-flm';
        break;
      case "film_ex":
        $this->crop_factor = 3/2;
        $this->thumb_type = 'g-thumbtype-flm';
        $this->_thumb_size_x = 300;
        break;
      case "wide":
        $this->crop_factor = 16/9;
        $this->thumb_type = 'g-thumbtype-wd';
        break;
      case "wide_ex":
        $this->crop_factor = 16/9;
        $this->thumb_type = 'g-thumbtype-wd';
        $this->_thumb_size_x = 300;
        break;
      case "photo_ex":
        $this->crop_factor = 1;
        $this->thumb_type = 'g-thumbtype-sqr';
        $this->_thumb_size_x = 300;
        break;
      case "photo":
      default:
        $this->crop_factor = 1;
        $this->thumb_type = 'g-thumbtype-sqr';
        break;
    endswitch;

    $this->_thumb_size_y = intval($this->_thumb_size_x / $this->crop_factor);

    if (($this->sidebarvisible == "none") or ($this->sidebarvisible == "bottom") or ($this->sidebarvisible == "top") ):
      $this->thumb_inpage = $this->ensureoptionsvalue("thumb_inpage", FALSE);
    endif;                 
  }

  public function is_sidebarallowed($align) {
    return (($this->sidebarallowed == "any") or ($this->sidebarallowed == $align));
  }

  public function custom_header() {
    if (Kohana::find_file('views', "header.html", FALSE)):
      return new View("header.html"); 
    endif;
  }

  public function custom_footer() {
    if (Kohana::find_file('views', "footer.html", FALSE)):
      return new View("footer.html"); 
    endif;
  }

  public function get_item_title($item, $allowbbcode = FALSE, $limit_title_length = 0) {
    if (!$item)
      return "";

    if ($item->is_album()):
      $title = $item->title;
    else:
      switch ($this->title_source):
	      case "description":
	        $title = $item->description;
	        break;
	      case "no-filename":
	        $title = $item->title;
	        $filename = $item->name;

	        if (strcasecmp($title, $filename) == 0):
	          $title = "";
	        else:
		        if (defined('PATHINFO_FILENAME')):
	  	        $filename = pathinfo($filename, PATHINFO_FILENAME);
		        elseif (strstr($item->filename, '.')):
		          $filename = substr($filename, 0, strrpos($filename, '.'));
		        endif; 

		        if (strcasecmp($title, $filename) == 0):
		          $title = "";
		        else:
			        $filename = item::convert_filename_to_title($filename); // Normalize filename to title format 
		  	      if (strcasecmp($title, $filename) == 0)
	  	  	      $title = "";
						endif;
					endif;
	        break;
	      default:
	        $title = $item->title;
	        break;
	    endswitch;
	  endif;

    $title = html::purify($title);
    if ($allowbbcode):
      $title = $this->bb2html($title, 1);
    else:
      $title = $this->bb2html($title, 2);
    endif;

    if ($limit_title_length):
      $title = text::limit_chars($title, $limit_title_length);
    endif;

    if ($title === "")
      $title = t(ucfirst($item->type)) . " " . $item->id;

    return $title;
  }

  public function breadcrumb_menu($theme, $parents) {
    $content = "";
    if ($this->breadcrumbs_position == "hide"):
	// Begin rWatcher Edit -- Add support for $theme->breadcrumbs.
    elseif (!empty($theme->breadcrumbs)):
      $content .= '<ul class="g-breadcrumbs g-' . $this->breadcrumbs_position . '">';
      $i = 0;
      foreach ($theme->breadcrumbs as $breadcrumb):
        $breadcrumb_class = "";
		if ($breadcrumb->last) : $breadcrumb_class = "g-active"; endif;
		if ($breadcrumb->first) : $breadcrumb_class = "g-first"; endif;
        $content .= '<li class="' . $breadcrumb_class . '">';
        $content .= (($i > 0)? " :: " : null );
        if (!$breadcrumb->last): $content .= '<a href="' . $breadcrumb->url . '">'; endif;
        $content .= html::purify(text::limit_chars($breadcrumb->title, $this->visible_title_length));
        if (!$breadcrumb->last): $content .= '</a>'; endif;
        $content .= '</li>';
        $i++;
      endforeach;
      $content .= '</ul>';
	// End rWatcher Edit.
    elseif ($this->item() and (!empty($parents) or (empty($parents) and $this->breadcrumbs_showinroot))):
      $content .= '<ul class="g-breadcrumbs g-' . $this->breadcrumbs_position . '">';
      $i = 0;
      if (!empty($parents)):
        foreach ($parents as $parent):
          $content .= '<li ' . (($i == 0)? " class=\"g-first\"" : null) . '>';
          $content .= (($i > 0)? " :: " : null );
          $content .= '<a href="' . $parent->url($parent == $this->item()->parent() ? "show={$this->item()->id}" : null) . '">';
          $content .= $this->get_item_title($parent, FALSE, $this->visible_title_length);
          $content .= '</a></li>';
          $i++;
        endforeach;
      endif;
      $content .= '<li class="g-active ' . (($i == 0)? " g-first" : null) . '"> '. (($i > 0)? " :: " : null ) . $this->get_item_title($this->item(), FALSE, $this->visible_title_length) . '</li>';
      $content .= '</ul>';
    endif;

    return $content;
  }

  protected function sidebar_menu_item($type, $url, $caption, $css) {
    if (!$this->is_sidebarallowed($type)):
      return "";
    endif;

    $iscurrent = ($this->sidebarvisible == $type);
    $content_menu = '<li>'; 
    if (!$iscurrent):
      $content_menu .= '<a title="' . $caption . '" href="' . $url . '?sb=' . $type . '" rel="nofollow">';
    endif;
    $content_menu .= '<span class="ui-icon g-sidebar-' . $css;
    if ($iscurrent):
      $content_menu .= ' g-current';
    endif;
    $content_menu .= '">' . $caption . '</span>';
    if (!$iscurrent):
      $content_menu .= '</a>';
    endif;

    return $content_menu . '</li>';
  }

  public function sidebar_menu($url) {
    if ($this->sidebarallowed != "any"):
      return "";
    endif;
    if ($this->page_subtype == "profile"):
      return "";
    endif;

    $content_menu  = $this->sidebar_menu_item("left", $url, t("Sidebar Left"), "left");
    $content_menu .= $this->sidebar_menu_item("top", $url, t("Sidebar Top"), "top");
    $content_menu .= $this->sidebar_menu_item("none", $url, t("No Sidebar"), "full");
    $content_menu .= $this->sidebar_menu_item("bottom", $url, t("Sidebar Bottom"), "bottom");
    $content_menu .= $this->sidebar_menu_item("right", $url, t("Sidebar Right"), "right");
    return '<ul id="g-viewformat">' . $content_menu . '</ul>';
  }

  public function add_paginator($position, $isalbum = TRUE) {
    if ($isalbum):
      $check = (($this->paginator_album == "both") or ($this->paginator_album == $position));
    else:
      $check = (($this->paginator_photo == "both") or ($this->paginator_photo == $position));
    endif;

    if ($check):
      return ($this->paginator());
    else:
      return "";
    endif;
  }

  public function get_bodyclass() {
    $body_class = "";
    if ($this->is_rtl):
      $body_class = "rtl";
    endif;
    if ($this->toolbar_large):
      $body_class .= " g-toolbar-large";
    endif;
    if ($this->viewmode == "mini"):
      $body_class .= " viewmode-mini";
    endif;
		$body_class .= " g-sidebar-" . $this->sidebarvisible;

    switch ($this->column_count):
      case 5:    
        $body_class .= " g-column-5";
        break;
      case 4:    
        $body_class .= " g-column-4";
        break;
      case 2:
        $body_class .= " g-column-2";
        break;
      case -1:
        $body_class .= " g-column-flex";
        break;
      case 3:
      default:
        $body_class .= " g-column-3";
        break;
    endswitch;


    switch ($this->thumb_ratio):
      case "digital_ex":
      case "film_ex":
      case "wide_ex":
      case "photo_ex":
        $body_class .= ' g-extended';
        break;
      default:
        break;
    endswitch;

    $body_class .= " g-" . $this->framepack;
    return 'class="' . trim($body_class) . '"';
  }

  public function get_thumb_link($item) {
		if ($item->is_album()):
		  return "";
		endif;
    if (item::viewable($item)):
   	  if (access::can("view_full", $item)):
   			$direct_link = $item->file_url();
   		else:
   			$direct_link = $item->resize_url();
      endif;
      return '<a title="' . $this->get_item_title($item) . '" style="display: none;" class="g-sb-preview" rel="g-preview" href="' . $direct_link . '">&nbsp;</a>';
    else:
  	  return "";
		endif;
  }

  public function get_thumb_element($item, $addcontext = FALSE, $linkonly = FALSE) {
    $thumb_item = $item;
    if ($this->thumb_random):
      if ($item->is_album() && ($rnd = item::random_query()->where("parent_id", "=", $item->id)->find()) && $rnd->loaded()):
        $thumb_item = $rnd;
      endif;
    endif;

    $item_class = $item->is_album() ? "g-album" : "g-photo";
    $content = '<li id="g-item-id-' . $item->id . '" class="g-item ' . $item_class . ' ' . $this->thumb_type;
    if ($item->is_album()):
    	$_thumb_descmode = $this->thumb_descmode_a;
		else:
    	$_thumb_descmode = $this->thumb_descmode;
		endif;

    $content .= ($_thumb_descmode == "bottom")? " g-expanded" : " g-default";

    if ($thumb_item->has_thumb()):
      $is_portrait = ($thumb_item->thumb_height > $thumb_item->thumb_width);
      $_shift = "";
      switch ($this->thumb_imgalign):
        case "center":
          if (($this->crop_factor == 1) and (!$is_portrait)): 
            $_shift = 'style="margin-top: ' . intval(($this->_thumb_size_y - $thumb_item->thumb_height) / 2) . 'px;"';
          elseif ($this->crop_factor > 0): 
            $_shift = 'style="margin-top: -' . intval(($thumb_item->thumb_height - $this->_thumb_size_y) / 2) . 'px;"';
          endif;
          break;
        case "bottom":
          if (($this->crop_factor == 1) and (!$is_portrait)): 
            $_shift = 'style="margin-top: ' . intval($this->_thumb_size_y - $thumb_item->thumb_height) . 'px;"';
          elseif ($this->crop_factor > 0): 
            $_shift = 'style="margin-top: -' . intval($thumb_item->thumb_height - $this->_thumb_size_y) . 'px;"';
          endif;
          break;
        case "fit":
          break;
        case "top":
        default:
          break;
      endswitch;
    else:
      $is_portrait = FALSE;
      $_shift = 'style="margin-top: 0px;"';
    endif;

    $content .= ($is_portrait)? " g-portrait" : " g-landscape";
    $content .= '">' . $this->thumb_top($item);

    $content .= '<div class="g-thumbslide">';
		$thumb_content = '<p class="g-thumbcrop">';

		$use_direct_link = (($this->disablephotopage) && (!$item->is_album())); 
		$class_name = "g-thumblink";
		if ($use_direct_link):
			$class_name .= ' g-sb-preview" rel="g-preview';
		  if (access::can("view_full", $item)):
				$direct_link = $item->file_url();
			else:
				$direct_link = $item->resize_url();
			endif;
		else:
			$direct_link = $item->url();
		endif;

    if ($use_direct_link && module::is_active("exif") && module::info("exif")): 
      $thumb_content .= '<a class="g-meta-exif-link g-dialog-link" href="' . url::site("exif/show/{$item->id}") . '" title="' . t("Photo details")->for_html_attr() . '">&nbsp;</a>';
    endif;

    $thumb_content .= '<a title="' . $this->get_item_title($item) . '" '. $_shift . ' class="' . $class_name . '" href="' . $direct_link . '">';
    if ($thumb_item->has_thumb()):
      if (($this->crop_factor > 1) && ($this->thumb_imgalign == "fit")):
      	if ($thumb_item->thumb_height > $this->_thumb_size_y):
      		if ($is_portrait):
      			$_max = $this->_thumb_size_y;
      		else:
	      	  $_max = intval($this->_thumb_size_x * ($this->_thumb_size_y / $thumb_item->thumb_height));
	      	endif;
	      else:
	        $_max = $this->_thumb_size_x;
        endif;
      	$_max = min($thumb_item->thumb_width, $_max);
        $thumb_content .= $thumb_item->thumb_img(array(), $_max);
      else:
        $thumb_content .= $thumb_item->thumb_img();
      endif;
    else:
      $thumb_content .= '<img title="No Image" alt="No Image" src="' . $this->url("images/missing-img.png") . '"/>';
    endif;
    $thumb_content .= '</a></p>';

    if (($this->thumb_metamode != "hide") and ($_thumb_descmode == "overlay_bottom")):
      $_thumb_metamode = "merged";
    else:
	    $_thumb_metamode = $this->thumb_metamode;
    endif;

    if (($_thumb_descmode == "overlay") or ($_thumb_descmode == "overlay_top") or ($_thumb_descmode == "overlay_bottom")):
      $thumb_content .= '<ul class="g-description ';
      if ($_thumb_descmode == "overlay_top"):
        $thumb_content .= 'g-overlay-top';
      endif;
      if ($_thumb_descmode == "overlay_bottom"):
        $thumb_content .= 'g-overlay-bottom';
      endif;
      $thumb_content .= '"><li class="g-title">' . $this->get_item_title($item, FALSE, $this->visible_title_length) . '</li>';
      if ($_thumb_metamode == "merged"):                                                    
        $thumb_content .= $this->thumb_info($item);
      endif;
      $thumb_content .= '</ul>';
    endif;

    if (($_thumb_metamode == "default") and ($_thumb_descmode != "overlay_bottom")): 
      $thumb_content .= '<ul class="g-metadata">' . $this->thumb_info($item) . '</ul>';
    endif;

    if ($_thumb_descmode == "bottom"):
      $thumb_content .= '<ul class="g-description">';
      $thumb_content .= '<li class="g-title">' . $this->get_item_title($item) . '</li>';
      if ($_thumb_metamode == "merged"): 
        $thumb_content .= $this->thumb_info($item);
      endif;
      $thumb_content .= '</ul>';
    endif;

    if ($addcontext):
      $_text = $this->context_menu($item, "#g-item-id-{$item->id} .g-thumbnail");
      $thumb_content .= (stripos($_text, '<li>'))? $_text : null;
    endif;

		try {
	    $view = new View("frame.html");
	    $view->thumb_content = $thumb_content;
	    $content .= $view;
    } catch (Exception $e) {
			$content .= $thumb_content;
    }

    $content .= '</div>';
    $content .= $this->thumb_bottom($item);
    $content .= '</li>';

    return $content;
  }

  public function get_block_html($module) {
    $active = block_manager::get_active("site_sidebar");
    $result = "";
    foreach ($active as $id => $desc) {
      if (($desc[0] == $module) and (method_exists("$desc[0]_block", "get"))) {
        $block = call_user_func(array("$desc[0]_block", "get"), $desc[1], $this);
        if (!empty($block)) {
          $block->id = $id;
          $block->css_id = $block->css_id . "-inline";
          $result .= $block;
        }
      }
    }
    return $result;
  }

  public function css_link($file, $direct = FALSE) {
    if (!$direct):
      $file = $this->url("css/" . $file);
    endif;
    return "\n<link rel=\"stylesheet\" href=\"" . $file . "\" type=\"text/css\" media=\"screen,print,projection\" />\n";
  }

  public function custom_css_inject($direct) {
    $_css = $this->custom_css_path;
    if ($_css != ""):
      $_fileonly = (stripos($_css, '/') === FALSE);

      if ($_fileonly):
        if (!$direct):
          return $this->css($_css);
        endif;
      else:
        if ($direct):
          return $this->css_link($_css, TRUE);
        endif;
      endif;
    else:
      return "";
    endif;
  }

  public function theme_js_inject() {
    $js = "";
    if ($this->show_root_page):
      $js .= $this->script("jquery.cycle.js");
    endif;
//    $js .= $this->script("jquery.touchslider.js");
    $js .= $this->script("ui.support.js");
    return $js;
  }

  public function theme_css_inject() {
    $css  = $this->css("screen.css");
    $css .= $this->css("colors.css");
    $css .= $this->css("frame.css");
    $css .= $this->custom_css_inject(FALSE);
    return $css;
  }

  public function credits() {
    if (module::get_var("gallery", "show_credits")):
      $version_string = SafeString::of_safe_html('Gallery ' . gallery::VERSION);
      return '<ul id="g-credits">'
        . '<li class="g-branding"><a id="g-gallery-logo" href="http://gallery.menalto.com" title="' . $version_string . '"></a>'
        . '<a id="g-theme-logo" href="http://codex.gallery2.org/Gallery3:Themes:greydragon" target="_blank" title="' . $this->themename . ' ' . $this->themeversion . ' (' . $this->colorpack . ')"></a></li>'
        . gallery_theme::credits()
        . '</ul>';
    else:
      return '';
    endif;
  }

  // $mode: bit 1 - use mix mode ($mode in [1, 3]), bit 2 - strips bbcode ($mode in [2, 3])
  public function bb2html($text, $mode) {
    // Syntax Sample:
    // --------------
    // [img]http://elouai.com/images/star.gif[/img]
    // [url="http://elouai.com"]eLouai[/url]
    // [size="25"]HUGE[/size]
    // [color="red"]RED[/color]
    // [b]bold[/b]
    // [i]italic[/i]
    // [u]underline[/u]
    // [list][*]item[*]item[*]item[/list]
    // [code]value="123";[/code]
    // [quote]John said yadda yadda yadda[/quote]
  
    static $bbcode_mappings = array(
      "#\\[b\\](.*?)\\[/b\\]#" => "<strong>$1</strong>",
      "#\\[i\\](.*?)\\[/i\\]#" => "<em>$1</em>",
      "#\\[u\\](.*?)\\[/u\\]#" => "<u>$1</u>",
      "#\\[s\\](.*?)\\[/s\\]#" => "<strike>$1</strike>",
      "#\\[o\\](.*?)\\[/o\\]#" => "<overline>$1</overline>",
      "#\\[url\\](.*?)\[/url\\]#" => "<a href=\"$1\">$1</a>",
      "#\\[url=(.*?)\\](.*?)\[/url\\]#" => "<a href=\"$1\" target=\"_blank\">$2</a>",
      "#\\[mail=(.*?)\\](.*?)\[/mail\\]#" => "<a href=\"mailto:$1\" target=\"_blank\">$2</a>",
      "#\\[img\\](.*?)\\[/img\\]#" => "<img src=\"$1\" alt=\"\" />",
      "#\\[img=(.*?)\\](.*?)\[/img\\]#" => "<img src=\"$1\" alt=\"$2\" />",
      "#\\[quote\\](.*?)\\[/quote\\]#" => "<blockquote><p>$1</p></blockquote>",
      "#\\[code\\](.*?)\\[/code\\]#" => "<pre>$1</pre>",
      "#\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]#" => "<span style=\"font-size: $1;\">$2</span>",
      "#\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]#" => "<span style=\"color: $1;\">$2</span>",
      "#\\[class=([^\\[]*)\\]([^\\[]*)\\[/class\\]#" => "<span class=\"$1\">$2</span>",
      "#\\[center\\](.*?)\\[/center\\]#" => "<div style=\"text-align: center;\">$1</div>",
      "#\\[list\\](.*?)\\[/list\\]#" => "<ul>$1</ul>",
      "#\\[ul\\](.*?)\\[/ul\\]#" => "<ul>$1</ul>",
      "#\\[li\\](.*?)\\[/li\\]#" => "<li>$1</li>",
    );
  
    static $bbcode_strip = '|[[\/\!]*?[^\[\]]*?]|si'; 

    if (($mode == 1) or ($mode == 3)):
      $newtext = str_replace("&lt;", "<", $text); 
      $newtext = str_replace("&gt;", ">", $newtext); 
      $newtext = str_replace("&quot;", "\"", $newtext); 
    else:
	    // Replace any html brackets with HTML Entities to prevent executing HTML or script 
      $newtext = str_replace("<", "&lt;", $text); 
      $newtext = str_replace(">", "&gt;", $newtext); 
      $newtext = str_replace("&amp;quot;", "&quot;", $newtext); 
    endif;

    // Convert new line chars to html <br /> tags 
    $newtext = nl2br($newtext);  

    if (strpos($text, "[") !== false):
      if (($mode == 2) or ($mode == 3)):
        $newtext = preg_replace($bbcode_strip, '', $newtext);
      else:
        $newtext = preg_replace(array_keys($bbcode_mappings), array_values($bbcode_mappings), $newtext);
      endif;
    endif;

    return stripslashes($newtext);  //stops slashing, useful when pulling from db
  }

  function curl_get_file_contents($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, $url);
    $contents = curl_exec($curl);
    curl_close($curl);
 
    if ($contents):
    	return $contents;
    else:
    	return FALSE;
    endif;
  }

  function valid_url($str) {
  	return ( ! preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $str)) ? FALSE : TRUE;
  }

  function get_slideshow_list($limit = 10) {
		if( ! function_exists('simplexml_load_string'))
			throw new Kohana_User_Exception('Feed Error', 'SimpleXML must be installed!');

		$items = array();
		$host = 'http://' . $_SERVER['SERVER_NAME'] . '/';

		$feed_url = $this->root_feed;
	  if (!$this->valid_url($feed_url)):
	    $feed_url = $host . $feed_url;
	  endif;

	  $use_file_load = ($this->valid_url($feed_url)) || is_file($feed_url); 

	  $er = error_reporting(0);
		// Try to get feed directly
		try {
	    try {
	      if ($use_file_load):
	    		$feed = simplexml_load_file($feed_url, 'SimpleXMLElement', LIBXML_NOCDATA);
	      else:
	    		$feed = simplexml_load_string($feed_url, 'SimpleXMLElement', LIBXML_NOCDATA);
	      endif;
	    } catch (Exception $e) {
			}
	  } catch (Exception $e) {
	  };

		if (isset($feed) && ($feed)):
			// Direct load worked fine
		else:
			// Direct load did not work, let's try CURL (URL file-access is disabled ?)
	    try {
        $file = $this->curl_get_file_contents($feed_url);
      	if ($file):
        	$feed = simplexml_load_string($file, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
      	endif;
    	} catch (Exception $e) {
    	};
		endif;
  	error_reporting($er);

  	if (isset($feed) && ($feed)):
			$feed = isset($feed->channel) ? $feed->channel->xpath("//media:content[contains(@url, 'var/resizes')]") : array();
			$i = 0;
			foreach ($feed as $item):
				if ($limit > 0 AND $i++ === $limit)
					break;
    		$items[] = (array) $item;
			endforeach;
		endif;
		return $items;
	}
}

?>