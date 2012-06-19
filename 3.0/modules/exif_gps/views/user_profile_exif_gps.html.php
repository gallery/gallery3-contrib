<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
<?
  if (module::get_var("exif_gps", "googlemap_api_key", "") != "") {
    print "google.load(\"maps\", \"3\",  {other_params:\"key=" . module::get_var("exif_gps", "googlemap_api_key") . "&sensor=false\"});";
  } else {
    print "google.load(\"maps\", \"3\",  {other_params:\"sensor=false\"});";
  }
?>

  var google_zoom_hack = false;
  var map = null;
  var infoWindow = null;

  $.ajaxSetup({
    error: function(xhr, status, error) {
      var status_text = document.getElementById('exif-gps-status');
      status_text.innerHTML = "<font size=\"6\" color=\"white\"><strong>An AJAX error occured: " + status + "<br />\nError: " + error + "</strong></font>";
    }
  });

  function initialize() {
    var myLatlng = new google.maps.LatLng(0, 0);
    var myOptions = {
      zoom: 1,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.<?=$map_type; ?>
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var glatlngbounds = new google.maps.LatLngBounds( ); // This is so we can auto center the map.
    infoWindow = new google.maps.InfoWindow();

    var map_markers = [];
    var str_marker_html = "";
    var current_latlng = null;

    jQuery.get("<?=url::abs_site("exif_gps/xml/user/{$user_id}"); ?>", {}, function(data) {
      jQuery(data).find("marker").each(function() {
        var xmlmarker = jQuery(this);
        var latlng = new google.maps.LatLng(parseFloat(xmlmarker.attr("lat")),
                                    parseFloat(xmlmarker.attr("lng")));

        if (!latlng.equals(current_latlng)) {
          if (current_latlng != null) {
            var fn = markerClickFunction(str_marker_html, current_latlng);
            google.maps.event.addListener(map_markers[map_markers.length-1], 'click', fn);
          }

          current_latlng = latlng;
          str_marker_html = "";
          glatlngbounds.extend(latlng);
          var marker = new google.maps.Marker({position: latlng});
          map_markers.push(marker);
        }

        var str_thumb_html = String(xmlmarker.attr("thumb"));
        str_thumb_html = str_thumb_html.replace("&lt;", "<");
        str_thumb_html = str_thumb_html.replace("&gt;", ">");
        str_thumb_html = str_thumb_html.replace("&apos;", "\'");
        str_thumb_html = str_thumb_html.replace("&quot;", "\"");
        str_thumb_html = str_thumb_html.replace("&amp;", "&");

        str_thumb_html = "<div class=\"g-exif-gps-thumb\"><a href=\"" + 
                         String(xmlmarker.attr("url")) + "\">" + str_thumb_html + "</a></div>";
        str_marker_html += str_thumb_html;

      });

      var fn = markerClickFunction(str_marker_html, current_latlng);
      google.maps.event.addListener(map_markers[map_markers.length-1], 'click', fn);

      var mcOptions = { gridSize: <?= module::get_var("exif_gps", "markercluster_gridsize"); ?>, maxZoom: <?= module::get_var("exif_gps", "markercluster_maxzoom"); ?>};
      var markerCluster = new MarkerClusterer(map, map_markers, mcOptions);
      google_zoom_hack = true;
      map.fitBounds(glatlngbounds);
      document.getElementById('over_map').style.display = 'none';
    });

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
  }

  markerClickFunction = function(str_thumb_html, latlng) {
    return function(e) {
      e.cancelBubble = true;
      e.returnValue = false;
      if (e.stopPropagation) {
        e.stopPropagation();
        e.preventDefault();
      }

      infoWindow.setContent(str_thumb_html);
      infoWindow.setPosition(latlng);
      infoWindow.open(map);
    };
  };

  google.setOnLoadCallback(initialize);
</script>

<style>
   #wrapper { position: relative; }
   #over_map { position: absolute; top: 0px; left: 0px; z-index: 99; }
</style>

<div id="wrapper">
  <div id="map_canvas" style="width:690px; height:480px;"></div>
  <div id="over_map" style="width:690px; height:480px;">
    <p id="exif-gps-status" style="text-align: center; display: table-cell; vertical-align: middle; width: 690px; height: 480px;">
      <font size="6" color="white"><strong><?= t("Loading..."); ?></strong></font><br /><br />
      <img src="<?= url::file("modules/exif_gps/images/exif_gps-loading-map-large.gif"); ?>" style="vertical-align: middle;"></img>
    </p>
  </div>
</div>
