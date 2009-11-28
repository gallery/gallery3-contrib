<?php defined("SYSPATH") or die("No direct script access.") ?>

<h1 align="center"><?=t($calendar_year) ?></h1>
<?= $calendar_user_year_form ?>

<?
  print "<table><tr>";
  $counter_months = 1;
  // Loop through each month in the current year.
  while ($counter_months <12) {
    print "<td>";
    $calendar = new Calendar($counter_months, $calendar_year);

    // Figure out if any photos were taken for the current month.
    if ($calendar_user == "-1") {
      $month_count = ORM::factory("item")
        ->viewable()
        ->where("type !=", "album")
        ->where("captured >=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
        ->where("captured <", mktime(0, 0, 0, $counter_months+1, 1, $calendar_year))
        ->find_all()
        ->count();
    } else {
      $month_count = ORM::factory("item")
        ->viewable()
        ->where("owner_id", $calendar_user)
        ->where("type !=", "album")
        ->where("captured >=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
        ->where("captured <", mktime(0, 0, 0, $counter_months+1, 1, $calendar_year))
        ->find_all()
        ->count();
    }

    // If there are photos, loop through each day in the month and display links on the correct dates.
    if ($month_count > 0) {
      $curr_day = 1;
      $MAX_DAYS = date('t', mktime(00, 00, 00, $counter_months, 1, $calendar_year));
      while ($curr_day < $MAX_DAYS) {
        if ($calendar_user == "-1") {
          $day_count = ORM::factory("item")
            ->viewable()
            ->where("type !=", "album")
            ->where("captured >=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
            ->where("captured <", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
            ->find_all()
            ->count();
        } else {
          $day_count = ORM::factory("item")
            ->viewable()
            ->where("owner_id", $calendar_user)
            ->where("type !=", "album")
            ->where("captured >=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
            ->where("captured <", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
            ->find_all()
            ->count();
        }
        if ($day_count > 0) {
          $calendar -> attach($calendar -> event() 
                                       -> condition('year', $calendar_year) 
                                       -> condition('month', $counter_months) 
                                       -> condition('day', $curr_day)  
                                       -> output(html::anchor(url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $curr_day), $day_count)));
        }
        $curr_day++;
      }

      // Do the last day of the month seperately, because the mktime code is different.
      if ($calendar_user == "-1") {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("type !=", "album")
          ->where("captured >=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
          ->where("captured <", mktime(0, 0, 0, ($counter_months + 1), 1, $calendar_year))
          ->find_all()
          ->count();
      } else {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("owner_id", $calendar_user)
          ->where("type !=", "album")
          ->where("captured >=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
          ->where("captured <", mktime(0, 0, 0, ($counter_months + 1), 1, $calendar_year))
          ->find_all()
          ->count();
      }
      if ($day_count > 0) {
        $calendar -> attach($calendar -> event() 
                                      -> condition('year', $calendar_year) 
                                      -> condition('month', $counter_months) 
                                      -> condition('day', $MAX_DAYS)  
                                      -> output(html::anchor(url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $MAX_DAYS), $day_count)));
      }
    }
    echo $calendar->render();
    print "</td>";
    if (($counter_months == 3) || ($counter_months == 6) || ($counter_months == 9)) {
      print "</tr><tr>";
    }
    $counter_months++;
  }
  
  // Do December seperately, because the mktime code is different.
  print "<td>";
  $calendar = new Calendar($counter_months, $calendar_year);
  if ($calendar_user == "-1") {
    $month_count = ORM::factory("item")
      ->viewable()
      ->where("type !=", "album")
      ->where("captured >=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
      ->where("captured <", mktime(0, 0, 0, 1, 1, ($calendar_year + 1)))
      ->find_all()
      ->count();
  } else {
    $month_count = ORM::factory("item")
      ->viewable()
      ->where("owner_id", $calendar_user)
      ->where("type !=", "album")
      ->where("captured >=", mktime(0, 0, 0, $counter_months, 1, $calendar_year))
      ->where("captured <", mktime(0, 0, 0, 1, 1, ($calendar_year + 1)))
      ->find_all()
      ->count();  
  }
  if ($month_count > 0) {
    $curr_day = 1;
    $MAX_DAYS = date('t', mktime(00, 00, 00, $counter_months, 1, $calendar_year));
    while ($curr_day < $MAX_DAYS) {
      if ($calendar_user == "-1") {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("type !=", "album")
          ->where("captured >=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
          ->where("captured <", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
          ->find_all()
          ->count();
      } else {
        $day_count = ORM::factory("item")
          ->viewable()
          ->where("owner_id", $calendar_user)
          ->where("type !=", "album")
          ->where("captured >=", mktime(0, 0, 0, $counter_months, $curr_day, $calendar_year))
          ->where("captured <", mktime(0, 0, 0, $counter_months, ($curr_day + 1), $calendar_year))
          ->find_all()
          ->count();
      }
      if ($day_count > 0) {
        $calendar -> attach($calendar -> event() 
                                      -> condition('year', $calendar_year) 
                                      -> condition('month', $counter_months) 
                                      -> condition('day', $curr_day)  
                                       -> output(html::anchor(url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $curr_day), $day_count)));
      }
      $curr_day++;
    }
    if ($calendar_user == "-1") {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("type !=", "album")
        ->where("captured >=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
        ->where("captured <", mktime(0, 0, 0, 1, 1, $calendar_year+1))
        ->find_all()
        ->count();
    } else {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("owner_id", $calendar_user)
        ->where("type !=", "album")
        ->where("captured >=", mktime(0, 0, 0, $counter_months, $MAX_DAYS, $calendar_year))
        ->where("captured <", mktime(0, 0, 0, 1, 1, $calendar_year+1))
        ->find_all()
        ->count();	
    }
    if ($day_count > 0) {
      $calendar -> attach($calendar -> event() 
                                    -> condition('year', $calendar_year) 
                                    -> condition('month', $counter_months) 
                                    -> condition('day', $MAX_DAYS)  
                                    -> output(html::anchor(url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $MAX_DAYS), $day_count)));

    }
  }
  $counter_months++;
  echo $calendar->render();
  print "</td></tr></table>";
?>
