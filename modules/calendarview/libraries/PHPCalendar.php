<?php defined('SYSPATH') OR die('No direct access allowed.');

class PHPCalendar_Core {
  // Month and year to use for calendaring
  protected $month;
  protected $year;
  protected $month_url;

  // First Day of the Week (0 = Sunday or 1 = Monday).
  protected $week_start = 0;

  // Events for the current month.
  protected $event_data = Array();

  public function __construct($month = NULL, $year = NULL, $url = NULL)
  {
    empty($month) and $month = date('n'); // Current month
    empty($year)  and $year  = date('Y'); // Current year

    // Set the month and year
    $this->month = (int) $month;
    $this->year  = (int) $year;
    $this->month_url = $url;
  }

  public function event($day_of_the_week, $event_url = NULL, $css_id = NULL, $custom_text = NULL) 
  {
    $this->event_data += Array($day_of_the_week => Array($event_url, $css_id, $custom_text));
  }

  public function render()
  {
    return $this->generate_calendar($this->year, $this->month, $this->event_data, 2, $this->month_url, $this->week_start, NULL);
  }

  # PHP Calendar (version 2.3), written by Keith Devens
  # http://keithdevens.com/software/php_calendar
  #  see example at http://keithdevens.com/weblog
  # License: http://keithdevens.com/software/license
  function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array())
  {
    $first_of_month = gmmktime(0,0,0,$month,1,$year);
    #remember that mktime will automatically correct if invalid dates are entered
    # for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
    # this provides a built in "rounding" feature to generate_calendar()

    if ($first_day == 0) $day_names = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"); 
    if ($first_day == 1) $day_names = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"); 

    list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
    $weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
    $title   = t(date("F", mktime(0, 0, 0, $month, 1, $year))) . '&nbsp;' . $year;

    #Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
    @list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
    if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
    if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
    $calendar = '<table class="calendar" id="g-calendar-month">'."\n".
      '<td class="title" colspan="7" align="center">'.$p.($month_href ? '<a href="'. ($month_href) .'">'.$title.'</a>' : $title).$n."</td></tr>\n<tr>";

    if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
      #if day_name_length is >3, the full name of the day will be printed
      foreach($day_names as $d)
        $calendar .= '<th abbr="' . $d .'">'.t($day_name_length < 4 ? substr($d,0,$day_name_length) : $d) . '</th>';
      $calendar .= "</tr>\n<tr>";
    }

    if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
    for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
      if($weekday == 7){
        $weekday   = 0; #start a new week
        $calendar .= "</tr>\n<tr>";
      }
      if(isset($days[$day]) and is_array($days[$day])){
        @list($link, $classes, $content) = $days[$day];
        if(is_null($content))  $content  = $day;
          $calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
            ($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>';
      }
      else $calendar .= "<td class=\"day\">$day</td>";
    }
    if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

    return $calendar."</tr>\n</table>\n";
  }
}
?>