<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
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
class order_log {
  const ORDERED = 1;
  const PAID = 2;
  const LATE_PAYMENT = 3;
  const COPY_SENT = 9;
  const DELIVERED_NOTPAID = 10;
  const DELIVERED = 20;
  const EXPIRED = 30;
	const CANCELLED = 99;
	
  /**
   * Add a log entry.
   *
   * @param string  $category  an arbitrary category we can use to filter log messages
   * @param string  $message   a detailed log message
   * @param integer $severity  INFO, WARNING or ERROR
   * @param string  $html      an html snippet presented alongside the log message to aid the admin
   */
  static function log($order, $event) {
    $log = ORM::factory("order_log");
    $log->id = $order->id;
    $log->status = $order->status;
    $log->event = $event;
    $log->timestamp = time();
    $log->save();
  }
}
