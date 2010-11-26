<?php

class embedlinks_block extends embedlinks_block_Core {

    static function get($block_id, $theme) {
        if ($theme->item && $theme->item->view_1 == 1)
            parent::get($block_id, $theme);
    }

}