<?php

function getJpegHeader($filename)
{
  $file = @fopen($filename, 'rb');
  if (!$file) {
    return FALSE;
  }
  $startOfImage = fread($file, 2);
  if ($startOfImage != "\xFF\xD8") {
    fclose($file);
    return FALSE;
  }
  $result = FALSE;
  
  do {
    $startOfSegment = fread($file, 1);
    if ($startOfSegment != "\xFF") {
      fclose($file);
      return $result;
    }
    $typeOfSegment = ord(fread($file, 1));
    if ($typeOfSegment === FALSE || $typeOfSegment == 0xDA || $typeOfSegment == 0xD9) {	// Start of image or End of image
      fclose($file);
      return $result;
    }
    if ($typeOfSegment < 0xD0 || $typeOfSegment > 0xD7) {
      $size = fread($file, 2);
      if ($size === FALSE) {
        fclose($file);
        return $result;
      }
      $sizeOfSegment = unpack("nV", $size);
      $data = fread($file, $sizeOfSegment['V']-2);
      if ($data === FALSE) {
        fclose($file);
        return $result;
      }
      if ($result === FALSE)
        unset($result);
      $result[] = array("type" => $typeOfSegment, "data" => $data);	// Multiple segments can have the same type like Exif and XMP
    }
  } while (!feof($file));
  fclose($file);
  return $result;
}


function getIptcBlock($jpegHeader)
{
  for ($i = 0; $i < count($jpegHeader); $i++) {
    if ($jpegHeader[$i]['type'] == 0xED)  {
      if (strncmp($jpegHeader[$i]['data'], "Photoshop 3.0\x00", 14) == 0) {
        return $jpegHeader[$i]['data'];
      }
    }
  }
  return FALSE;
}


function getXmpDom($jpegHeader)
{
  for ($i = 0; $i < count($jpegHeader); $i++) {
    if ($jpegHeader[$i]['type'] == 0xE1)  {
      if (strncmp($jpegHeader[$i]['data'], "http://ns.adobe.com/xap/1.0/\x00", 29) == 0) {
        $xmlstr = substr($jpegHeader[$i]['data'], 29);
        $doc = new DOMDocument();
		$doc->loadXML($xmlstr);
        return $doc;
      }
    }
  }
  return FALSE;
}


function getXmpValue($dom, $xpathQuery)
{
  if ($dom === FALSE)
    return null;
  $xpath = new DOMXPath($dom);
  $xpath->registerNamespace('rdf', "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
  $xpath->registerNamespace('photoshop', "http://ns.adobe.com/photoshop/1.0/");
  $xpath->registerNamespace('Iptc4xmpCore', "http://iptc.org/std/Iptc4xmpCore/1.0/xmlns/");
  $xpath->registerNamespace('dc', "http://purl.org/dc/elements/1.1/");
  $xpath->registerNamespace('mediapro', "http://ns.iview-multimedia.com/mediapro/1.0/");
  $nodeList = $xpath->query($xpathQuery);
  $result = "";
  foreach ($nodeList as $node) {
    if (!empty($result))
      $result .= ';';
    $result .= $node->nodeValue;
  }
  return $result;
}

