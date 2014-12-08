<?php defined("SYSPATH") or die("No direct script access.") ?>

<?  if (module::get_var("pages_xtra", "show_sidebar")) : ?>
 <style type="text/css">
<?  if (module::get_var("gallery", "active_site_theme") == "greydragon") : ?>
    #g-column-right {
      display: none;
    }
    .g-page-block-content {
      width: 99%;
    }
    <? else: ?>
   #g-sidebar {
      display: none;
    }
    #g-content {
      width: 950px;
    }
    <? endif ?>
  </style>
 <? endif ?>

<div class="g-page-block">

<? // Disable next line so that H1 is NOT auto-generated and auto-linked with Title. Manually write <h1> tags into body code, for static pages.
?>   
<?/* <h1><?=  t($title) ?></h1>  */?>

          
<div class="g-page-block-content">
<br />
<!-- addthis Toolbox begin -->
<div id="g-view-menu"> 
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>	
</div>
<!-- addthis Toolbox end -->

    <?=t($body) ?>    
  </div>
</div>
