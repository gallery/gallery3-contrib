<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  $items_video = ORM::factory("items_video")
                 ->where("item_id", "=", $item->id)
                 ->find();
  if ($items_video->loaded() && file_exists($item->resize_path() . ".flv")) {
    print html::anchor(str_replace("?m=", ".flv?m=", $item->resize_url(true)), "", $attrs);
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
