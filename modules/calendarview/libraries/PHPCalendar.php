<?php defined('SYSPATH') OR die('No direct access allowed.');

class PHPCalendar_Core {
	// Month and year to use for calendaring
	protected $month;
	protected $year;

	// First Day of the Week (0 = Sunday, 1 = Monday, etc.).
	protected $week_start = 1;

	// Events for the current month.
	protected $event_data = Array();

	public function __construct($month = NULL, $year = NULL)
	{
		empty($month) and $month = date('n'); // Current month
		empty($year)  and $year  = date('Y'); // Current year

		// Set the month and year
		$this->month = (int) $month;
		$this->year  = (int) $year;

	}
	
	public function event($day_of_the_week, $event_url = NULL, $css_id = NULL, $custom_text = NULL) {
		$this->event_data += Array($day_of_the_week => Array($event_url, $css_id, $custom_text));
	}
	
	public function render()
	{
	    return $this->generate_calendar($this->year, $this->month, $this->event_data, 2, NULL, $this->week_start, NULL);
	}

	# PHP Calendar (version 2.3), written by Keith Devens
	# http://keithdevens.com/software/php_calendar
	#  see example at http://keithdevens.com/weblog
	# License: http://keithdevens.com/software/license
	function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
		$first_of_month = gmmktime(0,0,0,$month,1,$year);
		#remember that mktime will automatically correct if invalid dates are entered
		# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
		# this provides a built in "rounding" feature to generate_calendar()

		$day_names = array(); #generate all the day names according to the current locale
		for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
			$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

		list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
		$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
		$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

		#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
		@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
		if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
		if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
		$calendar = '<table class="calendar">'."\n".
			'<td class="title" colspan="7" align="center">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</td></tr>\n<tr>";

		if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
			#if day_name_length is >3, the full name of the day will be printed
			foreach($day_names as $d)
				$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
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