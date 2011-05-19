<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
// rWatcher Edit:  This is a combination of Gallery's movieplayer.html.php file and 
//  some custom edits.

  $items_video = ORM::factory("items_video")
                 ->where("item_id", "=", $item->id)
                 ->find();
  if ($items_video->loaded() && file_exists($item->resize_path() . ".flv")) {
    print html::anchor(str_replace("?m=", ".flv?m=", $item->resize_url(true)), "", $attrs);
  } else if ($items_video->loaded() && !(file_exists($item->resize_path() . ".flv"))) {
    print "<a href=\"" . $item->file_url(true) . "\" class=\"g-movie\" id=\"g-videos-full-url\"></a>";
  } else {
    print html::anchor($item->file_url(true), "", $attrs);
  }
?>

<script type="text/javascript">
  flowplayer(
    "<?= $attrs["id"] ?>",
    {
      src: "<?= url::abs_file("lib/flowplayer.swf") ?>",
      wmode: "transparent",
      provider: "pseudostreaming"
    },
    {
      clip: {
        scaling: 'fit'
      },
      plugins: {
        pseudostreaming: {
          url: "<?= url::abs_file("lib/flowplayer.pseudostreaming.swf") ?>"
        },
        controls: {
          autoHide: 'always',
          hideDelay: 2000
        }
      }
    }
  )
</script>
