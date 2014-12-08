<?php defined("SYSPATH") or die("No direct script access.") ?>

<?
  // Strip HTML
  $page_description = strip_tags($page_description);
  // Strip Line Breaks
  $page_description = str_replace("\n", " ", $page_description);
  // Limit Description to 150 characters.
  $page_description = substr($page_description, 0,150);

?> 
<?
//  Apply view to static pages only. 
        if (($theme->page_type != "collection") && ($theme->page_type != "item"))
// or,  if ((!$theme->item()) && (!$theme->tag())) 
   {
?>
<meta name="keywords" content="<?= $page_tags ?>" />    
<meta name="description" content="<?= $page_description ?>" />
<?  
   }  
?>

