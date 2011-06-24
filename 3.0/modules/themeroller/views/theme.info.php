<?php defined("SYSPATH") or die("No direct script access.") ?>
name = "<?= $display_name ?>"
description = "<?= $description ?>"
version = 1
author = "<?= $user_name ?>"
site = "<?= !$is_admin ? 1 : 0?>"
admin = "<?= $is_admin ? 1 : 0?>"
; definition = <?= $definition ?>

