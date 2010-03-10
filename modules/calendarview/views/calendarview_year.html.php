<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-album-header">
  <div id="g-album-header-buttons">
    <?= $theme->dynamic_top() ?>
  </div>
  <h1><?= html::clean($title) ?></h1>
</div>

<br/><?= $calendar_user_year_form ?><br /><br />

<?
  $counter_months = 1;
  // Loop through January to November in the current year.
  while ($counter_months <12) {
    print "<div id=\"g-calendar-grid\">";

    // Figure out if any photos were taken for the current month.
    if ($calendar_user == "-1") {
      $month_count = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
        ->where("captured", "<", mktime(0, 0, 0, $counter_months+1, 1, $calendar_year))
        ->find_all()
        ->count();
    } else {
      $month_count = ORM::factory("item")
        ->viewable()
        ->where("owner_id", "=", $calendar_user)
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
        ->where("captured", "<", mktime(0, 0, 0, $counter_months+1, 1, $calendar_year))
        ->find_all()
        ->count();
    }
    if ($month_count > 0) {
      $month_url = url::site("calendarview/month/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/");
    } else {
      $month_url = "";
    }
    $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);

    // If there are photos, loop through each day in the month and display links on the correct dates.
    if ($month_count > 0) {
      $curr_day = 1;
      $MAX_DAYS = date('t', mktime(00, 00, 00, $counter_months, 1, $calendar_year));
      while ($curr_day < $MAX_DAYS) {
        if ($calendar_user == "-1") {
          $day_count = ORM::factory("item")
            ->viewable()
            ->where("type", "!=", "album")
            ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
            ->where("captured", "<", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
            ->find_all()
            ->count();
        } else {
          $day_count = ORM::factory("item")
            ->viewable()
            ->where("owner_id", "=", $calendar_user)
            ->where("type", "!=", "album")
            ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
            ->where("captured", "<", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
            ->find_all()
            ->count();
        }
        if ($day_count > 0) {
          $calendar->event($curr_day, url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $curr_day));
        }
        $curr_day++;
      }

      // Do the last day of the month seperately, because the mktime code is different.
      if ($calendar_user == "-1") {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("type", "!=", "album")
          ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
          ->where("captured", "<",mktime(0, 0, 0, ($counter_months + 1), 1, $calendar_year))
          ->find_all()
          ->count();
      } else {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("owner_id", "=", $calendar_user)
          ->where("type", "!=", "album")
          ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
          ->where("captured", "<", mktime(0, 0, 0, ($counter_months + 1), 1, $calendar_year))
          ->find_all()
          ->count();
      }
      if ($day_count > 0) {
        $calendar->event($MAX_DAYS, url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $MAX_DAYS));
      }
    }
    echo $calendar->render();
    print "</div>";
    $counter_months++;
  }

  // Do December seperately, because the mktime code is different.
  print "<div id=\"g-calendar-grid\">";
  if ($calendar_user == "-1") {
    $month_count = ORM::factory("item")
      ->viewable()
      ->where("type", "!=", "album")
      ->where("captured", ">=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
      ->where("captured", "<", mktime(0, 0, 0, 1, 1, ($calendar_year + 1)))
      ->find_all()
      ->count();
  } else {
    $month_count = ORM::factory("item")
      ->viewable()
      ->where("owner_id", "=", $calendar_user)
      ->where("type", "!=", "album")
      ->where("captured", ">=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
      ->where("captured", "<", mktime(0, 0, 0, 1, 1, ($calendar_year + 1)))
      ->find_all()
      ->count();
  }
  if ($month_count > 0) {
    $month_url = url::site("calendarview/month/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/");
  } else {
    $month_url = "";
  }
  $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);
  if ($month_count > 0) {
    $curr_day = 1;
    $MAX_DAYS = date('t', mktime(00, 00, 00, $counter_months, 1, $calendar_year));
    while ($curr_day < $MAX_DAYS) {
      if ($calendar_user == "-1") {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("type", "!=", "album")
          ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
          ->where("captured", "<", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
          ->find_all()
          ->count();
      } else {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("owner_id", "=", $calendar_user)
          ->where("type", "!=", "album")
          ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
          ->where("captured", "<", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
          ->find_all()
          ->count();
      }
      if ($day_count > 0) {
        $calendar->event($curr_day, url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $curr_day));
      }
      $curr_day++;
    }
    if ($calendar_user == "-1") {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
        ->where("captured", "<", mktime(0, 0, 0, 1, 1, $calendar_year+1))
        ->find_all()
        ->count();
    } else {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("owner_id", "=", $calendar_user)
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
        ->where("captured", "<", mktime(0, 0, 0, 1, 1, $calendar_year+1))
        ->find_all()
        ->count();
    }
    if ($day_count > 0) {
      $calendar->event($MAX_DAYS, url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $MAX_DAYS));
    }
  }
  $counter_months++;
  echo $calendar->render();
  print "</div>";
?>