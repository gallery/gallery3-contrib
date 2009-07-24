<?php defined("SYSPATH") or die("No direct script access.") ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$google_map_key ?>&sensor=false"
  type="text/javascript">
</script>

<script type="text/javascript">   
  function initialize() {
    if (GBrowserIsCompatible()) {
      var map = new GMap2(document.getElementById("map_canvas"));
      map.setCenter(new GLatLng(<?=$google_map_latitude ?>, 
                                <?=$google_map_longitude ?>));
      map.setZoom(<?=$google_map_zoom ?>);
      map.setUIToDefault();

      // Make Google Earth an Option.
      map.addMapType(G_SATELLITE_3D_MAP);
      var mapControl = new GMapTypeControl();
      map.addControl(mapControl);
            
      map.setMapType(<?=$google_map_type ?>);
      
      <? foreach ($tags_gps as $oneGPS): ?>
      {
          
        var myGeographicCoordinates = new GLatLng(<?= $oneGPS->latitude ?>, 
                                                  <?= $oneGPS->longitude ?>);
        map.addOverlay(createMarker(myGeographicCoordinates, 
                                      "<?= $oneGPS->description ?>", 
                                      "<?= url::site("tags/$oneGPS->tag_id")?>", 
                                      "<?= ORM::factory("tag", $oneGPS->tag_id)->name ?>"
                                    ));
      }
      <? endforeach ?>
      
      function createMarker(point, description, tagURL, tagName) {
        var marker = new GMarker(point);
    	GEvent.addListener(marker, "click", function() {
          var myHtml = description + "<br/><br/>" + 
                       "Tag: <a href=\"" + tagURL + "\">" + tagName + "</a>";
    	  map.openInfoWindowHtml(point, myHtml);
        });
  
        return marker;
      }
    }
  }


</script>

<a href="#" onclick="javascript:initialize();">Load Map</a>

<div id="map_canvas" style="width: 600px; height: 480px"></div>

      
<br/>
