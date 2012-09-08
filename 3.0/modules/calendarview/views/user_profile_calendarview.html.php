<?php defined("SYSPATH") or die("No direct script access.") ?>
<?
  // Generate a list of items within the specified 3 month time-frame.
  $items = ORM::factory("item")
    ->viewable()
    ->where("owner_id", "=", $user_id)
    ->where("type", "!=", "album")
    ->where("captured", ">=", mktime(0, 0, 0, $user_month-2, 1, $user_year))
    ->where("captured", "<", mktime(0, 0, 0, $user_month+1, 1, ($user_year)))
    ->order_by("captured")
    ->find_all();

  // Set up some initial variables.
  $calendar_year = $user_year;
  $counter_months = $user_month - 2;
  if ($counter_months < 1) {
    $counter_months += 12;
    $calendar_year--;
  }
  $counter_days = 0;
  $counter = 0;

  // Print the first month.
  print "<div id=\"g-calendar-profile-grid\">";
  if ((count($items) > 0) && (date("n", $items[$counter]->captured) == $counter_months)) {
    $month_url = url::site("calendarview/month/" . $calendar_year . "/" . $user_id . "/" . $counter_months . "/");
  } else {
    $month_url = "";
  }
  $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);

  // Loop through each photo taken during the 3 month time frame, and see what month and day they were taken on.
  //   Make the corresponding dates on the calendars into clickable links.
  while ($counter < (count($items))) {

    // Check and see if we've switched to a new month.
    //  If so, render the current calendar and set up a new one.
    //  Continue printing empty months until we reach the next photo or the last month.
    while (date("n", $items[$counter]->captured) != $counter_months) {
      echo $calendar->render();
      print "</div>";
      $counter_months++;
      if ($counter_months == 13) {
        $counter_months = 1;
        $calendar_year++;
      }
      $counter_days = 0;
      print "<div id=\"g-calendar-profile-grid\">";
      if (date("n", $items[$counter]->captured) == $counter_months) {
        $month_url = url::site("calendarview/month/" . $calendar_year . "/" . $user_id . "/" . $counter_months . "/");
      } else {
        $month_url = "";
      }
      $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);
    }

    // If the day of the current photo is different then the day of the previous photo, 
    //   then add a link to the calendar for this date and set the current day to this day.
    if (date("j", $items[$counter]->captured) > $counter_days) {
      $counter_days = date("j", $items[$counter]->captured);
      $calendar->event($counter_days, url::site("calendarview/day/" . $calendar_year . "/" . $user_id . "/" . $counter_months . "/" . $counter_days));
    }  

    // Move onto the next photo.
    $counter++;
  }

  // Print out the last calendar to be generated.
  echo $calendar->render();
  print "</div>";
  $counter_months++;

  // If the calendar that was previously rendered was not the final month, 
  //   then print out a few empty months to fill the remaining space.
  while ($counter_months < $user_month + 1) {
    print "<div id=\"g-calendar-profile-grid\">";
    $month_url = "";
    $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);
    echo $calendar->render();
    print "</div>";
    $counter_months++;
  }

?>
<br clear="all" /><br /><br />
<div align="right"><a href="<?=url::site("calendarview/calendar/{$user_year}/{$user_id}"); ?>"><?=t("View full calendar"); ?> &gt;&gt;</a></div>
