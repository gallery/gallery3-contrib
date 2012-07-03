<?php defined("SYSPATH") or die("No direct script access.") ?>
  <div id="g-tags-map-edit-admin">
  <h2> <?= t("Edit GPS Data for Tag ") . $tag_name ?> </h2>
<div ID="map" STYLE="width: 800px; height: 400px"></div>
  <div id="g-tags-map-instructions"><?=t("Double-Click on the map to create a new marker."); ?><br />
  <?=t("Drag and drop the marker to move it to a new location."); ?><br />
  </div>
  <?= $tagsmapedit_form ?>
</div>


<? if (isset($google_map_key) && ($google_map_key != "")) {
  print "<script src=\"http://www.google.com/jsapi?key=" . $google_map_key . "\" type=\"text/javascript\"></script>\n";
} else {
  print "<script src=\"http://www.google.com/jsapi\" type=\"text/javascript\"></script>\n";
}
?>

<script type="text/javascript">
  google.load("maps", "2.160");
  var lat = $("input[name=gps_latitude]");
  var lon = $("input[name=gps_longitude]");

  var map;

  function Gload() {
    if (GBrowserIsCompatible()) {
      map = new GMap2(document.getElementById("map"));
      map.addMapType(G_PHYSICAL_MAP);
      map.setMapType(G_PHYSICAL_MAP);
      map.enableScrollWheelZoom();
      map.setCenter(new GLatLng(lat.attr("value"), lon.attr("value")));
      map.setZoom(<?=$zoom ?>);
      map.addControl(new GSmallMapControl());
      map.addControl(new GMapTypeControl());
      map.addControl(new GScaleControl());

      GEvent.addListener(map,"dblclick",function(overlay, latlng) {
        lon.attr("value", latlng.x);
        lat.attr("value", latlng.y);
        var markeri = new GMarker(latlng, {draggable: true});
        map.addOverlay(markeri);
        GEvent.addListener(markeri, "dragend", function(point){
          lon.attr("value", point.x);
          lat.attr("value", point.y);
        });
      });
    }

    if (lon.attr("value") && lat.attr("value")){
      var point = new GLatLng(lat.attr("value"), lon.attr("value"));
      map.setCenter(point, 4);
      var marker = new GMarker(point, {draggable: true});
      map.addOverlay(marker);
      GEvent.addListener(marker, "dragend", function(point){
        lon.attr("value", point.x);
        lat.attr("value", point.y);
      });
    }
  }

  google.setOnLoadCallback(Gload);
</script>
