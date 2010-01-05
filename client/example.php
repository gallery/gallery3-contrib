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
$tag = $tags->add()
  ->set_value("name", "My Tag")
  ->save();

print "Create a new album <br/>";
$album = $root->add()
  ->set_value("type", "album")
  ->set_value("name", "Sample Album")
  ->set_value("title", "This is my Sample Album")
  ->save();

print "Upload a photo <br/>";
$photo = $album->add()
  ->set_value("type", "photo")
  ->set_value("name", "Sample Photo")
  ->set_value("title", "Sample Photo")
  ->set_file("/tmp/foo.jpg")
  ->save();
print "Added: " . $album->members[0] . " <br/>";

print "Tag the album <br/>";
$tag->add()
  ->set_value("url", $album->url)
  ->save();

print "Tag the photo <br/>";
$tag->add()
  ->set_value("url", $photo->url)
  ->save();
print "Tagged items: " . join($tag->members, " ") . "<br/>";

print "Un-tag the photo <br/>";
$tag->remove($photo->url);
print "Tagged items: " . join($tag->members, " ") . "<br/>";

print "Find and modify the album <br/>";
$album = $root->get("Sample-Album")
  ->set_value("title", "This is my title")
  ->save();
print "New title: $album->title <br/>";

// Now delete the album
print "Delete the album <br/>";
$album->delete();

// Delete the tag
$tag->delete();

print "Done! <br/>";
?>