<?
$src = array (
		"rounded_black.png",   
		"rounded_dkgrey.png", 
		"rounded_ltgrey.png",
		"rounded_white.png"
		);   
$under = 0;    // combine images underneath or not?
// -- end of config

$imgBuf = array ();
$maxW=0; $maxH=0;
foreach ($src as $link)
{
	switch(substr ($link,strrpos ($link,".")+1))
	{
		case 'png':
			$iTmp = imagecreatefrompng($link);
			break;
		case 'gif':
			$iTmp = imagecreatefromgif($link);
			break;               
		case 'jpeg':           
		case 'jpg':
			$iTmp = imagecreatefromjpeg($link);
			break;               
	}

	if ($under)
	{
		$maxW=(imagesx($iTmp)>$maxW)?imagesx($iTmp):$maxW;
		$maxH+=imagesy($iTmp);
	}
	else
	{
		$maxW+=imagesx($iTmp);
		$maxH=(imagesy($iTmp)>$maxH)?imagesy($iTmp):$maxH;
	}

	array_push ($imgBuf,$iTmp);
}

$iOut = imagecreatetruecolor ($maxW,$maxH) ;
$iOut = imagecreate ($maxW,$maxH) ;

$pos=0;
foreach ($imgBuf as $img)
{
	if ($under)
		imagecopy ($iOut,$img,0,$pos,0,0,imagesx($img),imagesy($img));
	else
		imagecopy ($iOut,$img,$pos,0,0,0,imagesx($img),imagesy($img));   
	$pos+= $under ? imagesy($img) : imagesx($img);
	imagedestroy ($img);
}
//header("Content-type: image/jpg");

imagealphablending($iOut, true);
imagesavealpha($iOut, true);

imagepng($iOut);

?>
