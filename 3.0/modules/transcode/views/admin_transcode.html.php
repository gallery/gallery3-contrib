<?php defined("SYSPATH") or die("No direct script access.") ?>	

<div id="g-admin-code-block">
	<h2><?= t("Transcoding Settings") ?></h2>

	<p><?= t("Setup the preferred video format to transcode all uploaded videos to below. Select one or multiple resolutions/formats to use."); ?></p>

	<div class="g-block-content">
		<?php echo $form; ?>
	</div>
</div>

<script type="text/javascript">
    function verifyffmpeg() {
        $('#ffmpeg_path').parent('li').removeClass('g-error');
        $('p.g-error').remove();

        $.getJSON("<?php echo url::site("admin/transcode/verify"); ?>",
               { ffmpeg_path: $("#ffmpeg_path").val() },
               verifyffmpegcb
              );
    }
    function verifyffmpegcb(data) {
        if (data.success) {
            $("#audio_codec").find("option").remove();
            var i = 0;
            $.each(data.codecs, function(key, val) {
                $("#audio_codec").append(new Option(key, val, (i > 0 ? false : true)));
                i++;
            });
        }
        else {
            var li = $('#ffmpeg_path').parent('li');
            li.addClass('g-error');
            li.append('<p class="g-message g-error"> ' + data.error + ' </p>');
        }
    }
</script>
