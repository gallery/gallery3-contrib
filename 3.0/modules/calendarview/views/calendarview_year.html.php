<?php defined("SYSPATH") or die("No direct script access.") ?>
<div id="g-album-header">
  <div id="g-album-header-buttons">
    <?= $theme->dynamic_top() ?>
  </div>
  <h1><?= html::clean($title) ?></h1>
</div>

<br/><?= $calendar_user_year_form ?><br /><br />

<?
  // Search the db for all photos that were taken during the selected year.
  if ($calendar_user == "-1") {
    $items_for_year = ORM::factory("item")
      ->viewable()
      ->where("type", "!=", "album")
      ->where("captured", ">=", mktime(0, 0, 0, 1, 1, $calendar_year))
      ->where("captured", "<", mktime(0, 0, 0, 1, 1, ($calendar_year + 1)))
      ->order_by("captured")
      ->find_all();
  } else {
    $items_for_year = ORM::factory("item")
      ->viewable()
      ->where("owner_id", "=", $calendar_user)
      ->where("type", "!=", "album")
      ->where("captured", ">=", mktime(0, 0, 0, 1, 1, $calendar_year))
      ->where("captured", "<", mktime(0, 0, 0, 1, 1, ($calendar_year + 1)))
      ->order_by("captured")
      ->find_all();
  }

  // Set up some initial variables.
  $counter_months = 1;
  $counter_days = 0;
  $counter = 0;
  
  // Set up the January Calendar.
  //  Check and see if any photos were taken in January, 
  //  If so, make the month title into a clickable link.
  print "<div id=\"g-calendar-grid\">";
  if ((count($items_for_year) > 0) && (date("n", $items_for_year[$counter]->captured) == 1)) {
    $month_url = url::site("calendarview/month/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/");
  } else {
    $month_url = "";
  }
  $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);

  // Loop through each photo taken during this year, and see what month and day they were taken on.
  //   Make the corresponding dates on the calendars into clickable links.
  while ($counter < (count($items_for_year))) {
  
    // Check and see if we've switched to a new month.
    //  If so, render the current calendar and set up a new one.
    while (date("n", $items_for_year[$counter]->captured) > $counter_months) {
      echo $calendar->render();
      print "</div>";
      $counter_months++;
      $counter_days = 0;
      print "<div id=\"g-calendar-grid\">";
      if (date("n", $items_for_year[$counter]->captured) == $counter_months) {
        $month_url = url::site("calendarview/month/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/");
      } else {
        $month_url = "";
      }
      $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);
    }

    // If the day of the current photo is different then the day of the previous photo, 
    //   then add a link to the calendar for this date and set the current day to this day.
    if (date("j", $items_for_year[$counter]->captured) > $counter_days) {
      $counter_days = date("j", $items_for_year[$counter]->captured);
      $calendar->event($counter_days, url::site("calendarview/day/" . $calendar_year . "/" . $calendar_user . "/" . $counter_months . "/" . $counter_days));
    }  

    // Move onto the next photo.
    $counter++;
  }

  // Print out the last calendar to be generated.
  echo $calendar->render();
  print "</div>";
  $counter_months++;

  // If the calendar that was previously rendered was not December, 
  //   then print out a few empty months for the rest of the year.
  while ($counter_months < 13) {
    print "<div id=\"g-calendar-grid\">";
    $month_url = "";
    $calendar = new PHPCalendar($counter_months, $calendar_year, $month_url);
    echo $calendar->render();
    print "</div>";
    $counter_months++;
  }
?>
<?= $theme->dynamic_bottom() ?>
