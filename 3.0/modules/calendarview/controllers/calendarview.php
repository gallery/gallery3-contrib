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
class CalendarView_Controller extends Controller {
  public function calendar($display_year="", $display_user="") {
    // Draw a calendar for the year specified by $display_year.

    // Make sure the function parameters aren't null,
    //   give them default values if they are.
    if ($display_year == "") {
      $display_year = date('Y');
    }
    if ($display_user == "") {
      $display_user = "-1";
    }

    // Draw the page.
    $template = new Theme_View("calpage.html", "other", "CalendarView");
    $template->css("calendarview_calendar.css");
    $template->set_global("calendar_user", $display_user);
    $template->page_title = t("Gallery :: Calendar");
    $template->content = new View("calendarview_year.html");
    $template->content->calendar_year = $display_year;
    $template->content->calendar_user = $display_user;
    $template->content->calendar_user_year_form = $this->_get_calenderprefs_form($display_year, $display_user);
    $template->content->title = t("Calendar") . ": " . $display_year;
    // Set up breadcrumbs
    $calendar_breadcrumbs[0] = new Calendar_Breadcrumb(item::root()->title, item::root()->url());
    $calendar_breadcrumbs[1] = new Calendar_Breadcrumb($display_year, "");
    $template->set_global("breadcrumbs", $calendar_breadcrumbs);
    print $template;
  }

  public function day($display_year, $display_user, $display_month, $display_day) {
    // Display all images for the specified day.

    // Figure out the total number of photos to display.
    $day_count = 0;
    if ($display_user == "-1") {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $display_month, $display_day, $display_year))
        ->where("captured", "<", mktime(0, 0, 0, $display_month, ($display_day + 1), $display_year))
        ->find_all()
        ->count();
    } else {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("owner_id", "=", $display_user)
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $display_month, $display_day, $display_year))
        ->where("captured", "<", mktime(0, 0, 0, $display_month, ($display_day + 1), $display_year))
        ->find_all()
        ->count();
    }

    // Figure out paging stuff.
    $page_size = module::get_var("gallery", "page_size", 9);
    $page = (int) Input::instance()->get("page", "1");
    $offset = ($page-1) * $page_size;
    $max_pages = max(ceil($day_count / $page_size), 1);

    // Make sure that the page references a valid offset
    if (($page < 1) || ($page > $max_pages)) {
      throw new Kohana_404_Exception();
    }

    // Set up the page.
    $template = new Theme_View("calpage.html", "collection", "CalendarDayView");
    $template->set_global("page", $page);
    $template->set_global("max_pages", $max_pages);
    $template->set_global("page_size", $page_size);
    $template->page_title = t("Gallery :: Calendar");

    // Figure out which photos go on this page.
    if ($display_user == "-1") {
      $template->set_global("children", ORM::factory("item")
                            ->viewable()
                            ->where("type", "!=", "album")
                            ->where("captured", ">=", mktime(0, 0, 0, $display_month, $display_day, $display_year))
                            ->where("captured", "<", mktime(0, 0, 0, $display_month, ($display_day + 1), $display_year))
                            ->order_by("captured", "ASC")
                            ->find_all($page_size, $offset));
    } else {
      $template->set_global("children", ORM::factory("item")
                            ->viewable()
                            ->where("owner_id", "=", $display_user)
                            ->where("type", "!=", "album")
                            ->where("captured", ">=", mktime(0, 0, 0, $display_month, $display_day, $display_year))
                            ->where("captured", "<", mktime(0, 0, 0, $display_month, ($display_day + 1), $display_year))
                            ->order_by("captured", "ASC")
                            ->find_all($page_size, $offset));
    }

    // Set up breadcrumbs
    $calendar_breadcrumbs[0] = new Calendar_Breadcrumb(item::root()->title, item::root()->url());
    $calendar_breadcrumbs[1] = new Calendar_Breadcrumb($display_year, url::site("calendarview/calendar/" . $display_year . "/" . $display_user));
    $calendar_breadcrumbs[2] = new Calendar_Breadcrumb(t(date("F", mktime(0, 0, 0, $display_month, $display_day, $display_year))), url::site("calendarview/month/" . $display_year . "/" . $display_user . "/" . $display_month));
    $calendar_breadcrumbs[3] = new Calendar_Breadcrumb($display_day, "");
    $template->set_global("breadcrumbs", $calendar_breadcrumbs);

    // Finish setting up and then display the page.
    $template->set_global("children_count", $day_count);
    $template->content = new View("dynamic.html");
    $template->content->title = t("Photos From ") . date("d", mktime(0, 0, 0, $display_month, $display_day, $display_year)) . " " . t(date("F", mktime(0, 0, 0, $display_month, $display_day, $display_year))) . " " . date("Y", mktime(0, 0, 0, $display_month, $display_day, $display_year));
    print $template;
  }

  public function month($display_year, $display_user, $display_month) {
    // Display all images for the specified month.

    // Figure out the total number of photos to display.
    $day_count = 0;
    if ($display_user == "-1") {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("type", "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $display_month, 1, $display_year))
        ->where("captured", "<", mktime(0, 0, 0, $display_month+1, 1, $display_year))
        ->find_all()
        ->count();
    } else {
      $day_count = ORM::factory("item")
        ->viewable()
        ->where("owner_id", "=", $display_user)
        ->where("type",  "!=", "album")
        ->where("captured", ">=", mktime(0, 0, 0, $display_month, 1, $display_year))
        ->where("captured", "<", mktime(0, 0, 0, $display_month+1, 1, $display_year))
        ->find_all()
        ->count();
    }

    // Figure out paging stuff.
    $page_size = module::get_var("gallery", "page_size", 9);
    $page = (int) Input::instance()->get("page", "1");
    $offset = ($page-1) * $page_size;
    $max_pages = max(ceil($day_count / $page_size), 1);

    // Make sure that the page references a valid offset
    if (($page < 1) || ($page > $max_pages)) {
      throw new Kohana_404_Exception();
    }

    // Set up the page.
    $template = new Theme_View("calpage.html", "collection", "CalendarMonthView");
    $template->set_global("page", $page);
    $template->set_global("max_pages", $max_pages);
    $template->set_global("page_size", $page_size);
    $template->page_title = t("Gallery :: Calendar");

    // Figure out which photos go on this page.
    if ($display_user == "-1") {
      $template->set_global("children", ORM::factory("item")
                            ->viewable()
                            ->where("type", "!=", "album")
                            ->where("captured", ">=", mktime(0, 0, 0, $display_month, 1, $display_year))
                            ->where("captured", "<", mktime(0, 0, 0, $display_month+1, 1, $display_year))
                            ->order_by("captured", "ASC")
                            ->find_all($page_size, $offset));
    } else {
      $template->set_global("children", ORM::factory("item")
                            ->viewable()
                            ->where("owner_id", "=", $display_user)
                            ->where("type", "!=", "album")
                            ->where("captured", ">=", mktime(0, 0, 0, $display_month, 1, $display_year))
                            ->where("captured", "<", mktime(0, 0, 0, $display_month+1, 1, $display_year))
                            ->order_by("captured", "ASC")
                            ->find_all($page_size, $offset));
    }

    // Set up breadcrumbs
    $calendar_breadcrumbs[0] = new Calendar_Breadcrumb(item::root()->title, item::root()->url());
    $calendar_breadcrumbs[1] = new Calendar_Breadcrumb($display_year, url::site("calendarview/calendar/" . $display_year . "/" . $display_user));
    $calendar_breadcrumbs[2] = new Calendar_Breadcrumb(t(date("F", mktime(0, 0, 0, $display_month, 1, $display_year))), "");
    $template->set_global("breadcrumbs", $calendar_breadcrumbs);

    // Finish setting up and then display the page.
    $template->set_global("children_count", $day_count);
    $template->content = new View("dynamic.html");
    $template->content->title = t("Photos From ") . t(date("F", mktime(0, 0, 0, $display_month, 1, $display_year))) . " " . date("Y", mktime(0, 0, 0, $display_month, 1, $display_year));
    print $template;
  }

  private function _get_calenderprefs_form($display_year, $display_user) {
    // Generate a form to allow the visitor to select a year and a gallery photo owner.
    $calendar_group = new Forge("calendarview/setprefs", "", "post",
                      array("id" => "g-view-calendar-form"));

    // Generate a list of all Gallery users who have uploaded photos.
    $valid_users[-1] = "(All Users)";
    $gallery_users = ORM::factory("user")->find_all();
    foreach ($gallery_users as $one_user) {
      $count = ORM::factory("item")
               ->viewable()
               ->where("owner_id", "=", $one_user->id)
               ->where("type", "!=", "album")
               ->where("captured", "!=", "")
               ->find_all()
               ->count();
      if ($count > 0) {
        $valid_users[$one_user->id] = $one_user->full_name;
      }
    }

    // Generate a list of years, starting with the year the earliest photo was
    //   taken, and ending with the year of the most recent photo.
    $valid_years = Array();
    $all_photos = ORM::factory("item")
      ->viewable()
      //->where("owner_id", "=", $one_user->id)
      ->where("type", "!=", "album")
      ->where("captured", "!=", "")
      ->order_by("captured", "DESC")
      ->find_all();
    $counter = date('Y', $all_photos[count($all_photos)-1]->captured);
    while ($counter <= date('Y', $all_photos[0]->captured)) {
      $valid_years[$counter] = $counter;
      $counter++;
    }

    // Create the form.
    $calendar_group->dropdown('cal_user')
                   ->label(t("Display Photos From User: "))
                   ->id('cal_user')
                   ->options($valid_users)
                   ->selected($display_user);
    $calendar_group->dropdown('cal_year')
                   ->label(t("For Year: "))
                   ->id('cal_year')
                   ->options($valid_years)
                   ->selected($display_year);

    // Add a save button to the form.
    $calendar_group->submit("SaveSettings")->value(t("Go"))->id('cal_go');

    // Return the newly generated form.
    return $calendar_group;
  }

  public function setprefs() {
    // Change the calendar year and / or user.

    // Prevent Cross Site Request Forgery
    access::verify_csrf();

    // Get user specified settings.
    $str_user_id = Input::instance()->post("cal_user");
    $str_year_id = Input::instance()->post("cal_year");

    // redirect to the currect page.
    url::redirect(url::site("calendarview/calendar/" . $str_year_id . "/" . $str_user_id, request::protocol()));
  }
}