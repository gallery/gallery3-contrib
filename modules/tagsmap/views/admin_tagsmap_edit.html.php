<?php defined("SYSPATH") or die("No direct script access.") ?>
  <div id="g-tags-map-edit-admin">
  <h2> <?= t("Edit GPS Data for Tag ") . $tag_name ?> </h2>
<div ID="map" STYLE="width: 800px; height: 400px"></div>
  <div id="g-tags-map-instructions"><?=t("Double-Click on the map to create a new marker."); ?><br />
  <?=t("Drag and drop the marker to move it to a new location."); ?><br />
  </div>
  <?= $tagsmapedit_form ?>
</div>


<script src="http://www.google.com/jsapi?key=<?= module::get_var("tagsmap", "googlemap_api_key") ?>" type="text/javascript"></script>
<script type="text/javascript"> 
  google.load("maps", "2.160");
  var lat = document.getElementById("gps_latitude").value;
  var lon = document.getElementById("gps_longitude").value;

  var map;

  function Gload() {
    if (GBrowserIsCompatible()) {
      map = new GMap2(document.getElementById("map"));
      map.addMapType(G_PHYSICAL_MAP);
      map.setMapType(G_PHYSICAL_MAP);
      map.enableScrollWheelZoom();
      map.setCenter(new GLatLng(<?=module::get_var("tagsmap", "googlemap_latitude"); ?>, <?=module::get_var("tagsmap", "googlemap_longitude"); ?>));
      map.setZoom(<?=module::get_var("tagsmap", "googlemap_zoom"); ?>);
      map.addControl(new GSmallMapControl()); // affiche le curseur de zoom
      map.addControl(new GMapTypeControl()); // affiche le curseur de déplacement
      map.addControl(new GScaleControl()); // affiche lechelle
	  
      GEvent.addListener(map,"dblclick",function(overlay, latlng) {
        document.getElementById("gps_longitude").value = latlng.x;
        document.getElementById("gps_latitude").value = latlng.y;
        var markeri = new GMarker(latlng, {draggable: true});
        map.addOverlay(markeri);
        GEvent.addListener(markeri, "dragend", function(point){
          document.getElementById("gps_longitude").value = point.x;
          document.getElementById("gps_latitude").value = point.y;
        });
      });
    }
				
    if (lon != '' && lat != ''){
      var point = new GLatLng(lat,lon);
      map.setCenter(point, 8);
      var marker = new GMarker(point, {draggable: true});
      map.addOverlay(marker);
      GEvent.addListener(marker, "dragend", function(point){
        document.getElementById("gps_longitude").value = point.x;
        document.getElementById("gps_latitude").value = point.y;
      });
    }
  }
			
  google.setOnLoadCallback(Gload);
</script>

