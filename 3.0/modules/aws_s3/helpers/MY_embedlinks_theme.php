<?php

class embedlinks_theme extends embedlinks_theme_Core {

    static function photo_bottom($theme) {
        if (module::get_var("embedlinks", "InPageLinks")) {
            $item = $theme->item;
            if ($item->view_1 == 1)
                return parent::photo_bottom($theme);
        }
    }

}