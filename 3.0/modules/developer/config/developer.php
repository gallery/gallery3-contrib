<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Defines the available callback methods
 */
$config["methods"] = array(
  "theme" => array("album_blocks" => t("Album block"),
                   "album_bottom" => t("Bottom of album content"),
                   "album_top" => t("Top of Album content"),
                   "admin_credits" => t("Administration page credits"),
                   "admin_footer" => t("Adminsitration page footer"),
                   "admin_header_top" => t("Top of administration page header"),
                   "admin_header_bottom" => t("Bottom of administration page header"),
                   "admin_page_bottom" => t("Bottom of administration page"),
                   "admin_page_top" => t("Top of administration page"),
                   "admin_head" => t("Adminstration page head"),
                   "body_attributes" => t("Body Attributes"),
                   "credits" => t("Album or photo page credits"),
                   "dynamic_bottom" => t("Bottom of dynamic page content"),
                   "dynamic_top" => t("Top of dynamic page content"),
                   "footer" => t("Album or photo page footer"),
                   "head" => t("Album or photo page head"),
                   "header_bottom" => t("Album or photo header bottom"),
                   "header_top" => t("Album or photo header top"),
                   "page_bottom" => t("Album or photo bottom"),
                   "page_top" => t("Album or photo top"),
                   "photo_blocks" => t("Photo block"),
                   "photo_bottom" => t("Bottom of photo content"),
                   "photo_top" => t("Top of photo content"),
                   "resize_bottom" => t("Bottom of the resize view"),
                   "resize_top" => t("Top of the resize view"),
                   "sidebar_bottom" => t("Bottom of sidebar"),
                   "sidebar_top" => t("Top of sidebar"),
                   "thumb_bottom" => t("Bottom of thumbnail"),
                   "thumb_info" => t("Thumbnail information"),
                   "thumb_top" => t("Top of thumbnail display")));
