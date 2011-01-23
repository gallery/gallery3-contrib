<?php defined("SYSPATH") or die("No direct script access.") ?>
<script type="text/javascript">
window.resizeTo(1000, 500);
</script>
<p align="center"><?php echo $_GET['ip']; ?></p>
<p align="center"><?php echo gethostbyaddr($_GET['ip']); ?></p>
<p align="center" class="copyText"><strong>[<a href="javascript:window.close();" class="txtLink">x</a>]</strong></p>

