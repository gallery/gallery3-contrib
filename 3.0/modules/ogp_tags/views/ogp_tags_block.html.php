<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $tag_array = array();
  $item = $theme->item;

  // Set up the tags that describe the current page.
  $tag_array[] = array("og:site_name", str_replace("\"", "&quot;", str_replace("\"", "&quot;", item::root()->title)));
  $tag_array[] = array("og:url", url::abs_current(true));
  $tag_array[] = array("og:title", str_replace("\"", "&quot;", $item->title));

  // Set albums and photos to type = article, movies to type = video for embeding.
  if ($item->is_album() || $item->is_photo()) {
    $tag_array[] = array("og:type", "article");
  } elseif ($item->is_movie()) {
    $tag_array[] = array("og:type", "video");
  }

  // Use the thumb url for the page image.
  $tag_array[] = array("og:image", $item->thumb_url(true));

  // If there's a description, display it.
  if ($item->description != "") {
    $tag_array[] = array("og:description", html::purify($item->description));
  }

  // If the item is a photo, then we already have the mime type in the database, so display that.
  //   Otherwise, attempt to auto-detect mime type using getimagesize().
  if ($item->is_photo()) {
    $tag_array[] = array("og:image:type", $item->mime_type);
    $tag_array[] = array("og:image:width", $item->thumb_width);
    $tag_array[] = array("og:image:height", $item->thumb_height);
  } elseif ($item->is_album()) {
    $size = getimagesize($item->thumb_path());
    if ($size) {
      $tag_array[] = array("og:image:type", $size['mime']);
    }
  }

  // If the item is a movie, display some additional meta tags so facebook can embed the movie.
  if ($item->is_movie()) {

    // Make sure the movie is in a format flowplayer supports.
    $file_ext = "";
    if (strlen($item->name) > 4) {
      $file_ext = strtolower(substr($item->name, -4));
    }
    if (($file_ext == ".flv") || ($file_ext == ".mp4") || ($file_ext == ".m4v")) {
      $tag_array[] = array("og:video", "http://releases.flowplayer.org/swf/flowplayer-3.2.16.swf?config=" . urlencode("{'clip':{'url':'" . $item->file_url(true) . "'}}"));
      $tag_array[] = array("og:video:secure_url", "https://releases.flowplayer.org/swf/flowplayer-3.2.16.swf?config=" . urlencode("{'clip':{'url':'" . $item->file_url(true) . "'}}"));
      $tag_array[] = array("og:video:type", "application/x-shockwave-flash");
      $tag_array[] = array("og:video:width", $item->width);
      $tag_array[] = array("og:video:height", $item->height);
    }
  }

  // Loop through and display each meta tag.
  foreach ($tag_array as $one_tag) {
	print "<meta property=\"" . $one_tag[0] . "\" content=\"" . $one_tag[1] . "\" />\n";
  }
?>