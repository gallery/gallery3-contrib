<?
include("Gallery3.php");

$SITE_URL = "http://example.com/gallery3";
$USER     = "admin";
$PASSWORD = "admin";

if (file_exists("local_config.php")) {
  include("local_config.php");
}

print "Connect to $SITE_URL <br/>";
$gallery3 = Gallery3::connect($SITE_URL, $USER, $PASSWORD);
$root = $gallery3->get("gallery");
$tags = $gallery3->get("tags");

print "Create a tag <br/>";
$tag = $tags->create()
  ->set_value("name", "My Tag")
  ->save();

print "Create a new album <br/>";
$album = $root->create()
  ->set_value("type", "album")
  ->set_value("name", "Sample Album")
  ->set_value("title", "This is my Sample Album")
  ->save();

print "Upload a photo <br/>";
$photo = $album->create()
  ->set_value("type", "photo")
  ->set_value("name", "Sample Photo")
  ->set_value("title", "Sample Photo")
  ->set_file("/tmp/foo.jpg")
  ->save();

print "Tag the photo <br/>";
$tag->create()
  ->set_value("url", $photo->url)
  ->save();

print "Modify the album <br/>";
$album = $root->get("Sample-Album")
  ->set_value("title", "This is my title")
  ->save();

// Now delete the album
print "Delete the album <br/>";
$album->delete();

print "Done! <br/>";
?>