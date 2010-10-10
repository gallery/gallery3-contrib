<?
class Theme_View extends Theme_View_Core {

  protected $photonav_position;
  protected $sidebarvisible;
  protected $sidebarallowed;
  protected $logopath;
  protected $thumb_descmode = "overlay";
  protected $photo_descmode = "overlay";
  protected $is_thumbmeta_visible = TRUE;
  protected $is_blockheader_visible = TRUE;
  protected $is_photometa_visible = FALSE;
  protected $disable_seosupport = FALSE;
  protected $mainmenu_position = "";
  protected $show_breadcrumbs = TRUE;
  protected $copyright = null;
  protected $show_guest_menu = FALSE;
  protected $loginmenu_position = "footer";
  protected $desc_allowbbcode = FALSE;
  protected $enable_pagecache = FALSE;
  protected $color_pack = "greydragon";

  protected $crop_factor = -1;
  protected $crop_class = "";
  protected $_thumb_size_x = 200;
  protected $_thumb_size_y = 200;

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

  public function load_sessioninfo() {
    $this->sidebarvisible = $_REQUEST['sb'];

    if (empty($this->sidebarvisible)):
      $session = Session::instance();
      $_sidebar_mode = $session->get("gd_sidebar");
      if ($_sidebar_mode):
        $this->sidebarvisible = $_sidebar_mode;
      else:
        $this->sidebarvisible = $this->ensureoptionsvalue("sidebar_visible", "right");
      endif;
    else:
      // Sidebar position is kept for 360 days
      Session::instance()->set("gd_sidebar", $this->sidebarvisible, time() + 31536000);
    endif;

    $this->sidebarallowed = $this->ensureoptionsvalue("sidebar_allowed", "any");
    $this->sidebarvisible = $this->ensurevalue($this->sidebarvisible, "right");

    if ($this->sidebarallowed == "none")  { $this->sidebarvisible = $this->ensureoptionsvalue("sidebar_visible", "right"); };
    if ($this->sidebarallowed == "right") { $this->sidebarvisible = "right"; }
    if ($this->sidebarallowed == "left")  { $this->sidebarvisible = "left"; }

    if ($this->item()):
      if ($this->ensureoptionsvalue("sidebar_albumonly", FALSE)):
        if (!$this->item()->is_album()):
          $this->sidebarallowed = "none"; 
          $this->sidebarvisible = "none";
        endif;
      endif;
    endif;

    $this->logopath = $this->ensureoptionsvalue("logo_path", url::file("lib/images/logo.png"));
    $this->show_guest_menu = $this->ensureoptionsvalue("show_guest_menu", FALSE);
    $this->horizontal_crop = $this->ensureoptionsvalue("horizontal_crop", FALSE);
    $this->thumb_descmode = $this->ensureoptionsvalue("thumb_descmode", "overlay");
    $this->photo_descmode = $this->ensureoptionsvalue("photo_descmode", "overlay");
    $this->is_thumbmeta_visible = ((!$this->ensureoptionsvalue("hide_thumbmeta", FALSE)) and module::is_active("info"));
    $this->is_photometa_visible = ((!$this->ensureoptionsvalue("hide_photometa", TRUE)) and module::is_active("info"));
    $this->disable_seosupport = $this->ensureoptionsvalue("disable_seosupport", FALSE);
    $this->is_blockheader_visible = (!$this->ensureoptionsvalue("hide_blockheader", FALSE));
    $this->mainmenu_position = $this->ensureoptionsvalue("mainmenu_position", "default");
    $this->show_breadcrumbs = (!$this->ensureoptionsvalue("hide_breadcrumbs", FALSE));
    $this->loginmenu_position = ($this->ensureoptionsvalue("loginmenu_position", "default"));
    $this->copyright = ($this->ensureoptionsvalue("copyright", null));
    $this->photonav_position = module::get_var("th_greydragon", "photonav_position", "top");
    $this->desc_allowbbcode = $this->ensureoptionsvalue("desc_allowbbcode", FALSE);
    $this->enable_pagecache = $this->ensureoptionsvalue("enable_pagecache", FALSE);
    $this->color_pack = $this->ensureoptionsvalue("color_pack", "greydragon");

    $cssfile = gallery::find_file("css/colorpacks/" . $this->color_pack, "colors.css", false);

    if (!$cssfile):
      $this->color_pack = 'greydragon';
    endif;

    switch (module::get_var("th_greydragon", "thumb_ratio")):
      case "digital":
        $this->crop_factor = 4/3;
        $this->crop_class = 'g-thumbtype-dgt';
        break;
      case "square":
        $this->crop_factor = 1;
        $this->crop_class = 'g-thumbtype-sqr';
        break;
      case "film":
        $this->crop_factor = 3/2;
        $this->crop_class = 'g-thumbtype-flm';
        break;
      case "photo":
      default:
        $this->crop_factor = 1;
        $this->crop_class = 'g-thumbtype-sqr';
        break;
    endswitch;

    $this->_thumb_size_y = floor($this->_thumb_size_x / $this->crop_factor);
  }

  public function is_sidebarallowed($align) {
    return (($this->sidebarallowed == "any") or ($this->sidebarallowed == $align));
  }

  public function breadcrumb_menu($theme, $parents) {
    $content = "";

    if ($theme->item() && !empty($parents)): 
      $content .= '<ul class="g-breadcrumbs ' . (($theme->mainmenu_position == "top")? "left" : "default") . '">';
      $i = 0;
      foreach ($parents as $parent):
        $content .= '<li ' . (($i == 0)? " class=\"g-first\"" : null) . '>';
        $content .= '<a href="' . $parent->url($parent == $theme->item()->parent() ? "show={$theme->item()->id}" : null) . '">';
        $content .= $theme->bb2html(html::purify($parent->title), 2);
        $content .= '</a></li>';
        $i++;
      endforeach;
      $content .= '<li class="g-active ' . (($i == 0)? " g-first" : null) . '">' . $theme->bb2html(html::purify($theme->item()->title), 2) . '</li>';
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
      $content_menu .= '<a title="' . $caption . '" href="' . $url . '?sb=' . $type . '">';
    endif;
    $content_menu .= '<span class="g-viewthumb-' . $css . ' ';
    if ($iscurrent):
      $content_menu .= 'g-viewthumb-current';
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

    $content_menu = ($this->sidebar_menu_item("left", $url, "Sidebar Left", "left"));
    $content_menu .= ($this->sidebar_menu_item("none", $url, "No Sidebar", "full"));
    $content_menu .= ($this->sidebar_menu_item("right", $url, "Sidebar Right", "right"));
    return '<ul id="g-viewformat">' . $content_menu . '</ul>';
  }

  public function add_paginator($position) {
    if (($this->photonav_position == "both") or ($this->photonav_position == $position)):
      return ($this->paginator());
    else:
      return "";
    endif;
  }

  public function get_thumb_element($item, $addcontext) {
    $item_class = $item->is_album() ? "g-album" : "g-photo";

    if (($this->sidebarallowed == "none") and ($this->sidebarvisible == "none")):
      $item_class .= " g-extra-column";
    endif;

    $content = '<li id="g-item-id-' . $item->id . '" class="g-item ' . $item_class . '">';
    $content .= $this->thumb_top($item);

    if (($this->crop_factor == 1) and ($item->thumb_width > $item->thumb_height)): 
      $_shift = 'style="margin-top: ' . floor(($this->_thumb_size_y - $item->thumb_height) / 2) . 'px;"';
    else:
      if (($this->crop_factor > 0) and ($item->thumb_width < $item->thumb_height)): 
        $_shift = 'style="margin-top: -' . floor(($item->thumb_height - $this->_thumb_size_y) / 2) . 'px;"';
      else:
        $_shift = "";
      endif;
    endif;

    $content .= '<div class="';
    if ($this->thumb_descmode == "bottom"):
      $content .= 'g-thumbslide-ext ';
    else:
      $content .= 'g-thumbslide ';
    endif;

    $content .= $this->crop_class . '"><p class="g-thumbcrop">';
    if ($this->thumb_descmode == "overlay"):
      $content .= '<span class="g-description">';
      $content .= '<strong>' . $this->bb2html(html::purify($item->title), 2) . '</strong>';   // html::purify(text::limit_chars($item->title, 44, "…"))
      $content .= '</span>';
    endif;
    $content .= '<a '. $_shift . ' class="g-thumlink" href="' . $item->url() . '">';
    if (($item->thumb_height == 0) or ($item->thumb_width == 0)):
      $content .= '<img title="No Image" alt="No Image" src="' . $this->url("images/missing-img.png") . '"/>';
    else:
      $content .= $item->thumb_img();
    endif;
    $content .= '</a></p>';

    if ($this->thumb_descmode == "bottom"):
      $content .= '<span class="g-description">';
      $content .= '<strong>' . $this->bb2html(html::purify($item->title), 2) . '</strong>';
      $content .= '</span>';
    endif;

    if (($this->is_thumbmeta_visible) and (module::is_active("info"))):
      $content .= '<ul class="g-metadata">' . $this->thumb_info($item) . '</ul>';
    endif;

    if ($addcontext):
      $_text = $this->context_menu($item, "#g-item-id-{$item->id} .g-thumbnail");
      $content .= (stripos($_text, '<li>'))? $_text : null;
    endif;
    
    $content .= '</div>';
    $content .= $this->thumb_bottom($item);
    $content .= '</li>';

    return $content;
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

    // Replace any html brackets with HTML Entities to prevent executing HTML or script 
    // Don't use strip_tags here because it breaks [url] search by replacing & with amp
    if (($mode == 1) or ($mode == 3)):
      $newtext = str_replace("&lt;", "<", $text); 
      $newtext = str_replace("&gt;", ">", $newtext); 
      $newtext = str_replace("&quot;", "\"", $newtext); 
    else:
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
}

?>