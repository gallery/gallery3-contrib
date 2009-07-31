<?php defined("SYSPATH") or die("No direct script access.") ?>
<? if ($map_fullsize == true) { ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
  <head> 
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
    <title>Gallery: Map</title> 
  </head>
  <body>
<? } ?>

<script src="http://www.google.com/jsapi?key=<?=$google_map_key ?>" type="text/javascript"></script>
<script type="text/javascript"> 
  google.load("maps", "2.160");

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
  
  google.setOnLoadCallback(initialize);
</script>

<? if ($map_fullsize == true) { ?>
  <div id="map_canvas" style="width: 100%; height: 100%"></div>
  </body></html>
<? } else { ?>
  <div id="map_canvas" style="width: 600px; height: 480px"></div> <br/>
  <a href="<?= url::site("tagsmap/googlemap/fullsize/1")?>">
           <?= t("View Fullsize")?>
  </a><br/><br/>
<? } ?>
