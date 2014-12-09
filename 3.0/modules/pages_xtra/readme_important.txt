ADDITIONAL INSTRUCTIONS FOR PAGES_XTRA MODULE :-

To enable the MetaDescriptions and MetaTags you provide in Pages_xtra to be visible to Search Engines, Admins MUST manually add a line of code to the page.html.php file of their theme, as follows -

Step 1: Locate the page.html.php file in the Views folder of your Theme.
Step 2: Open your page.html.php file and find the <head></head> section
Step 3: Copy and paste the line below between the head tags. After the opening tag or before the closing tag is ok. 

          <? include ("/home/ISP_username/public_html/gallery3/modules/pages_xtra/views/pages_meta_block.html.php"); ?>

Step 4: Replace 'home/ISP_username' with the base URL of your server or CPanel, and replace 'gallery3' with your Gallery installation
        name if different. 
Step 5: Save your page.html.php file and check that your pages_xtra source code shows correct data for metadescription and metatags 


 ....Graficat   
