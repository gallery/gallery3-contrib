<?php defined("SYSPATH") or die("No direct script access.") ?>
name = <?= $display_name->for_js() . "\n" ?>
description = <?= $description->for_js() . "\n" ?>
version = 1
author = <?= $user_name->for_js() . "\n" ?>
site = <?= (!$is_admin ? 1 : 0) . "\n"?>
admin = <?= ($is_admin ? 1 : 0) . "\n"?>
author_url = <?= $user_name->for_js() . "\n" ?>
info_url = <?= $info_url->for_js() . "\n" ?>
discuss_url = <?= $discuss_url->for_js() . "\n" ?>
; definition = <?= $definition ?>

