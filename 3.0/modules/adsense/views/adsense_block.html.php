<?php 
defined("SYSPATH") or die("No direct script access.");
if(module::get_var("adsense","location") == "sidebar") {
	$code = module::get_var("adsense", "code");
	if (!$code) {
	  return;
	}
	$proto = request::protocol();
	$google_code = '
	<script type="text/javascript">' . $code . '</script>
	<script type="text/javascript"
		src="' . $proto . '://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>';
	
	echo $google_code;
}
?>

