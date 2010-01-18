<?
include("Gallery3.php");

$SITE_URL = "http://example.com/gallery3";
$USER     = "admin";
$PASSWORD = "admin";

if (file_exists("local_config.php")) {
  include("local_config.php");
}

alert("Connect to $SITE_URL");
$gallery3 = Gallery3::connect($SITE_URL, $USER, $PASSWORD);
$root = $gallery3->get("gallery");
$tags = $gallery3->get("tags");

alert("Create a tag");
$tag = $tags->add()
  ->set_value("name", "My Tag")
  ->save();

alert("Create a new album");
$album = $root->add()
  ->set_value("type", "album")
  ->set_value("name", "Sample Album")
  ->set_value("title", "This is my Sample Album")
  ->save();

alert("Upload a photo");
$photo = $album->add()
  ->set_value("type", "photo")
  ->set_value("name", "Sample Photo.jpg")
  ->set_value("title", "Sample Photo")
  ->set_file("/tmp/foo.jpg")
  ->save();
alert("Added: " . $album->members[0] . "");

alert("Search for the photo");
$photos = $root->get("", array("name" => "Sample"));
alert("Found: {$photos->members[0]}");

alert("Grab a random photo");
$photos = $root->get("", array("random" => "true"));
alert("Found: {$photos->members[0]}");

alert("Tag the album");
$tag->add()
  ->set_value("url", $album->url)
  ->save();

alert("Tag the photo");
$tag->add()
  ->set_value("url", $photo->url)
  ->save();
alert("Tagged items: " . join($tag->members, " "));

alert("Un-tag the photo");
$tag->remove($photo->url);
alert("Tagged items: " . join($tag->members, " "));

alert("Find and modify the album");
$album = $root->get("Sample-Album")
  ->set_value("title", "This is my title")
  ->save();
alert("New title: $album->title");

// Now delete the album
alert("Delete the album");
$album->delete();

// Delete the tag
$tag->delete();

alert("Done!");

function alert($msg) {
  print "$msg <br/>";
  flush();
}
?>