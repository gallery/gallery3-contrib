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
class gallery_remote_Core {
  const GR_PROT_MAJ = 2;
  const GR_PROT_MIN = 3;
  
  const GR_STAT_SUCCESS = 0;
  const PROTO_MAJ_VER_INVAL = 101;
  const PROTO_MIN_VER_INVAL= 102;
  const PROTO_VER_FMT_INVAL = 103;
  const PROTO_VER_MISSING = 104;
  const PASSWD_WRONG = 201;
  const LOGIN_MISSING = 202;
  const UNKNOWN_CMD = 301;
  const NO_ADD_PERMISSION = 401;
  const NO_FILENAME = 402;
  const UPLOAD_PHOTO_FAIL = 403;
  const NO_WRITE_PERMISSION = 404;
  const NO_VIEW_PERMISSION = 405;
  const NO_CREATE_ALBUM_PERMISSION = 501;
  const CREATE_ALBUM_FAILED = 502;
  const MOVE_ALBUM_FAILED = 503;
  const ROTATE_IMAGE_FAILED = 504;
}
