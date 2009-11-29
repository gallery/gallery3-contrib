<?php defined('SYSPATH') OR die('No direct access allowed.');

// Get the day names
$days = Calendar::days(2);

// Previous and next month timestamps
$next = mktime(0, 0, 0, $month + 1, 1, $year);
$prev = mktime(0, 0, 0, $month - 1, 1, $year);

// Import the GET query array locally and remove the day
$qs = $_GET;
unset($qs['day']);

// Previous and next month query URIs
$prev = Router::$current_uri.'?'.http_build_query(array_merge($qs, array('month' => date('n', $prev), 'year' => date('Y', $prev))));
$next = Router::$current_uri.'?'.http_build_query(array_merge($qs, array('month' => date('n', $next), 'year' => date('Y', $next))));

?>
<table class="calendar">
<tr class="controls">
<td class="title" colspan="7" align="center">
<a href="<? print url::site("calendarview/month/" . $year . "/" . $calendar_user . "/" . $month ) ?>"><?php print t(strftime('%B', mktime(0, 0, 0, $month, 1, $year))) . " " . t(strftime('%Y', mktime(0, 0, 0, $month, 1, $year))) ?></a>
</td>
</tr>
<tr>
<?php foreach ($days as $day): ?>
<th><?php echo t($day) ?></th>
<?php endforeach ?>
</tr>
<?php foreach ($weeks as $week): ?>
<tr>
<?php foreach ($week as $day):

list ($number, $current, $data) = $day;

if (is_array($data))
{
	$classes = $data['classes'];
	$output = empty($data['output']) ? '' : '<ul class="output"><li>'.implode('</li><li>', $data['output']).'</li></ul>';
}
else
{
	$classes = array();
	$output = '';
}

?>
<? if ($day[1] == true) { ?>
<td class="<?php echo implode(' ', $classes) ?>"><span class="day"><?php echo $day[0] ?></span><?php echo $output ?></td>
<? } else { ?>
<td></td>
<? } ?>
<?php endforeach ?>
</tr>
<?php endforeach ?>
</table>
