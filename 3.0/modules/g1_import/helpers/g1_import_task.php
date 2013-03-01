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
class g1_import_task_Core {
  static function available_tasks() {
    $version = '';
    if (g1_import::is_configured()) {
      g1_import::init();
      // Guard from common case where the import has been
      // completed and the original files have been removed.
      if (is_dir(g1_import::$album_dir)) {
        $version = g1_import::version();
      }
    }

    if (g1_import::is_initialized()) {
      return array(Task_Definition::factory()
                   ->callback('g1_import_task::import')
                   ->name(t('Import from Gallery 1'))
                   ->description(
                     t('Gallery %version detected', array('version' => $version)))
                   ->severity(log::SUCCESS));
    }

    return array();
  }

  static function import($task) {
    $start = microtime(true);
    g1_import::init();

    $stats = $task->get('stats');
    $done = $task->get('done');
    $total = $task->get('total');
    $completed = $task->get('completed');
    $mode = $task->get('mode');
    $queue = $task->get('queue');
    if (!isset($mode)) {
      $stats = g1_import::g1_stats();
      $stats['items'] = $stats['photos'] + $stats['movies'];
      unset($stats['photos']);
      unset($stats['movies']);
      $stats['fix'] = 1;
      $task->set('stats', $stats);

      $task->set('total', $total = array_sum(array_values($stats)));
      $completed = 0;
      $mode = 0;

      $done = array();
      foreach (array_keys($stats) as $key) {
        $done[$key] = 0;
      }
      $task->set('done', $done);
    }

    $modes = array('users', 'albums', 'items', 'comments', 'highlights', 'fix', 'done');
    while (!$task->done && microtime(true) - $start < 1.5) {
      if ($done[$modes[$mode]] >= $stats[$modes[$mode]]) {
        // Nothing left to do for this mode.  Advance.
        $mode++;
        $task->set('last_id', 0);
        $queue = array();

        // Start the loop from the beginning again.  This way if we get to a mode that requires no
        // actions (eg, if the G1 comments module isn't installed) we won't try to do any comments
        // queries.. in the next iteration we'll just skip over that mode.
        if ($modes[$mode] != 'done') {
          continue;
        }
      }

      switch($modes[$mode]) {

      case 'users':
        $done['users'] = $stats['users']-1;
        $task->status = t(
          'Ignoring users (%count of %total)',
          array('count' => $done['users'], 'total' => $stats['users']));
        break;

      case 'albums':
        if (empty($queue)) {
          if(count(g1_import::$tree)==0) {
            g1_import::g1_stats();
          }
          $task->set('queue', $queue = array('' => g1_import::$tree));
        }
        $log_message = g1_import::import_album($queue);
        if ($log_message) {
          $task->log($log_message);
        }
        $task->status = t(
          'Importing albums (%count of %total)',
          array('count' => $done['albums'] + 1, 'total' => $stats['albums']));
        break;

      case 'items':
        if (empty($queue)) {
          if (count(g1_import::$queued_items)==0) {
            g1_import::g1_stats();
          }

          $queuelen = 100;
          $thisstart = $task->get('last_id', 0);
          $nextstart = $thisstart + $queuelen;
          $task->set('last_id', $nextstart);

          $task->set('queue', $queue = array_splice(g1_import::$queued_items, $thisstart, $queuelen));
        }
        $log_message = g1_import::import_item($queue);
        if ($log_message) {
          $task->log($log_message);
        }
        $task->status = t(
          'Importing photos (%count of %total)',
          array('count' => $done['items'] + 1, 'total' => $stats['items']));
        break;

      case 'comments':
        if (empty($queue)) {
          if (count(g1_import::$queued_comments)==0) {
            g1_import::g1_stats();
          }

          $queuelen = 100;
          $thisstart = $task->get('last_id', 0);
          $nextstart = $thisstart + $queuelen;
          $task->set('last_id', $nextstart);

          $task->set('queue', $queue = array_splice(g1_import::$queued_comments, $thisstart, $queuelen));
        }
        $log_message = g1_import::import_comment($queue);
        if ($log_message) {
          $task->log($log_message);
        }
        $task->status = t(
          'Importing comments (%count of %total)',
          array('count' => $done['comments'] + 1, 'total' => $stats['comments']));
        break;

      case 'highlights':
        if (empty($queue)) {
          if (count(g1_import::$queued_highlights)==0) {
            g1_import::g1_stats();
          }

          $queuelen = 100;
          $thisstart = $task->get('last_id', 0);
          $nextstart = $thisstart + $queuelen;
          $task->set('last_id', $nextstart);

          $task->set('queue', $queue = array_splice(g1_import::$queued_highlights, $thisstart, $queuelen));
        }
        $log_message = g1_import::set_album_highlight($queue);
        if ($log_message) {
          $task->log($log_message);
        }
        $task->status = t(
          'Album highlights (%count of %total)',
          array('count' => $done['highlights'] + 1, 'total' => $stats['highlights']));
        break;

      case 'fix':
        if (empty($queue)) {
          if (count(g1_import::$albums_flat)==0) {
            g1_import::g1_stats();
          }
          $task->set('queue', $queue = 'dummy');
        }
        $log_message = g1_import::hotfix_all();
        if ($log_message) {
          $task->log($log_message);
        }
        $task->status = t(
          'Final Hotfixing (%count of %total)',
          array('count' => $done['fix'] + 1, 'total' => $stats['fix']));
        break;

      case 'done':
        $task->status = t('Import complete');
        $task->done = true;
        $task->state = 'success';
        break;
      }

      if (!$task->done) {
        $done[$modes[$mode]]++;
        $completed++;
      }
    }

    $task->percent_complete = 100 * ($completed / $total);
    $task->set('completed', $completed);
    $task->set('mode', $mode);
    $task->set('queue', $queue);
    $task->set('done', $done);
  }
}
