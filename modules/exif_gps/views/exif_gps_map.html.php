<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= module::get_var("exif_gps", "googlemap_api_key"); ?>"></script>
<script type="text/javascript">
  google.load("maps", "3",{"other_params":"sensor=false"});
  function initialize() {
    var latlng = new google.maps.LatLng(0,0);
    var myOptions = {
      zoom: 1,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
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
        <? $windowContent = "<div class=\\\"g-exif-gps-thumb\\\"><a href=\\\"" . 
                            $item->url() . "\\\">" . 
                            str_replace("\"", "\\\"", $item->thumb_img(array("class" => "g-exif-gps-thumbnail"))) . 
                            "</a></div>"; ?>
      <? } elseif (($currLat != $item_coordinates->latitude) && ($currLong != $item_coordinates->longitude)) { ?>
        var infowindow<?=$counter; ?> = new google.maps.InfoWindow({ 
          content: "<?=$windowContent; ?>",
          size: new google.maps.Size(50,50)
        });
        google.maps.event.addListener(marker<?=$counter; ?>, 'click', function() {
          infowindow<?=$counter; ?>.open(map,marker<?=$counter; ?>);
        });
        <? $counter++; ?>
        <? $currLat = $item_coordinates->latitude; ?>
        <? $currLong = $item_coordinates->longitude; ?>
        var marker<?=$counter; ?> = new google.maps.Marker({
          position: new google.maps.LatLng(<?=$item_coordinates->latitude; ?>,<?=$item_coordinates->longitude; ?>), 
          map: map
        });
        <? $windowContent = "<div class=\\\"g-exif-gps-thumb\\\"><a href=\\\"" . 
                            $item->url() . "\\\">" . 
                            str_replace("\"", "\\\"", $item->thumb_img(array("class" => "g-exif-gps-thumbnail"))) . 
                            "</a></div>"; ?>
      <? } else { ?>
        <? $windowContent = $windowContent . " <div class=\\\"g-exif-gps-thumb\\\"><a href=\\\"" . 
                            $item->url() . "\\\">" . 
                            str_replace("\"", "\\\"", $item->thumb_img(array("class" => "g-exif-gps-thumbnail"))) . 
                            "</a></div>"; ?>
      <? } ?>
    <? } ?>
    var infowindow<?=$counter; ?> = new google.maps.InfoWindow({ 
      content: "<?=$windowContent; ?>",
      size: new google.maps.Size(50,50)
    });
    google.maps.event.addListener(marker<?=$counter; ?>, 'click', function() {
      infowindow<?=$counter; ?>.open(map,marker<?=$counter; ?>);
    });
    map.fitBounds(glatlngbounds);
  }

  google.setOnLoadCallback(initialize);

</script>
<div id="map_canvas" style="width:600px; height:480px;"></div>