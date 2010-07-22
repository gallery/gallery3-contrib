
<? $base = url::base(false, "http");
   $url = $base."modules/gwtorganise/war/index.php?url=".urlencode($base);
 ?>
<iframe src="<?=$url?>" style="width:100%; height:500px; border:8px solid #d2e1f6; margin:0; padding:0;">
</iframe>
GWT Organise version built on <?= revision::getTimeStamp()?>