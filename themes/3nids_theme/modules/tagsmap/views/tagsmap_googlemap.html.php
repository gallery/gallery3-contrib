<?php defined("SYSPATH") or die("No direct script access.") ?>

<? if ($map_fullsize == true) { ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
  <head> 
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
    <title><?= t("Gallery :: Map") ?></title> 
  </head>
  <body>
<? } ?>

<script src="http://www.google.com/jsapi?key=<?=$google_map_key ?>" type="text/javascript"></script>
<script type="text/javascript"> 
google.load("maps", "2");
</script>
<script src="/modules/tagsmap/js/extinfowindow.js" type="text/javascript"></script>
<script type="text/javascript"> 


var map;
  function Gload() {
    if (GBrowserIsCompatible()) {
      map = new GMap2(document.getElementById("g-map-canva"));

      // Make Google Earth an Option.
      map.addMapType(G_SATELLITE_3D_MAP);
      var mapControl = new GMapTypeControl();
      map.addControl(mapControl);

      // Set map defaults.
      map.setCenter(new GLatLng(<?=$google_map_latitude ?>, 
                                <?=$google_map_longitude ?>));
      map.setZoom(<?=$google_map_zoom ?>);
      map.setUIToDefault();      
      map.setMapType(<?=$google_map_type ?>);

	tooltip = document.createElement("div");
	map.getPane(G_MAP_FLOAT_PANE).appendChild(tooltip);
	tooltip.style.visibility="hidden";
	  
	  
     // Function for making the clickable markers.
	var BaseIcon = new GIcon();
		BaseIcon.shadow = '<?= url::file("modules/tagsmap/images/marker_shadow.png") ?>';
		BaseIcon.iconSize = new GSize(12,20);
		BaseIcon.shadowSize = new GSize(12+15,20);
		BaseIcon.iconAnchor = new GPoint(6, 20);
		BaseIcon.infoWindowAnchor = new GPoint(5, 1);
		BaseIcon.image = '<?= url::file("modules/tagsmap/images/markery.png") ?>';
		BaseIcon.iconSize = new GSize(12,20);
		BaseIcon.shadowSize = new GSize(12+15,20);
		BaseIcon.iconAnchor = new GPoint(12/2, 20);

	var icon = new GIcon();
		icon.image = '<?= url::file("modules/tagsmap/images/landscape16.png") ?>';
		icon.iconSize = new GSize(20, 20);
		icon.iconAnchor = new GPoint(10, 10);
		icon.infoWindowAnchor = new GPoint(5, 1);
 
      
      function createMarker(point, html_thumb, tagURL, tagName) {
        var marker = new GMarker(point,BaseIcon);
	marker.tooltip = '<div class="tooltip">'+tagName+'<\/div>';

    	GEvent.addListener(marker, "click", function() {
		  //var myHtml = "<a href=\"" + tagURL + "\">" + tagName + "</a><br>" + html_thumb;
		     marker.openExtInfoWindow(
		      map,
		      "gmInfo",
		      html_thumb,
		      {beakOffset: 3}
		    ); 
		});
	 GEvent.addListener(marker,"mouseover", function() {
		showTooltip(marker);
	});
	GEvent.addListener(marker,"mouseout", function() {
		tooltip.style.visibility="hidden"
	});
	GEvent.addListener(marker, "click", function() {
		tooltip.style.visibility="hidden"
	});
	return marker;
      }

      // Create markers for each tag with GPS coordinates.
      <? $counter = 0; ?>
      <? foreach ($tags_gps as $oneGPS): ?>
        var myGeographicCoordinates<?=$counter; ?> = new GLatLng(<?= $oneGPS->latitude ?>, 
                                                  <?= $oneGPS->longitude ?>);
	
	<? //$tagitems = $oneGPS->tagitems(); ?>
	<? $html_thumb = "<div id=\"g-map-div-$counter\" class=\"g-map-thumb-link\"></div><div class=\"g-map-thumb-img\"><table class=\"g-map-thumb-table\"><tr>"; 
	$tagitems = tagsmap::tagitems($oneGPS);
	foreach ($tagitems as $tagchild){	
	      $html_thumb .= "<td class=\"g-map-thumb-td\"><a href=\"" . $tagchild->url() . "\" onMouseOver=\"ThumbLink('g-map-div-$counter','" . html::purify($tagchild->title) . "')\" onMouseOut=\"ThumbLink('g-map-div-$counter','')\"><img src=\"" . $tagchild->thumb_url() . "\" class=\"gMapThumbnail\"></a></td>";
		}
	$html_thumb .= "</tr></table></div>";
	?>
	
        map.addOverlay(createMarker(myGeographicCoordinates<?=$counter; ?>, 
                                      "<?= addcslashes($html_thumb,'"') ?>", 
                                      "<?= url::site("tags/$oneGPS->tag_id")?>", 
                                      "<?= str_replace("map.","",ORM::factory("tag", $oneGPS->tag_id)->name) ?>"
                                    ));
        <? $counter++; ?>
      <? endforeach ?>
    }
  }

  google.setOnLoadCallback(Gload);


  function ThumbLink(divid,html){
	document.getElementById(divid).innerHTML = html;
  }
  
  
  function showTooltip(marker) {
	tooltip.innerHTML = marker.tooltip;
	var point=map.getCurrentMapType().getProjection().fromLatLngToPixel(map.fromDivPixelToLatLng(new GLatLng(0,0),true),map.getZoom());
	var offset=map.getCurrentMapType().getProjection().fromLatLngToPixel(marker.getPoint(),map.getZoom());
	var anchor=marker.getIcon().iconAnchor;
	var width=marker.getIcon().iconSize.width;
	var height=tooltip.clientHeight;
	var pos = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(offset.x - point.x - anchor.x + width, offset.y - point.y -anchor.y -height));
	pos.apply(tooltip);
	tooltip.style.visibility="visible";
}



</script>

<? if ($map_fullsize == true) { ?>
  <div id="g-map-canva" style="width: 100%; height: 100%"></div>
  </body></html>
<? } else { ?>
  <div id="g-map-canva" style="width: 660px; height: 550px"></div> <br/>
  <!--<a href="<?= url::site("tagsmap/googlemap/fullsize/1")?>">
           <?= t("View Fullsize")?>
  </a><br/><br/>!-->
<? } ?>
