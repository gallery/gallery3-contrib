<?php 
defined("SYSPATH") or die("No direct script access.");
if(module::get_var("adsense","location") == "sidebar") {
	$code = module::get_var("adsense", "code");
	if (!$code) {
	  return;
	}
	$google_code = '
	<script type="text/javascript">' . $code . '</script>
	<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>';
	
	echo $google_code;
}
?>

