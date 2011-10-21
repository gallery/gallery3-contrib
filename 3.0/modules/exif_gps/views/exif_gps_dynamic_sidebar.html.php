<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= module::get_var("exif_gps", "googlemap_api_key"); ?>"></script>
<script type="text/javascript">
  google.load("maps", "3",{"other_params":"sensor=false"});
  function initialize() {
    var latlng = new google.maps.LatLng(<?=$latitude; ?>,<?=$longitude; ?>);
    var myOptions = {
      zoom: <?= module::get_var("exif_gps", "sidebar_zoom"); ?>,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.<?=$sidebar_map_type ?>
    };
    var map = new google.maps.Map(document.getElementById("sidebar_map_canvas"), myOptions);
    var marker = new google.maps.Marker({
      position: latlng, 
      map: map
    });
  }

  google.setOnLoadCallback(initialize);

</script>
<div id="sidebar_map_canvas" style="width:205px; height:214px"><img src="http://maps.google.com/maps/api/staticmap?center=<?=$latitude; ?>,<?=$longitude; ?>&zoom=<?= module::get_var("exif_gps", "sidebar_zoom"); ?>&size=205x214&maptype=<?=$sidebar_map_type ?>&markers=color:red|color:red|<?=$latitude; ?>,<?=$longitude; ?>&sensor=false"></div>
