<?php defined("SYSPATH") or die("No direct script access.");

class picasa_faces_installer
{
    static function install()
    {
        // Create a table to store face mappings in.
        $db = Database::instance();

        $db->query(
            "CREATE TABLE IF NOT EXISTS `picasa_faces` (
               `id` int(9) NOT NULL auto_increment,
               `face_id` varchar(16) NOT NULL,
               `tag_id` int(9) NOT NULL,
               `user_id` int(9) NOT NULL,
               PRIMARY KEY  (`id`),
               KEY `face_id` (`face_id`,`id`)
             ) DEFAULT CHARSET=utf8;"
         );

        // Set the module version number.
        module::set_version("picasa_faces", 2);
    }

    static function upgrade($version)
    {
        if ($version == 1)
        {
            Database::instance()->query(
                "ALTER TABLE `picasa_faces` ADD `user_id` int(9) NOT NULL"
            );

            module::set_version("picasa_faces", 2);
        }
    }

    static function deactivate()
    {
        // Clear the require photo annototaion message when picasa faces is deactivated.
        site_status::clear("picasa_faces_needs_photoannotation");
    }

    static function uninstall()
    {
        // Delete the face mapping table before uninstalling.
        $db = Database::instance();
        $db->query("DROP TABLE IF EXISTS {picasa_faces};");
        module::delete("picasa_faces");
    }
}
