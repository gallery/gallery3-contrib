<?php defined("SYSPATH") or die("No direct script access.");

class picasa_faces_event_Core
{
    static function module_change($changes)
    {
        // See if the Photo Annotation module is installed,
        // tell the user to install it if it isn't.
        if (!module::is_active("photoannotation") || in_array("photoannotation", $changes->deactivate))
        {
            site_status::warning(
                t("The Picasa Faces module requires the Photo Annotation module.  " .
                "<a href=\"%url\">Activate the Photo Annotation module now</a>",
                array("url" => url::site("admin/modules"))),
                "picasa_faces_needs_photoannotation");
        }
        else
        {
            site_status::clear("picasa_faces_needs_photoannotation");
        }
    }
}
