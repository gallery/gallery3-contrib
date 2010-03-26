<?php defined("SYSPATH") or die("No direct script access.") ?>
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
    <? if (module::get_var("exif_gps", "googlemap_max_autozoom") != "") : ?>
    google.maps.event.addListener(map, 'zoom_changed', function() {
      if (google_zoom_hack) {
        if (map.getZoom() > 18) map.setZoom(18);
        google_zoom_hack = false;
      }
    });
    <? endif ?>

    google_zoom_hack = true;
    map.fitBounds(glatlngbounds);
  }

  google.setOnLoadCallback(initialize);

</script>

<div id="sidebar_map_canvas" style="width:205px; height:214px"></div>
