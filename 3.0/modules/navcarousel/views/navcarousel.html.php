<?php defined("SYSPATH") or die("No direct script access.");
  $thumbsize = module::get_var("navcarousel", "thumbsize", "50");
	$parent = $item->parent();
  $item_counter = 0;
  $item_offset = 0;
  $maintain_aspect = module::get_var("navcarousel", "maintainaspect", false);
  $no_resize = module::get_var("navcarousel", "noresize", false);
  $no_ajax = module::get_var("navcarousel", "noajax", false);
?>
<div id="navcarousel-wrapper">
  <ul id="navcarousel" class="jcarousel-skin-tango">
<?php
if (!$no_ajax) {
?>
  </ul>
</div>
<script type="text/javascript">
function navcarousel_itemLoadCallback(carousel, state)
{
    // Check if the requested items already exist
    if (carousel.has(carousel.first, carousel.last)) {
        return;
    }

    jQuery.get(
        '<?= url::site("navcarousel/item/". $item->id) ?>',
        {
            first: carousel.first,
            last: carousel.last
        },
        function(xml) {
            navcarousel_itemAddCallback(carousel, carousel.first, carousel.last, xml);
        },
        'xml'
    );
};

function navcarousel_itemAddCallback(carousel, first, last, xml)
{
    // Set the size of the carousel
    carousel.size(parseInt(jQuery('total', xml).text()));

    jQuery('image', xml).each(function(i) {
        carousel.add(first + i, navcarousel_getItemHTML(jQuery(this).text()));
    });
};

function navcarousel_getItemHTML(url)
{
    var thisurl='<?= $item->thumb_url() ?>';
    var linkCollection = new Object;

<?php
}
$totalitems = ORM::factory("item")->where("parent_id", "=", $parent->id)->where("type", "=", "photo")->count_all();
foreach ($parent->viewable()->children() as $photo) {
  if ($photo->is_album()) {
    continue;
  }
  if ($photo->id == $item->id) {
    $navcar_size_addition = 10;
  } else {
    $navcar_size_addition = 0;
  }
  if ($no_resize) {
    $navcar_divsize = "style=\"width: ". ($thumbsize + $navcar_size_addition) ."px; height: ". ($thumbsize + $navcar_size_addition) ."px;\"";
    if ($photo->width > $photo->height) {
      $navcar_thumbsize = "height=\"". ($thumbsize + $navcar_size_addition) ."\"";
    } else {
      $navcar_thumbsize = "width=\"". ($thumbsize + $navcar_size_addition) ."\"";
    }
  } else {
    $navcar_divsize = "";
    if ($maintain_aspect) {
      $navcar_thumbsize = photo::img_dimensions($photo->width, $photo->height, $thumbsize + $navcar_size_addition);
    } else {
      $navcar_thumbsize = "width=\"". ($thumbsize + $navcar_size_addition) ."\" height=\"". ($thumbsize + $navcar_size_addition) ."\"";
    }
  }
  if ($no_ajax) {
    if (module::get_var("navcarousel", "nomouseover", false)) {
      $img_title = "";
    } else {
      $img_title =  " title=\"". html::purify($photo->title)->for_html_attr() ." (". $parent->get_position($photo) . t("%position of %total", array("position" => "", "total" => $totalitems)) .")\"";
    }
    if ($item->id == $photo->id) {
      echo "<li><div class=\"g-button ui-corner-all ui-icon-left ui-state-hover carousel-current\" ". $navcar_divsize ."><div style=\"width: 100%; height: 100%; overflow: hidden;\"><img src=\"". $photo->thumb_url() ."\" alt=\"". html::purify($photo->title)->for_html_attr() ."\"". $img_title ." ". $navcar_thumbsize ." /></div></div></li>\n";
    } else {
      echo "<li><div class=\"g-button ui-corner-all ui-icon-left ui-state-default carousel-thumbnail\" ". $navcar_divsize ."><div style=\"width: 100%; height: 100%; overflow: hidden;\"><a href=\"". $photo->abs_url() ."\"><img src=\"". $photo->thumb_url() ."\" alt=\"". html::purify($photo->title)->for_html_attr() ."\"". $img_title ." ". $navcar_thumbsize ." /></a></div></div></li>\n";
    }
  } else {
    echo ("linkCollection['". $photo->thumb_url() ."'] = ['". $photo->abs_url() ."', '". html::purify($photo->title)->for_html_attr() ."', '". $parent->get_position($photo) ."', '". $navcar_thumbsize ."', '". $navcar_divsize ."'];\n");
  }
}
if ($no_ajax) {
  echo "
        </ul>\n
    </div>\n";
} else {
  if (module::get_var("navcarousel", "nomouseover", false)) {
    $img_title = "";
  } else {
    $img_title =  " title=\"' + linkCollection[url][1] + ' (' + linkCollection[url][2] + '". t("%position of %total", array("position" => "", "total" => $totalitems)) .")\"";
  }
  ?>
       if (thisurl==url)
        {
        return '<div class="g-button ui-corner-all ui-icon-left ui-state-hover carousel-current" ' + linkCollection[url][4] + '><div style="width: 100%; height: 100%; overflow: hidden;"><img src="' + url + '" alt="' + linkCollection[url][1] + '"<?= $img_title ?> ' + linkCollection[url][3] + ' /></div></div>';
        }
      else
        {
        return '<div class="g-button ui-corner-all ui-icon-left ui-state-default carousel-thumbnail" ' + linkCollection[url][4] + '><div style="width: 100%; height: 100%; overflow: hidden;"><a href="' + linkCollection[url][0] + '"><img src="' + url + '" alt="' + linkCollection[url][1] + '"<?= $img_title ?> ' + linkCollection[url][3] + ' /></a></div></div>';
        }
  };

  </script>
<?php  
}
