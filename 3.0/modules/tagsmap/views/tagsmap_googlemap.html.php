<?php defined("SYSPATH") or die("No direct script access.") ?>

<? if ($map_fullsize == true) { ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
  <head> 
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
    <title><?= t("Gallery :: Map") ?></title> 
  </head>
  <body>
<? } ?>

<? if (isset($google_map_key) && ($google_map_key != "")) {
  print "<script src=\"http://www.google.com/jsapi?key=" . $google_map_key . "\" type=\"text/javascript\"></script>\n";
} else {
  print "<script src=\"http://www.google.com/jsapi\" type=\"text/javascript\"></script>\n";
}
?>
<script type="text/javascript"> 
  google.load("maps", "2.160");

  function initialize() {
    if (GBrowserIsCompatible()) {
      var map = new GMap2(document.getElementById("map_canvas"));

      // Make Google Earth an Option.
      map.addMapType(G_SATELLITE_3D_MAP);
      var mapControl = new GMapTypeControl();
      map.addControl(mapControl);
      map.enableScrollWheelZoom();

      // Set map defaults.
      map.setCenter(new GLatLng(<?=$google_map_latitude ?>, 
                                <?=$google_map_longitude ?>));
      map.setZoom(<?=$google_map_zoom ?>);
      map.setUIToDefault();      
      map.setMapType(<?=$google_map_type ?>);

      // Function for making the clickable markers.
      function createMarker(point, description, tagURL, tagName) {
        var marker = new GMarker(point);
    	GEvent.addListener(marker, "click", function() {
          var myHtml = "<div id=\"g-tagsmap-dialog\">" + description + "<br/><br/>" + 
                       "Tag: <a href=\"" + tagURL + "\">" + tagName + "</a></div>";
    	  map.openInfoWindowHtml(point, myHtml);
        });
        return marker;
      }

      // Create markers for each tag with GPS coordinates.
      <? $counter = 0; ?>
      <? foreach ($tags_gps as $oneGPS): ?>
        <? $one_tag = ORM::factory("tag", $oneGPS->tag_id); ?>
        var myGeographicCoordinates<?=$counter; ?> = new GLatLng(<?= $oneGPS->latitude ?>, 
                                                  <?= $oneGPS->longitude ?>);
        map.addOverlay(createMarker(myGeographicCoordinates<?=$counter; ?>, 
                                      "<?= $oneGPS->description; ?>", 
                                      "<?= $one_tag->url(); ?>", 
                                      "<?= html::clean($one_tag->name); ?>"
                                    ));
        <? $counter++; ?>
      <? endforeach ?>
    }
  }

  google.setOnLoadCallback(initialize);
</script>

<? if ($map_fullsize == true) { ?>
  <div id="map_canvas" style="width: 100%; height: 100%"></div>
  </body></html>
<? } else { ?>
  <div id="map_canvas" style="width: 690px; height: 480px"></div> <br/>
  <center><a href="<?= url::site("tagsmap/googlemap/fullsize/1")?>">
           <?= t("View Fullsize")?>
  </a></center><br/><br/>
<? } ?>
