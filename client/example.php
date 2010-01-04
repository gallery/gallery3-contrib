<?
include("Gallery3.php");

$SITE_URL = "http://example.com/gallery3";
$USER     = "admin";
$PASSWORD = "admin";

if (file_exists("local_config.php")) {
  include("local_config.php");
}

// Connect to our Gallery
$gallery3 = Gallery3::connect($SITE_URL, $USER, $PASSWORD);

$root = $gallery3->get("gallery");

// Create a new album
$album = $root->create_album()
  ->set_value("name", "Sample Album")
  ->set_value("title", "This is my Sample Album")
  ->save();

// Upload a new photo
$photo = $album->create_photo()
  ->set_value("name", "Sample Photo")
  ->set_value("title", "Sample Photo")
  ->set_file("/tmp/foo.jpg")
  ->save();

// Look up the album and modify it.
$album = $root->get("Sample-Album")
  ->set_value("title", "This is my title")
  ->save();

// Now delete the album
$album->delete();
?>