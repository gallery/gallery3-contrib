<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $latitude = 0;
  $longitude = 0;
?>
<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= module::get_var("exif_gps", "googlemap_api_key"); ?>"></script>
<script type="text/javascript">
  google.load("maps", "3",{"other_params":"sensor=false"});
  var google_zoom_hack = false;

  function initialize() {
    var latlng = new google.maps.LatLng(0,0);
    var myOptions = {
      zoom: 1,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.<?=$sidebar_map_type; ?>
    };
    var map = new google.maps.Map(document.getElementById("sidebar_map_canvas"), myOptions);
    var glatlngbounds = new google.maps.LatLngBounds( ); // This is so we can auto center the map.
    <? $counter = 1; ?>
    <? foreach ($items as $item) { ?>
      <? $item_coordinates = ORM::factory("exif_coordinate")->where("item_id", "=", $item->id)->find(); ?>
      glatlngbounds.extend(new google.maps.LatLng(<?=$item_coordinates->latitude; ?>,<?=$item_coordinates->longitude; ?>));
      <? if (!isset($currLat)) { ?>
        <? $currLat = $item_coordinates->latitude; ?>
        <? $currLong = $item_coordinates->longitude; ?>
        <? $latitude = $item_coordinates->latitude; ?>
        <? $longitude = $item_coordinates->longitude; ?>
        var marker<?=$counter; ?> = new google.maps.Marker({
          position: new google.maps.LatLng(<?=$item_coordinates->latitude; ?>,<?=$item_coordinates->longitude; ?>), 
          map: map
        });
      <? } elseif (($currLat != $item_coordinates->latitude) && ($currLong != $item_coordinates->longitude)) { ?>
        <? $counter++; ?>
        <? $currLat = $item_coordinates->latitude; ?>
        <? $currLong = $item_coordinates->longitude; ?>
        var marker<?=$counter; ?> = new google.maps.Marker({
          position: new google.maps.LatLng(<?=$item_coordinates->latitude; ?>,<?=$item_coordinates->longitude; ?>), 
          map: map
        });
      <? } else { ?>
      <? } ?>
    <? } ?>
    <? if (($max_autozoom = module::get_var("exif_gps", "googlemap_max_autozoom")) != "") : ?>
    // If there is a maximum auto-zoom value, then set up an event to check the zoom
    // level the first time it is changed, and adjust it if necessary.
    // (if we call map.getZoom right after .fitBounds, getZoom will return the initial 
    // zoom level, not the auto zoom level, this way we get the auto zoomed value).
    google.maps.event.addListener(map, 'zoom_changed', function() {
      if (google_zoom_hack) {
        if (map.getZoom() > <?= $max_autozoom ?>) map.setZoom(<?= $max_autozoom ?>);
        google_zoom_hack = false;
      }
    });
    <? endif ?>

    google_zoom_hack = true;
    map.fitBounds(glatlngbounds);
  }

  google.setOnLoadCallback(initialize);

</script>

<div id="sidebar_map_canvas" style="width:205px; height:214px"><img src="http://maps.google.com/maps/api/staticmap?center=<?=$latitude; ?>,<?=$longitude; ?>&zoom=<?= module::get_var("exif_gps", "sidebar_zoom"); ?>&size=205x214&maptype=<?=$sidebar_map_type ?>&markers=color:red|color:red|<?=$latitude; ?>,<?=$longitude; ?>&sensor=false"></div>
