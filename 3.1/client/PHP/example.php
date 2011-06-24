<?
include("Gallery3.php");

$SITE_URL = "http://example.com/gallery3/index.php/rest";
$USER     = "admin";
$PASSWORD = "admin";

if (file_exists("local_config.php")) {
  include("local_config.php");
}

alert("Connect to $SITE_URL");
$auth = Gallery3::login($SITE_URL, $USER, $PASSWORD);
$root = Gallery3::factory("$SITE_URL/item/1", $auth);
$tags = Gallery3::factory("$SITE_URL/tags", $auth);
$comments = Gallery3::factory("$SITE_URL/comments", $auth);

$tag = Gallery3::factory()
  ->set("name", "My Tag")
  ->create($tags->url, $auth);
alert("Created tag: <b>{$tag->url}</b>");

$album = Gallery3::factory()
  ->set("type", "album")
  ->set("name", "Sample Album")
  ->set("title", "This is my Sample Album")
  ->create($root->url, $auth);
alert("Created album: <b>{$album->url} {$album->data->entity->title}</b>");


alert("Modify the album");
$album
  ->set("title", "This is the new title")
  ->save();
alert("New title: <b>{$album->data->entity->title}</b>");

for ($i = 0; $i < 2; $i++) {
  $photo = Gallery3::factory()
    ->set("type", "photo")
    ->set("name", "Sample Photo.png")
    ->set("title", "Sample Photo")
    ->set_file("test1.png")
    ->create($album->url, $auth);
  alert("Uploaded photo: <b>{$photo->url}</b>");
}
$album->load();
alert("Album members: <b>" . join(", ", $album->data->members) . "</b>");


alert("Replace the data file");
$photo->set_file("test2.png")
  ->save();


$comment = Gallery3::factory()
  ->set("item", $album->data->members[0])
  ->set("type", "comment")
  ->set("text", "This is a random comment-- whee!")
  ->create($comments->url, $auth);
alert("Comment: <b>{$comment->url}</b>");

alert("Reorder the album");
$album
  ->set_members(array($album->data->members[1], $album->data->members[0]))
  ->set("sort_column", "weight")
  ->save();
alert("New order: <b>" . join(", ", $album->data->members) . "</b>");

alert("Search for the photo");
$photos = Gallery3::factory($root->url, $auth)
  ->set("name", "Sample")
  ->load();
alert("Found: {$photos->data->members[0]}");


alert("Grab a random photo");
$photos = Gallery3::factory("{$root->url}?random=true", $auth)
  ->load();
alert("Found: {$photos->data->members[0]}");


alert("Tag the album (using the album's relationships: {$album->data->relationships->tags->url})");
$tag_relationship1 = Gallery3::factory()
  ->set("tag", $tag->url)
  ->set("item", $root->url)
  ->create($album->data->relationships->tags->url, $auth);
alert("Tag: {$tag_relationship1->url}");


alert("Tag the photo (using the tag's relationships: {$tag->data->relationships->items->url})");
$tag_relationship2 = Gallery3::factory()
  ->set("tag", $tag->url)
  ->set("item", $photo->url)
  ->create($tag->data->relationships->items->url, $auth);
alert("Tag: {$tag_relationship2->url}");

alert("Un-tag the photo");
$tag_relationship2->delete();
$tag->load();
alert("1 remaining tag: <b>{$tag->data->relationships->items->members[0]}</b>");

alert("Delete the album and tag");
$album->delete();
$tag->delete();

alert("Done!");

function alert($msg) {
  print "$msg <br/>\n";
  flush();
}
?>