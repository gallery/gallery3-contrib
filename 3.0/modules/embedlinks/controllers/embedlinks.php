<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class EmbedLinks_Controller extends Controller {
  /**
   * Display the EXIF data for an item.
   */
  public function showhtml($item_id) {
    // Generate the Dialog Box for HTML links.
    $item = ORM::factory("item", $item_id);
    access::required("view", $item);

    // If the current page is an album, only display two links.
    if ($item->is_album()) {
      $linkArray[0] = array("Text:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;>Click Here</a>");
      $linkArray[1] = array("Thumbnail:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;><img src=&quot;" . $item->thumb_url(true) . "&quot;></a>");
      $linkTitles[0] = array("Link To This Album:", 2);
        
    // If the item is a movie, don't display resize links, do display an embed link.
    } elseif ($item->is_movie()) {
      // Link to the current page.
      $linkArray[0] = array("Text:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;>Click Here</a>");
      $linkArray[1] = array("Thumbnail:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;><img src=&quot;" . $item->thumb_url(true) . "&quot;></a>");
      $linkTitles[0] = array("Link To This Page:", 2);  
      
      // If the visitor has suficient privlidges to see the fullsized
      //    version of the current image, then display links to it.
      if (access::can("view_full", $item)) {
        $linkArray[2] = array("Text:", "<a href=&quot;" . $item->file_url(true) . "&quot;>Click Here</a>");
        $linkArray[3] = array("Thumbnail:", "<a href=&quot;" . $item->file_url(true) . "&quot;><img src=&quot;" . $item->thumb_url(true) . "&quot;></a>");
        $linkArray[4] = array("Embed:", "<object width=&quot;" . $item->width . "&quot; height=&quot;" . $item->height . "&quot; data=&quot;" . url::abs_file("lib/flowplayer.swf") . "&quot; type=&quot;application/x-shockwave-flash&quot;><param name=&quot;movie&quot; value=&quot;" . url::abs_file("lib/flowplayer.swf") . "&quot; /><param name=&quot;allowfullscreen&quot; value=&quot;true&quot; /><param name=&quot;allowscriptaccess&quot; value=&quot;always&quot; /><param name=&quot;flashvars&quot; value='config={&quot;plugins&quot;:{&quot;pseudo&quot;:{&quot;url&quot;:&quot;flowplayer.h264streaming.swf&quot;},&quot;controls&quot;:{&quot;backgroundColor&quot;:&quot;#000000&quot;,&quot;backgroundGradient&quot;:&quot;low&quot;}},&quot;clip&quot;:{&quot;provider&quot;:&quot;pseudo&quot;,&quot;url&quot;:&quot;" . $item->file_url(true) . "&quot;},&quot;playlist&quot;:[{&quot;provider&quot;:&quot;pseudo&quot;,&quot;url&quot;:&quot;" . $item->file_url(true) . "&quot;}]}' /></object>");
        $linkTitles[1] = array("Link To The Video File:", 3);        
      }

    // Or else assume the item is a photo.
    } else {
      // Link to the current page.
      $linkArray[0] = array("Text:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;>Click Here</a>");
      $linkArray[1] = array("Thumbnail:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;><img src=&quot;" . $item->thumb_url(true) . "&quot;></a>");
      $linkArray[2] = array("Resized:", "<a href=&quot;" . url::abs_site("{$item->type}s/{$item->id}") . "&quot;><img src=&quot;" . $item->resize_url(true) . "&quot;></a>");
      $linkTitles[0] = array("Link To This Page:", 3);  

      // Link to the "resized" version of the current image.
      $linkArray[3] = array("Text:", "<a href=&quot;" . $item->resize_url(true) . "&quot;>Click Here</a>");
      $linkArray[4] = array("Thumbnail:", "<a href=&quot;" . $item->resize_url(true) . "&quot;><img src=&quot;" . $item->thumb_url(true) . "&quot;></a>");
      $linkArray[5] = array("Image:", "<img src=&quot;" . $item->resize_url(true) . "&quot;>");
      $linkTitles[1] = array("Link To The Resized Image:", 3);  
      
      // If the visitor has suficient privlidges to see the fullsized
      //    version of the current image, then display links to it.
      if (access::can("view_full", $item)) {
        $linkArray[6] = array("Text:", "<a href=&quot;" . $item->file_url(true) . "&quot;>Click Here</a>");
        $linkArray[7] = array("Thumbnail:", "<a href=&quot;" . $item->file_url(true) . "&quot;><img src=&quot;" . $item->thumb_url(true) . "&quot;></a>");
        $linkArray[8] = array("Resized:", "<a href=&quot;" . $item->file_url(true) . "&quot;><img src=&quot;" . $item->resize_url(true) . "&quot;></a>");
        $linkTitles[2] = array("Link To The Full Size Image:", 3);        
      }
    }
    
    $view = new View("embedlinks_htmldialog.html");
    $view->titles = $linkTitles;
    $view->details = $linkArray;
    print $view;
  }

  public function showbbcode($item_id) {
    // Generate the Dialog Box for BBCode links.
  $item = ORM::factory("item", $item_id);
    access::required("view", $item);
    
    // If the current page is an album, only display two links.
    if ($item->is_album()) {
      $linkArray[0] = array("Text:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "]Click Here[/url]");
      $linkArray[1] = array("Thumbnail:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "][img]" . $item->thumb_url(true) . "[/img][/url]");
      $linkTitles[0] = array("Link To This Album:", 2);  
    
    // If the item is a movie, don't display resize links.
    } elseif ($item->is_movie()) {
      // Link to the current page.
      $linkArray[0] = array("Text:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "]Click Here[/url]");
      $linkArray[1] = array("Thumbnail:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "][img]" . $item->thumb_url(true) . "[/img][/url]");
      $linkTitles[0] = array("Link To This Page:", 2);  
      
      // If the visitor has suficient privlidges to see the fullsized
      //    version of the current image, then display links to it.
      if (access::can("view_full", $item)) {
        $linkArray[2] = array("Text:", "[url=" . $item->file_url(true) . "]Click Here[/url]");
        $linkArray[3] = array("Thumbnail:", "[url=" . $item->file_url(true) . "][img]" . $item->thumb_url(true) . "[/img][/url]");
        $linkTitles[1] = array("Link To The Video File:", 2);        
      }
    
    // Or else assume the item is a photo.
    } else {
      // Link to the current page.
      $linkArray[0] = array("Text:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "]Click Here[/url]");
      $linkArray[1] = array("Thumbnail:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "][img]" . $item->thumb_url(true) . "[/img][/url]");
      $linkArray[2] = array("Resized:", "[url=" . url::abs_site("{$item->type}s/{$item->id}") . "][img]" . $item->resize_url(true) . "[/img][/url]");
      $linkTitles[0] = array("Link To This Page:", 3);  

      // Link to the "resized" version of the current image.
      $linkArray[3] = array("Text:", "[url=" . $item->resize_url(true) . "]Click Here[/url]");
      $linkArray[4] = array("Thumbnail:", "[url=" . $item->resize_url(true) . "][img]" . $item->thumb_url(true) . "[/img][/url]");
      $linkArray[5] = array("Image:", "[img]" . $item->resize_url(true) . "[/img]");
      $linkTitles[1] = array("Link To The Resized Image:", 3);  
      
      // If the visitor has suficient privlidges to see the fullsized
      //    version of the current image, then display links to it.
      if (access::can("view_full", $item)) {
        $linkArray[6] = array("Text:", "[url=" . $item->file_url(true) . "]Click Here[/url]");
        $linkArray[7] = array("Thumbnail:", "[url=" . $item->file_url(true) . "][img]" . $item->thumb_url(true) . "[/img][/url]");
        $linkArray[8] = array("Resized:", "[url=" . $item->file_url(true) . "][img]" . $item->resize_url(true) . "[/img][/url]");
        $linkTitles[2] = array("Link To The Full Size Image:", 3);        
      }
    }
    
    $view = new View("embedlinks_bbcodedialog.html");
    $view->titles = $linkTitles;
    $view->details = $linkArray;
    print $view;
  }

  public function showfullurl($item_id) {
    // Generate the Dialog Box for the URLs to the items thumb, resize and fullsize image.
  $item = ORM::factory("item", $item_id);
    access::required("view", $item);
    
    // If the current page is an album, only display a URL and thumnail fields.
    if ($item->is_album()) {
      $linkArray[0] = array("Album URL:", url::abs_site("{$item->type}s/{$item->id}"));
      $linkArray[1] = array("Thumbnail:", $item->thumb_url(true));
      $linkTitles[0] = array("URLs:", 2); 

    // If the item is a movie, do not display the resize url.
    } elseif ($item->is_movie()) {
      // Link to the current page.    
      $linkArray[0] = array("This Page:", url::abs_site("{$item->type}s/{$item->id}"));
      $linkArray[1] = array("Thumbnail:", $item->thumb_url(true));
      
      // If the visitor has suficient privlidges to see the fullsized
      //    version of the current image, then display its URL.
      if (access::can("view_full", $item)) {
        $linkArray[2] = array("Video File:", $item->file_url(true));
        $linkTitles[0] = array("URLs:", 3);  
      } else {
        $linkTitles[0] = array("URLs:", 2);  
      }
    
    // Or else assume the item is a photo.
    } else {
      // Link to the current page.    
      $linkArray[0] = array("This Page:", url::abs_site("{$item->type}s/{$item->id}"));
      $linkArray[1] = array("Thumbnail:", $item->thumb_url(true));
      $linkArray[2] = array("Resized:", $item->resize_url(true));
      
      // If the visitor has suficient privlidges to see the fullsized
      //    version of the current image, then display its URL.
      if (access::can("view_full", $item)) {
        $linkArray[3] = array("Full Size:", $item->file_url(true));
        $linkTitles[0] = array("URLs:", 4);  
      } else {
        $linkTitles[0] = array("URLs:", 3);  
      }
    }
    
    $view = new View("embedlinks_fullurldialog.html");
    $view->titles = $linkTitles;
    $view->details = $linkArray;
    print $view;
  }
  
}
