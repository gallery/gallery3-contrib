<?php

// Syntax Sample:
// --------------
// [img]http://elouai.com/images/star.gif[/img]
// [url="http://elouai.com"]eLouai[/url]
// [size="25"]HUGE[/size]
// [color="red"]RED[/color]
// [b]bold[/b]
// [i]italic[/i]
// [u]underline[/u]
// [list][*]item[*]item[*]item[/list]
// [code]value="123";[/code]
// [quote]John said yadda yadda yadda[/quote]

function bb2html($text, $mixmode)	{

  static $bbcode_mappings = array(
    "#\\[b\\](.*?)\\[/b\\]#" => "<strong>$1</strong>",
    "#\\[i\\](.*?)\\[/i\\]#" => "<em>$1</em>",
    "#\\[u\\](.*?)\\[/u\\]#" => "<u>$1</u>",
    "#\\[s\\](.*?)\\[/s\\]#" => "<strike>$1</strike>",
    "#\\[o\\](.*?)\\[/o\\]#" => "<overline>$1</overline>",
    "#\\[url\\](.*?)\[/url\\]#" => "<a href=\"$1\">$1</a>",
    "#\\[url=(.*?)\\](.*?)\[/url\\]#" => "<a href=\"$1\" target=\"_blank\">$2</a>",
    "#\\[mail=(.*?)\\](.*?)\[/mail\\]#" => "<a href=\"mailto:$1\" target=\"_blank\">$2</a>",
    "#\\[img\\](.*?)\\[/img\\]#" => "<img src=\"$1\" alt=\"\" />",
    "#\\[img=(.*?)\\](.*?)\[/img\\]#" => "<img src=\"$1\" alt=\"$2\" />",
    "#\\[quote\\](.*?)\\[/quote\\]#" => "<blockquote><p>$1</p></blockquote>",
    "#\\[code\\](.*?)\\[/code\\]#" => "<pre>$1</pre>",
    "#\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]#" => "<span style=\"font-size: $1;\">$2</span>",
    "#\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]#" => "<span style=\"color: $1;\">$2</span>",
    "#\\[class=([^\\[]*)\\]([^\\[]*)\\[/class\\]#" => "<span class=\"$1\">$2</span>",
    "#\\[center\\](.*?)\\[/center\\]#" => "<div style=\"text-align: center;\">$1</div>",
    "#\\[list\\](.*?)\\[/list\\]#" => "<ul>$1</ul>",
    "#\\[ul\\](.*?)\\[/ul\\]#" => "<ul>$1</ul>",
    "#\\[li\\](.*?)\\[/li\\]#" => "<li>$1</li>",
  );

  // Replace any html brackets with HTML Entities to prevent executing HTML or script 
  // Don't use strip_tags here because it breaks [url] search by replacing & with amp
  if ($mixmode == 1)
  {
    $newtext = str_replace("&lt;", "<", $text); 
    $newtext = str_replace("&gt;", ">", $newtext); 
    $newtext = str_replace("&quot;", "\"", $newtext); 
  } else {
    $newtext = str_replace("<", "&lt;", $text); 
    $newtext = str_replace(">", "&gt;", $newtext); 
    $newtext = str_replace("&amp;quot;", "&quot;", $newtext); 
  }

  // Convert new line chars to html <br /> tags 
  $newtext = nl2br($newtext);  

  if (strpos($text, "[") !== false) {
    $newtext = preg_replace(array_keys($bbcode_mappings), array_values($bbcode_mappings), $newtext);
  }

  return stripslashes($newtext);  //stops slashing, useful when pulling from db
}

?>