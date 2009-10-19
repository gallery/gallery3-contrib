<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="gTagsMapEditAdmin">
  <h2> <?= t("Edit GPS Data for Tag ") . $tag_name ?> </h2>
  <?= $tagsmapedit_form ?>
  </div>
<script src="http://www.google.com/jsapi?key=<?= module::get_var("tagsmap", "googlemap_api_key") ?>" type="text/javascript"></script>
<script type="text/javascript"> 
google.load("maps", "2");
		var lat = document.getElementById("gps_latitude").value;
		var lon = document.getElementById("gps_longitude").value;
		

  		var map;
		function Gload() {
			if (GBrowserIsCompatible()) {
				map = new GMap2(document.getElementById("map"));
					map.addMapType(G_PHYSICAL_MAP);
					map.setMapType(G_PHYSICAL_MAP);
					map.enableScrollWheelZoom();
					map.setCenter(new GLatLng(46.552656, 6.682645), 6);
					map.addControl(new GSmallMapControl()); // affiche le curseur de zoom
					//map.addControl(new GLargeMapControl()); // affiche le curseur de zoom
					map.addControl(new GMapTypeControl()); // affiche le curseur de déplacement
					map.addControl(new GScaleControl()); // affiche lechelle
					//map.addControl(new GOverviewMapControl()); // affiche le mini
					GEvent.addListener(map,"dblclick",function(overlay, latlng) {
						document.getElementById("gps_longitude").value = latlng.x;
						document.getElementById("gps_latitude").value = latlng.y;
						var markeri = new GMarker(latlng, {draggable: true});
						map.addOverlay(markeri);
						GEvent.addListener(markeri, "dragend", function(point){
							document.getElementById("gps_longitude").value = point.x;
							document.getElementById("gps_latitude").value = point.y;
							}
						);
						}
					);
				}
				
				if (lon != '' && lat != ''){
					var point = new GLatLng(lat,lon);
					map.setCenter(point, 8);
					var marker = new GMarker(point, {draggable: true});
					map.addOverlay(marker);
					GEvent.addListener(marker, "dragend", function(point){
						document.getElementById("gps_longitude").value = point.x;
						document.getElementById("gps_latitude").value = point.y;
						}
					);
				}
			}
			
    google.setOnLoadCallback(Gload);
</script>
  <div ID="map" STYLE="width: 800px; height: 400px"></div>

