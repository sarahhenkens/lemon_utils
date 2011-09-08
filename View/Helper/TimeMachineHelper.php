<?php
/**
 * TimeMachine Helper class file.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author        Cake Software Foundation, Inc.
 * @author        Jelle Henkens (jelle.henkens@gmail.com)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Time Helper class for easy use of time data.
 *
 * Manipulation of time data.
 *
 * @package       Cake.View.Helper
 * @link http://book.cakephp.org/view/1470/Time
 */
class TimeMachineHelper extends AppHelper {

/**
 * The format to use when formatting a time using `TimeMachinehelper::nice()`
 *
 * The format should use the locale strings as defined in the PHP docs under
 * `strftime` (http://php.net/manual/en/function.strftime.php)
 *
 * @var string
 * @see TimeMachinehelper::format()
 */
	public $niceFormat = '%a, %b %eS %Y, %H:%M';

/**
 * Constructor
 *
 * @param View $View the view object the helper is attached to.
 * @param array $settings Settings array Settings array
 */
	public function __construct(View $View, $settings = array()) {
		if (isset($settings['niceFormat'])) {
			$this->niceFormat = $settings['niceFormat'];
		}
		parent::__construct($View, $settings);
	}

/**
 * Converts a string representing the format for the function strftime and returns a
 * windows safe and i18n aware format.
 *
 * @param string $format Format with specifiers for strftime function.
 *    Accepts the special specifier %S which mimics th modifier S for date()
 * @param string $time UNIX timestamp
 * @return string windows safe and date() function compatible format for strftime
 */
	public function convertSpecifiers($format, $time = null) {
		if (!$time) {
			$time = time();
		}
		$this->__time = $time;
		return preg_replace_callback('/\%(\w+)/', array($this, '_translateSpecifier'), $format);
	}

/**
 * Auxiliary function to translate a matched specifier element from a regular expresion into
 * a windows safe and i18n aware specifier
 *
 * @param array $specifier match from regular expression
 * @return string converted element
 */
	protected function _translateSpecifier($specifier) {
		switch ($specifier[1]) {
			case 'a':
				$abday = __dc('cake', 'abday', 5);
				if (is_array($abday)) {
					return $abday[date('w', $this->__time)];
				}
				break;
			case 'A':
				$day = __dc('cake', 'day', 5);
				if (is_array($day)) {
					return $day[date('w', $this->__time)];
				}
				break;
			case 'c':
				$format = __dc('cake', 'd_t_fmt', 5);
				if ($format != 'd_t_fmt') {
					return $this->convertSpecifiers($format, $this->__time);
				}
				break;
			case 'C':
				return sprintf("%02d", date('Y', $this->__time) / 100);
			case 'D':
				return '%m/%d/%y';
			case 'e':
				if (DS === '/') {
					return '%e';
				}
				$day = date('j', $this->__time);
				if ($day < 10) {
					$day = ' ' . $day;
				}
				return $day;
			case 'eS' :
				return date('jS', $this->__time);
			case 'b':
			case 'h':
				$months = __dc('cake', 'abmon', 5);
				if (is_array($months)) {
					return $months[date('n', $this->__time) -1];
				}
				return '%b';
			case 'B':
				$months = __dc('cake', 'mon', 5);
				if (is_array($months)) {
					return $months[date('n', $this->__time) -1];
				}
				break;
			case 'n':
				return "\n";
			case 'p':
			case 'P':
				$default = array('am' => 0, 'pm' => 1);
				$meridiem = $default[date('a',$this->__time)];
				$format = __dc('cake', 'am_pm', 5);
				if (is_array($format)) {
					$meridiem = $format[$meridiem];
					return ($specifier[1] == 'P') ? strtolower($meridiem) : strtoupper($meridiem);
				}
				break;
			case 'r':
				$complete = __dc('cake', 't_fmt_ampm', 5);
				if ($complete != 't_fmt_ampm') {
					return str_replace('%p',$this->_translateSpecifier(array('%p', 'p')),$complete);
				}
				break;
			case 'R':
				return date('H:i', $this->__time);
			case 't':
				return "\t";
			case 'T':
				return '%H:%M:%S';
			case 'u':
				return ($weekDay = date('w', $this->__time)) ? $weekDay : 7;
			case 'x':
				$format = __dc('cake', 'd_fmt', 5);
				if ($format != 'd_fmt') {
					return $this->convertSpecifiers($format, $this->__time);
				}
				break;
			case 'X':
				$format = __dc('cake', 't_fmt', 5);
				if ($format != 't_fmt') {
					return $this->convertSpecifiers($format, $this->__time);
				}
				break;
		}
		return $specifier[0];
	}

/**
 * Converts given time (in server's time zone) to user's local time, given his/her offset from GMT.
 *
 * @param string $serverTime UNIX timestamp
 * @param integer $userOffset User's offset from GMT (in hours)
 * @return string UNIX timestamp
 */
	public function convert($serverTime, $userOffset) {
		$serverOffset = $this->serverOffset();
		$gmtTime = $serverTime - $serverOffset;
		$userTime = $gmtTime + $userOffset * (60*60);
		return $userTime;
	}

/**
 * Returns server's offset from UTC in seconds.
 *
 * @return integer Offset
 */
	public function serverOffset() {
		return date('Z', time());
	}

/**
 * Returns a DateTime object, given either a UNIX timestamp, a valid strtotime() date string or DateTime object.
 * Timezone can be a string or a DateTimeZone object.
 *
 * @param mixed $dateString Datetime string
 * @param mixed $timezone timezone name or DateTimeZone object
 * @return DateTime DateTime object with the correct timezone
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function fromString($dateString, $timezone = null) {
		if (empty($dateString)) {
			return false;
		}
		if (is_object($dateString)) {
			$date = $dateString;
		} elseif (is_integer($dateString) || is_numeric($dateString)) {
			$date = date_create('@' . $dateString);
			$date->setTimezone(new DateTimeZone(date_default_timezone_get()));
		} else {
			$date = date_create($dateString);
		}
		if (!$date) {
			return false;
		}
		$timezone = $this->timezone($timezone);
		if ($timezone !== null) {
			$date->setTimeZone($timezone);
		}
		return $date;
	}

/**
 * Returns a timezone object from a string or the user's timezone object
 *
 * @param mixed $timezone DateTimeZone object or a timezone name
 * @return DateTimeZone
 */
	public function timezone($timezone = null) {
		if (is_object($timezone)) {
			return $timezone;
		}
		if ($timezone === null) {
			$timezone = Configure::read('Config.timezone');
		}
		if ($timezone === null) {
			$timezone = date_default_timezone_get();
		}

		return new DateTimeZone($timezone);
	}

/**
 * Returns a nicely formatted date string for given Datetime string, DateTime object or UNIX time.
 *
 * See http://php.net/manual/en/function.strftime.php for information on formatting
 * using locale strings.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @param string $format The format to use. If null, `TimeMachinehelper::$niceFormat` is used
 * @return string Formatted date string
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function nice($dateString = null, $timezone = null, $format = null) {
		if ($dateString != null) {
			$date = $this->fromString($dateString, $timezone);
		} else {
			$date = date_create();
		}
		if (!$format) {
			$format = $this->niceFormat;
		}
		$format = $this->convertSpecifiers($format, $date->format('U'));
		return strftime($format, $date->format('U'));
	}

/**
 * Returns a formatted descriptive date string for given datetime string.
 *
 * If the given date is today, the returned string could be "Today, 16:54".
 * If the given date was yesterday, the returned string could be "Yesterday, 16:54".
 * If $dateString's year is the current year, the returned string does not
 * include mention of the year.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Described, relative date string
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function niceShort($dateString = null, $timezone = null) {
		$date = $dateString ? $this->fromString($dateString, $timezone) : date_create();

		$y = $this->isThisYear($date) ? '' : ' %Y';

		if ($this->isToday($dateString, $timezone)) {
			$ret = __d('cake', 'Today, %s', strftime("%H:%M", $date->format('U')));
		} elseif ($this->wasYesterday($dateString, $timezone)) {
			$ret = __d('cake', 'Yesterday, %s', strftime("%H:%M", $date->format('U')));
		} else {
			$format = $this->convertSpecifiers("%b %eS{$y}, %H:%M", $date->format('U'));
			$ret = strftime($format, $date->format('U'));
		}

		return $ret;
	}

/**
 * Returns a partial SQL string to search for all records between two dates.
 *
 * @param mixed $begin Datetime string, DateTime object or Unix timestamp
 * @param mixed $end Datetime string, DateTime object or Unix timestamp
 * @param string $fieldName Name of database field to compare with
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Partial SQL string.
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function daysAsSql($begin, $end, $fieldName, $timezone = null) {
		$begin = $this->fromString($begin, $timezone);
		$end = $this->fromString($end, $timezone);
		$begin = $begin->format('Y-m-d') . ' 00:00:00';
		$end = $end->format('Y-m-d') . ' 23:59:59';

		return "($fieldName >= '$begin') AND ($fieldName <= '$end')";
	}

/**
 * Returns a partial SQL string to search for all records between two times
 * occurring on the same day.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param string $fieldName Name of database field to compare with
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Partial SQL string.
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function dayAsSql($dateString, $fieldName, $timezone = null) {
		return $this->daysAsSql($dateString, $dateString, $fieldName, $timezone);
	}

/**
 * Returns true if given datetime string is today.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean True if datetime string is today
 */
	public function isToday($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('Y-m-d') == date_create('now', $date->getTimezone())->format('Y-m-d');
	}

/**
 * Returns true if given datetime string is within this week.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean True if datetime string is within current week
 * @link http://book.cakephp.org/view/1472/Testing-Time
 */
	public function isThisWeek($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('W o') == date_create('now', $date->getTimezone())->format('W o');
	}

/**
 * Returns true if given datetime string is within this month
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean True if datetime string is within current month
 * @link http://book.cakephp.org/view/1472/Testing-Time
 */
	public function isThisMonth($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('m Y') == date_create('now', $date->getTimezone())->format('m Y');
	}

/**
 * Returns true if given datetime string is within current year.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean True if datetime string is within current year
 * @link http://book.cakephp.org/view/1472/Testing-Time
 */
	public function isThisYear($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('Y') == date_create('now', $date->getTimezone())->format('Y');
	}

/**
 * Returns true if given datetime string was yesterday.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean True if datetime string was yesterday
 * @link http://book.cakephp.org/view/1472/Testing-Time
 *
 */
	public function wasYesterday($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('Y-m-d') == date_create('yesterday', $date->getTimezone())->format('Y-m-d');
	}

/**
 * Returns true if given datetime string is tomorrow.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean True if datetime string was yesterday
 * @link http://book.cakephp.org/view/1472/Testing-Time
 */
	public function isTomorrow($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('Y-m-d') == date_create('tomorrow', $date->getTimezone())->format('Y-m-d');
	}

/**
 * Returns the quarter
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param boolean $range if true returns a range in Y-m-d format
 * @return boolean True if datetime string is within current week
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function toQuarter($dateString, $range = false) {
		$time = $this->fromString($dateString);
		$date = ceil($time->format('m') / 3);

		if ($range !== false) {
			$year = $time->format('Y');

			switch ($date) {
				case 1:
					$date = array($year.'-01-01', $year.'-03-31');
					break;
				case 2:
					$date = array($year.'-04-01', $year.'-06-30');
					break;
				case 3:
					$date = array($year.'-07-01', $year.'-09-30');
					break;
				case 4:
					$date = array($year.'-10-01', $year.'-12-31');
					break;
			}
		}
		return $date;
	}

/**
 * Returns a UNIX timestamp from a textual datetime description.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return integer Unix timestamp
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function toUnix($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		if ($date === false) {
			return false;
		}
		return $date->format('U');
}

/**
 * Returns a date formatted for Atom RSS feeds.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Formatted date string
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function toAtom($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('Y-m-d\TH:i:s\Z');
	}

/**
 * Formats date for RSS feeds
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Formatted date string
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function toRSS($dateString, $timezone = null) {
		$date = $this->fromString($dateString, $timezone);
		return $date->format('r');
	}

/**
 * Returns either a relative date or a formatted date depending
 * on the difference between the current time and given datetime.
 * $datetime can be in a <i>strtotime</i> - parsable format, DateTime object or Unix timestamp
 *
 * ### Options:
 *
 * - `format` => a fall back format if the relative time is longer than the duration specified by end
 * - `end` => The end of relative time telling
 * - `timezone` => User's timezone name or DateTimeZone object
 *
 * Relative dates look something like this:
 *	3 weeks, 4 days ago
 *	15 seconds ago
 *
 * Default date formatting is d/m/yy e.g: on 18/2/09
 *
 * The returned string includes 'ago' or 'on' and assumes you'll properly add a word
 * like 'Posted ' before the function output.
 *
 * @param string $dateTime Datetime string or Unix timestamp
 * @param array $options Default format if timestamp is used in $dateString
 * @return string Relative time string.
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function timeAgoInWords($dateTime, $options = array()) {
		$timezone = null;
		if (is_array($options) && isset($options['timezone'])) {
			$timezone = $options['timezone'];
		}
		$date = $this->fromString($dateTime, $timezone);
		$now = date_create('now', $date->getTimezone());

		$backwards = ($date > $now);

		$format = 'j/n/y';
		$end = '+1 month';

		if (is_array($options)) {
			if (isset($options['format'])) {
				$format = $options['format'];
				unset($options['format']);
			}
			if (isset($options['end'])) {
				$end = $options['end'];
				unset($options['end']);
			}
		} else {
			$format = $options;
		}

		if ($backwards) {
			$futureTime = $date;
			$pastTime = $now;
		} else {
			$futureTime = $now;
			$pastTime = $date;
		}
		$diff = $futureTime->format('U') - $pastTime->format('U');

		// If more than a week, then take into account the length of months
		if ($diff >= 604800) {
			$current = array();

			list($future['H'], $future['i'], $future['s'], $future['d'], $future['m'], $future['Y']) = explode('/', $futureTime->format('H/i/s/d/m/Y'));

			list($past['H'], $past['i'], $past['s'], $past['d'], $past['m'], $past['Y']) = explode('/', $pastTime->format('H/i/s/d/m/Y'));
			$years = $months = $weeks = $days = $hours = $minutes = $seconds = 0;

			if ($future['Y'] == $past['Y'] && $future['m'] == $past['m']) {
				$months = 0;
				$years = 0;
			} else {
				if ($future['Y'] == $past['Y']) {
					$months = $future['m'] - $past['m'];
				} else {
					$years = $future['Y'] - $past['Y'];
					$months = $future['m'] + ((12 * $years) - $past['m']);

					if ($months >= 12) {
						$years = floor($months / 12);
						$months = $months - ($years * 12);
					}

					if ($future['m'] < $past['m'] && $future['Y'] - $past['Y'] == 1) {
						$years --;
					}
				}
			}

			if ($future['d'] >= $past['d']) {
				$days = $future['d'] - $past['d'];
			} else {
				$daysInPastMonth = $pastTime->format('t');
				$daysInFutureMonth = date('t', mktime(0, 0, 0, $future['m'] - 1, 1, $future['Y']));

				if (!$backwards) {
					$days = ($daysInPastMonth - $past['d']) + $future['d'];
				} else {
					$days = ($daysInFutureMonth - $past['d']) + $future['d'];
				}

				if ($future['m'] != $past['m']) {
					$months --;
				}
			}

			if ($months == 0 && $years >= 1 && $diff < ($years * 31536000)) {
				$months = 11;
				$years --;
			}

			if ($months >= 12) {
				$years = $years + 1;
				$months = $months - 12;
			}

			if ($days >= 7) {
				$weeks = floor($days / 7);
				$days = $days - ($weeks * 7);
			}
		} else {
			$years = $months = $weeks = 0;
			$days = floor($diff / 86400);

			$diff = $diff - ($days * 86400);

			$hours = floor($diff / 3600);
			$diff = $diff - ($hours * 3600);

			$minutes = floor($diff / 60);
			$diff = $diff - ($minutes * 60);
			$seconds = $diff;
		}
		$relativeDate = '';
		$diff = $futureTime->format('U') - $pastTime->format('U');

		if ($diff > abs($now->format('U') - date_create($end, $now->getTimezone())->format('U'))) {
			$relativeDate = __d('cake', 'on %s', $date->format($format));
		} else {
			if ($years > 0) {
				// years and months and days
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d year', '%d years', $years, $years);
				$relativeDate .= $months > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d month', '%d months', $months, $months) : '';
				$relativeDate .= $weeks > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d week', '%d weeks', $weeks, $weeks) : '';
				$relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d day', '%d days', $days, $days) : '';
			} elseif (abs($months) > 0) {
				// months, weeks and days
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d month', '%d months', $months, $months);
				$relativeDate .= $weeks > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d week', '%d weeks', $weeks, $weeks) : '';
				$relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d day', '%d days', $days, $days) : '';
			} elseif (abs($weeks) > 0) {
				// weeks and days
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d week', '%d weeks', $weeks, $weeks);
				$relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d day', '%d days', $days, $days) : '';
			} elseif (abs($days) > 0) {
				// days and hours
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d day', '%d days', $days, $days);
				$relativeDate .= $hours > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d hour', '%d hours', $hours, $hours) : '';
			} elseif (abs($hours) > 0) {
				// hours and minutes
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d hour', '%d hours', $hours, $hours);
				$relativeDate .= $minutes > 0 ? ($relativeDate ? ', ' : '') . __dn('cake', '%d minute', '%d minutes', $minutes, $minutes) : '';
			} elseif (abs($minutes) > 0) {
				// minutes only
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d minute', '%d minutes', $minutes, $minutes);
			} else {
				// seconds only
				$relativeDate .= ($relativeDate ? ', ' : '') . __dn('cake', '%d second', '%d seconds', $seconds, $seconds);
			}

			if (!$backwards) {
				$relativeDate = __d('cake', '%s ago', $relativeDate);
			}
		}
		return $relativeDate;
	}

/**
 * Returns true if specified datetime was within the interval specified, else false.
 *
 * @param mixed $timeInterval the numeric value with space then time type.
 *    Example of valid types: 6 hours, 2 days, 1 minute.
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return boolean
 * @link http://book.cakephp.org/view/1472/Testing-Time
 */
	public function wasWithinLast($timeInterval, $dateString, $timezone = null) {
		$tmp = str_replace(' ', '', $timeInterval);
		if (is_numeric($tmp)) {
			$timeInterval = $tmp . ' ' . __d('cake', 'days');
		}

		$date = $this->fromString($dateString, $timezone);
		$interval = $this->fromString('-'.$timeInterval, $timezone);

		if ($date >= $interval && $date <= date_create('now', $date->getTimezone())) {
			return true;
		}

		return false;
	}

/**
 * Returns UTC, given either a UNIX timestamp, a valid strtotime() date string or a DateTime object.
 *
 * @param mixed $dateString Datetime string, DateTime object or Unix timestamp
 * @return string Formatted date string
 * @link http://book.cakephp.org/view/1471/Formatting
 */
	public function utc($dateString = null) {
		if ($dateString !== null) {
			$date = $this->fromString($dateString);
		} else {
			$date = new DateTime('now');
		}
		$date->setTimeZone(new DateTimeZone('UTC'));
		return intval($date->format('U'));
	}

/**
 * Returns a formatted date string, given either a UNIX timestamp, a valid strtotime() date string or a DateTime object.
 * This function also accepts a time string and a format string as first and second parameters.
 * In that case this function behaves as a wrapper for TimeMachinehelper::i18nFormat()
 *
 * @param string $format date format string (or a DateTime string)
 * @param mixed $date Datetime string, DateTime object or Unix timestamp
 * @param boolean $invalid flag to ignore results of fromString == false
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Formatted date string
 */
	public function format($format, $date = null, $invalid = false, $timezone = null) {
		$time = $this->fromString($date, $timezone);
		$_time = $this->fromString($format, $timezone);

		if (is_object($_time) && $time === false) {
			$format = $date;
			return $this->i18nFormat($_time, $format, $invalid);
		}
		if ($time === false && $invalid !== false) {
			return $invalid;
		}
		return $time->format($format);
	}

/**
 * Returns a formatted date string, given either a UNIX timestamp, a valid strtotime() date string or a DateTime object.
 * It take in account the default date format for the current language if a LC_TIME file is used.
 *
 * @param mixed $date Datetime string, DateTime object or Unix timestamp
 * @param string $format strftime format string.
 * @param boolean $invalid flag to ignore results of fromString == false
 * @param mixed $timezone User's timezone name or DateTimeZone object
 * @return string Formatted and translated date string
 */
	public function i18nFormat($date, $format = null, $invalid = false, $timezone = null) {
		$date = $this->fromString($date, $timezone);
		if ($date === false && $invalid !== false) {
			return $invalid;
		}
		if (empty($format)) {
			$format = '%x';
		}
		$format = $this->convertSpecifiers($format, $date->format('U'));
		return strftime($format, $date->format('U'));
	}
}
