<?php defined("SYSPATH") or die("No direct script access.") ?>

<?php if ($item->is_movie()): ?>
    <?php if (count($resolutions) > 0): ?>
        <select id="resolution-select" onchange="changeVideo(this.value);">
            <?php foreach ($resolutions as $resolution): ?>
                <?php $r = explode("x", $resolution->resolution); ?>
                <option value="<?php echo $resolution->resolution; ?>"><?php echo $r[0]; ?> x <?php echo $r[1]; ?></option>
            <?php endforeach; ?>
        </select>
        <script type="text/javascript">
            var fpItmId = "g-item-id-<?php echo $item->id; ?>";
            var fpBaseUrl = "<?php echo url::abs_file("var/modules/transcode/flv/" . $item->id); ?>/";
            $f(fpItmId).onLoad(function() {
		changeVideo($('#resolution-select').val());
                //var url = fpBaseUrl + $('#resolution-select').val() + ".flv";
                //$('#' + fpItmId).flowplayer(0).play(url);
            });
            function changeVideo(res) {
                var id = "g-item-id-<?php echo $item->id; ?>";
                var dim = res.split('x');
		$('#' + fpItmId).css({width: dim[0] + 'px', height: dim[1] + 'px'});
                $('#' + id).flowplayer(0).play(fpBaseUrl + res + ".flv");
            }
        </script>
    <?php else: ?>
        <p>No alternative resolutions available.</p>
    <?php endif; ?>
<?php endif; ?>
