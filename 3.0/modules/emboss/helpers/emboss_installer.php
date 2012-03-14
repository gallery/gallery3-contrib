<?php defined('SYSPATH') or die('No direct script access.');
/*************************************************************************
 * Copyright (C) 2012  Michel A. Mayer                                   *
 *                                                                       *
 * This program is free software: you can redistribute it and/or modify  *
 * it under the terms of the GNU General Public License as published by  *
 * the Free Software Foundation, either version 3 of the License, or     *
 * (at your option) any later version.                                   *
 *                                                                       *
 * This program is distributed in the hope that it will be useful,       *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 * GNU General Public License for more details.                          *
 *                                                                       *
 * You should have received a copy of the GNU General Public License     *
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. *
 *************************************************************************/
class emboss_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE IF NOT EXISTS {emboss_overlays} (
                 `id` int(9) NOT NULL auto_increment,
                 `active` tinyint(4) NOT NULL DEFAULT 1,
                 `name` varchar(64) NOT NULL,
                 `width` int(9) NOT NULL,
                 `height` int(9) NOT NULL,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`name`))");

    $db->query("CREATE TABLE IF NOT EXISTS {emboss_mappings} (
                 `id` int(9) NOT NULL auto_increment,
                 `image_id` int(9) NOT NULL,
                 `best_overlay_id` int(9) NOT NULL,
                 `cur_overlay_id` int(9),
                 `cur_gravity` varchar(16),
                 `cur_transparency` tinyint(4),
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`image_id`))");

    @mkdir(VARPATH . 'originals');
    @mkdir(VARPATH . 'modules');
    @mkdir(VARPATH . 'modules/emboss');
    module::set_version('emboss',1);
    log::success('emboss','Emboss Installed');
  }

  static function upgrade($version)
   {
     module::set_version('emboss',$verion=1);
     log::info('emboss',"Upgrade to version $version / No action taken");
   }

  static function activate()
  {
    log::info('emboss','Emboss Activated');
    emboss::reconcile();
  }

  static function deactivate()
  {
    log::info('emboss','Emboss Deactivated');
  }

  static function uninstall() {
    emboss::uninstall();
  }
}
