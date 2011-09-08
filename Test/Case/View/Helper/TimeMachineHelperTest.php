<?php
/**
 * TimeMachineHelperTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       Cake.Test.Case.View.Helper
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author        Cake Software Foundation, Inc.
 * @author        Jelle Henkens (jelle.henkens@gmail.com)
 */
App::uses('TimeMachineHelper', 'LemonUtils.View/Helper');
App::uses('View', 'View');

/**
 * Time%achineHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class TimeMachineHelperTest extends CakeTestCase {

	private $__timezones = array('Europe/London', 'Europe/Brussels', 'UTC', 'America/Denver', 'America/Caracas', 'Asia/Kathmandu');
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		$controller = null;
		$View = new View($controller);
		$this->TimeMachine = new TimeMachineHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TimeMachine);
	}

/**
 * testToQuarter method
 *
 * @return void
 */
	public function testToQuarter() {
		$result = $this->TimeMachine->toQuarter('2007-12-25');
		$this->assertEqual($result, 4);

		$result = $this->TimeMachine->toQuarter('2007-9-25');
		$this->assertEqual($result, 3);

		$result = $this->TimeMachine->toQuarter('2007-3-25');
		$this->assertEqual($result, 1);

		$result = $this->TimeMachine->toQuarter('2007-3-25', true);
		$this->assertEqual($result, array('2007-01-01', '2007-03-31'));

		$result = $this->TimeMachine->toQuarter('2007-5-25', true);
		$this->assertEqual($result, array('2007-04-01', '2007-06-30'));

		$result = $this->TimeMachine->toQuarter('2007-8-25', true);
		$this->assertEqual($result, array('2007-07-01', '2007-09-30'));

		$result = $this->TimeMachine->toQuarter('2007-12-25', true);
		$this->assertEqual($result, array('2007-10-01', '2007-12-31'));
	}

/**
 * testTimeAgoInWords method
 *
 * @return void
 */
	public function testTimeAgoInWords() {
		$result = $this->TimeMachine->timeAgoInWords('-1 week');
		$this->assertEqual($result, '1 week ago');

		$result = $this->TimeMachine->timeAgoInWords('+1 week');
		$this->assertEqual($result, '1 week');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+4 months +2 weeks +3 days'), array('end' => '8 years'), true);
		$this->assertEqual($result, '4 months, 2 weeks, 3 days');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+4 months +2 weeks +2 days'), array('end' => '8 years'), true);
		$this->assertEqual($result, '4 months, 2 weeks, 2 days');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+4 months +2 weeks +1 day'), array('end' => '8 years'), true);
		$this->assertEqual($result, '4 months, 2 weeks, 1 day');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+3 months +2 weeks +1 day'), array('end' => '8 years'), true);
		$this->assertEqual($result, '3 months, 2 weeks, 1 day');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+3 months +2 weeks'), array('end' => '8 years'), true);
		$this->assertEqual($result, '3 months, 2 weeks');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+3 months +1 week +6 days'), array('end' => '8 years'), true);
		$this->assertEqual($result, '3 months, 1 week, 6 days');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +2 weeks +1 day'), array('end' => '8 years'), true);
		$this->assertEqual($result, '2 months, 2 weeks, 1 day');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +2 weeks'), array('end' => '8 years'), true);
		$this->assertEqual($result, '2 months, 2 weeks');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +1 week +6 days'), array('end' => '8 years'), true);
		$this->assertEqual($result, '2 months, 1 week, 6 days');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+1 month +1 week +6 days'), array('end' => '8 years'), true);
		$this->assertEqual($result, '1 month, 1 week, 6 days');

		for($i = 0; $i < 200; $i ++) {
			$years = mt_rand(0, 3);
			$months = mt_rand(0, 11);
			$weeks = mt_rand(0, 3);
			$days = mt_rand(0, 6);
			$hours = 0;
			$minutes = 0;
			$seconds = 0;
			$relative_date = '';

			if ($years > 0) {
				// years and months and days
				$relative_date .= ($relative_date ? ', -' : '-') . $years . ' year' . ($years > 1 ? 's' : '');
				$relative_date .= $months > 0 ? ($relative_date ? ', -' : '-') . $months . ' month' . ($months > 1 ? 's' : '') : '';
				$relative_date .= $weeks > 0 ? ($relative_date ? ', -' : '-') . $weeks . ' week' . ($weeks > 1 ? 's' : '') : '';
				$relative_date .= $days > 0 ? ($relative_date ? ', -' : '-') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif (abs($months) > 0) {
				// months, weeks and days
				$relative_date .= ($relative_date ? ', -' : '-') . $months . ' month' . ($months > 1 ? 's' : '');
				$relative_date .= $weeks > 0 ? ($relative_date ? ', -' : '-') . $weeks . ' week' . ($weeks > 1 ? 's' : '') : '';
				$relative_date .= $days > 0 ? ($relative_date ? ', -' : '-') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif (abs($weeks) > 0) {
				// weeks and days
				$relative_date .= ($relative_date ? ', -' : '-') . $weeks . ' week' . ($weeks > 1 ? 's' : '');
				$relative_date .= $days > 0 ? ($relative_date ? ', -' : '-') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif (abs($days) > 0) {
				// days and hours
				$relative_date .= ($relative_date ? ', -' : '-') . $days . ' day' . ($days > 1 ? 's' : '');
				$relative_date .= $hours > 0 ? ($relative_date ? ', -' : '-') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
			} elseif (abs($hours) > 0) {
				// hours and minutes
				$relative_date .= ($relative_date ? ', -' : '-') . $hours . ' hour' . ($hours > 1 ? 's' : '');
				$relative_date .= $minutes > 0 ? ($relative_date ? ', -' : '-') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '';
			} elseif (abs($minutes) > 0) {
				// minutes only
				$relative_date .= ($relative_date ? ', -' : '-') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
			} else {
				// seconds only
				$relative_date .= ($relative_date ? ', -' : '-') . $seconds . ' second' . ($seconds != 1 ? 's' : '');
			}

			if (date('j/n/y', strtotime(str_replace(',','',$relative_date))) != '1/1/70') {
				$result = $this->TimeMachine->timeAgoInWords(strtotime(str_replace(',','',$relative_date)), array('end' => '8 years'), true);
				if ($relative_date == '0 seconds') {
					$relative_date = '0 seconds ago';
				}

				$relative_date = str_replace('-', '', $relative_date) . ' ago';
				$this->assertEqual($result, $relative_date);
			}
		}

		for ($i = 0; $i < 200; $i ++) {
			$years = mt_rand(0, 3);
			$months = mt_rand(0, 11);
			$weeks = mt_rand(0, 3);
			$days = mt_rand(0, 6);
			$hours = 0;
			$minutes = 0;
			$seconds = 0;

			$relative_date = '';

			if ($years > 0) {
				// years and months and days
				$relative_date .= ($relative_date ? ', ' : '') . $years . ' year' . ($years > 1 ? 's' : '');
				$relative_date .= $months > 0 ? ($relative_date ? ', ' : '') . $months . ' month' . ($months > 1 ? 's' : '') : '';
				$relative_date .= $weeks > 0 ? ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '') : '';
				$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif (abs($months) > 0) {
				// months, weeks and days
				$relative_date .= ($relative_date ? ', ' : '') . $months . ' month' . ($months > 1 ? 's' : '');
				$relative_date .= $weeks > 0 ? ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '') : '';
				$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif (abs($weeks) > 0) {
				// weeks and days
				$relative_date .= ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '');
				$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif (abs($days) > 0) {
				// days and hours
				$relative_date .= ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '');
				$relative_date .= $hours > 0 ? ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
			} elseif (abs($hours) > 0) {
				// hours and minutes
				$relative_date .= ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '');
				$relative_date .= $minutes > 0 ? ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '';
			} elseif (abs($minutes) > 0) {
				// minutes only
				$relative_date .= ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
			} else {
				// seconds only
				$relative_date .= ($relative_date ? ', ' : '') . $seconds . ' second' . ($seconds != 1 ? 's' : '');
			}

			if (date('j/n/y', strtotime(str_replace(',','',$relative_date))) != '1/1/70') {
				$result = $this->TimeMachine->timeAgoInWords(strtotime(str_replace(',','',$relative_date)), array('end' => '8 years'), true);
				if ($relative_date == '0 seconds') {
					$relative_date = '0 seconds ago';
				}

				$relative_date = str_replace('-', '', $relative_date) . '';
				$this->assertEqual($result, $relative_date);
			}
		}

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-2 years -5 months -2 days'), array('end' => '3 years'), true);
		$this->assertEqual($result, '2 years, 5 months, 2 days ago');

		$result = $this->TimeMachine->timeAgoInWords('2007-9-25');
		$this->assertEqual($result, 'on 25/9/07');

		$result = $this->TimeMachine->timeAgoInWords('2007-9-25', 'Y-m-d');
		$this->assertEqual($result, 'on 2007-09-25');

		$result = $this->TimeMachine->timeAgoInWords('2007-9-25', 'Y-m-d', true);
		$this->assertEqual($result, 'on 2007-09-25');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-2 weeks -2 days'), 'Y-m-d', false);
		$this->assertEqual($result, '2 weeks, 2 days ago');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 weeks +2 days'), 'Y-m-d', true);
		$this->assertPattern('/^2 weeks, [1|2] day(s)?$/', $result);

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +2 days'), array('end' => '1 month'));
		$this->assertEqual($result, 'on ' . date('j/n/y', strtotime('+2 months +2 days')));

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +2 days'), array('end' => '3 month'));
		$this->assertPattern('/2 months/', $result);

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +12 days'), array('end' => '3 month'));
		$this->assertPattern('/2 months, 1 week/', $result);

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+3 months +5 days'), array('end' => '4 month'));
		$this->assertEqual($result, '3 months, 5 days');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-2 months -2 days'), array('end' => '3 month'));
		$this->assertEqual($result, '2 months, 2 days ago');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-2 months -2 days'), array('end' => '3 month'));
		$this->assertEqual($result, '2 months, 2 days ago');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +2 days'), array('end' => '3 month'));
		$this->assertPattern('/2 months/', $result);

		$result = $this->TimeMachine->timeAgoInWords(strtotime('+2 months +2 days'), array('end' => '1 month', 'format' => 'Y-m-d'));
		$this->assertEqual($result, 'on ' . date('Y-m-d', strtotime('+2 months +2 days')));

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-2 months -2 days'), array('end' => '1 month', 'format' => 'Y-m-d'));
		$this->assertEqual($result, 'on ' . date('Y-m-d', strtotime('-2 months -2 days')));

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-13 months -5 days'), array('end' => '2 years'));
		$this->assertEqual($result, '1 year, 1 month, 5 days ago');

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-2 hours'));
		$expected = '2 hours ago';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-12 minutes'));
		$expected = '12 minutes ago';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->timeAgoInWords(strtotime('-12 seconds'));
		$expected = '12 seconds ago';
		$this->assertEqual($expected, $result);

		$time = strtotime('-3 years -12 months');
		$result = $this->TimeMachine->timeAgoInWords($time);
		$expected = 'on ' . date('j/n/y', $time);
		$this->assertEqual($expected, $result);

		$date = new DateTime('-2 hours');
		$datePast = new DateTime('1995-01-10 10:00:00');
		foreach($this->__timezones as $timezone) {
			$result = $this->TimeMachine->timeAgoInWords($date, array('timezone' => $timezone));
			$this->assertEqual('2 hours ago', $result);

			$result = $this->TimeMachine->timeAgoInWords($datePast, array('format' => 'Y-m-d H:i:s',  'timezone' => $timezone));
			$expectedDate = clone $datePast;
			$expectedDate->setTimezone(new DateTimeZone($timezone));
			$this->assertEqual('on ' . $expectedDate->format('Y-m-d H:i:s'), $result);
		}
	}

/**
 * testNice method
 *
 * @return void
 */
	public function testNice() {
		$time = time() + 2 * DAY;
		$this->assertEqual(date('D, M jS Y, H:i', $time), $this->TimeMachine->nice($time));

		$time = time() - 2 * DAY;
		$this->assertEqual(date('D, M jS Y, H:i', $time), $this->TimeMachine->nice($time));

		$time = time();
		$this->assertEqual(date('D, M jS Y, H:i', $time), $this->TimeMachine->nice($time));

		$time = 0;
		$this->assertEqual(date('D, M jS Y, H:i', time()), $this->TimeMachine->nice($time));

		$time = null;
		$this->assertEqual(date('D, M jS Y, H:i', time()), $this->TimeMachine->nice($time));

		$time = time();
		$this->assertEqual(date('D', $time), $this->TimeMachine->nice($time, null, '%a'));
		$this->assertEqual(date('M d, Y', $time), $this->TimeMachine->nice($time, null, '%b %d, %Y'));

		$this->TimeMachine->niceFormat = '%Y-%d-%m';
		$this->assertEqual(date('Y-d-m', $time), $this->TimeMachine->nice($time));
	}

/**
 * testNiceShort method
 *
 * @return void
 */
	public function testNiceShort() {
		$time = time() + 2 * DAY;
		if (date('Y', $time) == date('Y')) {
			$this->assertEqual(date('M jS, H:i', $time), $this->TimeMachine->niceShort($time));
		} else {
			$this->assertEqual(date('M jS Y, H:i', $time), $this->TimeMachine->niceShort($time));
		}

		$time = time();
		$this->assertEqual('Today, ' . date('H:i', $time), $this->TimeMachine->niceShort($time));

		$time = time() - DAY;
		$this->assertEqual('Yesterday, ' . date('H:i', $time), $this->TimeMachine->niceShort($time));
	}

/**
 * testDaysAsSql method
 *
 * @return void
 */
	public function testDaysAsSql() {
		$begin = time();
		$end = time() + DAY;
		$field = 'my_field';
		$expected = '(my_field >= \''.date('Y-m-d', $begin).' 00:00:00\') AND (my_field <= \''.date('Y-m-d', $end).' 23:59:59\')';
		$this->assertEqual($expected, $this->TimeMachine->daysAsSql($begin, $end, $field));
	}

/**
 * testDayAsSql method
 *
 * @return void
 */
	public function testDayAsSql() {
		$time = time();
		$field = 'my_field';
		$expected = '(my_field >= \''.date('Y-m-d', $time).' 00:00:00\') AND (my_field <= \''.date('Y-m-d', $time).' 23:59:59\')';
		$this->assertEqual($expected, $this->TimeMachine->dayAsSql($time, $field));
	}

/**
 * testToUnix method
 *
 * @return void
 */
	public function testToUnix() {
		$this->assertEqual(time(), $this->TimeMachine->toUnix(time()));
		$this->assertEqual(strtotime('+1 day'), $this->TimeMachine->toUnix('+1 day'));
		$this->assertEqual(strtotime('+0 days'), $this->TimeMachine->toUnix('+0 days'));
		$this->assertEqual(strtotime('-1 days'), $this->TimeMachine->toUnix('-1 days'));
		$this->assertEqual(false, $this->TimeMachine->toUnix(''));
		$this->assertEqual(false, $this->TimeMachine->toUnix(null));
	}

/**
 * testToAtom method
 *
 * @return void
 */
	public function testToAtom() {
		$this->assertEqual(date('Y-m-d\TH:i:s\Z'), $this->TimeMachine->toAtom(time()));


		foreach ($this->__timezones as $timezone) {
			$yourTime = new DateTime('now', new DateTimeZone($timezone));
			$this->assertEqual($yourTime->format('Y-m-d\TH:i:s\Z'), $this->TimeMachine->toAtom(time(), $timezone));	
		}
	}

/**
 * testToRss method
 *
 * @return void
 */
	public function testToRss() {
		$this->assertEqual(date_create('now')->format('r'), $this->TimeMachine->toRss(time()));

		foreach ($this->__timezones as $timezone) {
			$yourTime = new DateTime('now', new DateTimeZone($timezone));
			$this->assertEqual($yourTime->format('r'), $this->TimeMachine->toRss(time(), $timezone));	
		}
	}

/**
 * testFormat method
 *
 * @return void
 */
	public function testFormat() {
		$format = 'D-M-Y';
		$arr = array(time(), strtotime('+1 days'), strtotime('+1 days'), strtotime('+0 days'));
		foreach ($arr as $val) {
			$this->assertEqual(date($format, $val), $this->TimeMachine->format($format, $val));
		}

		$result = $this->TimeMachine->format('Y-m-d', null, 'never');
		$this->assertEqual($result, 'never');
	}

/**
 * testOfUtc method
 *
 * @return void
 */
	public function testUtc() {
		$date = new DateTime('2011-09-06 10:00:00', new DateTimeZone('UTC'));
		$expected = $date->format('U');
		$date->setTimezone(new DateTimeZone(date_default_timezone_get()));
		$this->assertEqual($expected, $this->TimeMachine->utc($date->format('U')));
		$utc = new DateTime('now', new DateTimeZone('UTC'));
		$this->assertEqual($utc->format('U'), $this->TimeMachine->utc(null));
	}

/**
 * testIsToday method
 *
 * @return void
 */
	public function testIsToday() {
		$result = $this->TimeMachine->isToday('+1 day');
		$this->assertFalse($result);
		$result = $this->TimeMachine->isToday('+1 days');
		$this->assertFalse($result);
		$result = $this->TimeMachine->isToday('+0 day');
		$this->assertTrue($result);
		$result = $this->TimeMachine->isToday('-1 day');
		$this->assertFalse($result);

		$date = new DateTime('01:00:00', new DateTimeZone('UTC'));
		$this->assertFalse($this->TimeMachine->isToday($date, 'America/New_York'));
	}

/**
 * testIsThisWeek method
 *
 * @return void
 */
	public function testIsThisWeek() {
		// A map of days which goes from -1 day of week to +1 day of week
		$map = array(
			'Mon' => array(-1, 7), 'Tue' => array(-2, 6), 'Wed' => array(-3, 5),
			'Thu' => array(-4, 4), 'Fri' => array(-5, 3), 'Sat' => array(-6, 2),
			'Sun' => array(-7, 1)
		);
		$days = $map[date('D')];

		for ($day = $days[0] + 1; $day < $days[1]; $day++) {
			$this->assertTrue($this->TimeMachine->isThisWeek(($day > 0 ? '+' : '') . $day . ' days'));
		}
		$this->assertFalse($this->TimeMachine->isThisWeek($days[0] . ' days'));
		$this->assertFalse($this->TimeMachine->isThisWeek('+' . $days[1] . ' days'));
	}

/**
 * testIsThisMonth method
 *
 * @return void
 */
	public function testIsThisMonth() {
		$result = $this->TimeMachine->isThisMonth('+0 day');
		$this->assertTrue($result);
		$result = $this->TimeMachine->isThisMonth($time = mktime(0, 0, 0, date('m'), mt_rand(1, 28), date('Y')));
		$this->assertTrue($result);
		$result = $this->TimeMachine->isThisMonth(mktime(0, 0, 0, date('m'), mt_rand(1, 28), date('Y') - mt_rand(1, 12)));
		$this->assertFalse($result);
		$result = $this->TimeMachine->isThisMonth(mktime(0, 0, 0, date('m'), mt_rand(1, 28), date('Y') + mt_rand(1, 12)));
		$this->assertFalse($result);

	}

/**
 * testIsThisYear method
 *
 * @return void
 */
	public function testIsThisYear() {
		$result = $this->TimeMachine->isThisYear('+0 day');
		$this->assertTrue($result);
		$result = $this->TimeMachine->isThisYear(mktime(0, 0, 0, mt_rand(1, 12), mt_rand(1, 28), date('Y')));
		$this->assertTrue($result);
	}
	/**
 * testWasYesterday method
 *
 * @return void
 */
	public function testWasYesterday() {
		$result = $this->TimeMachine->wasYesterday('+1 day');
		$this->assertFalse($result);
		$result = $this->TimeMachine->wasYesterday('+1 days');
		$this->assertFalse($result);
		$result = $this->TimeMachine->wasYesterday('+0 day');
		$this->assertFalse($result);
		$result = $this->TimeMachine->wasYesterday('-1 day');
		$this->assertTrue($result);
		$result = $this->TimeMachine->wasYesterday('-1 days');
		$this->assertTrue($result);
		$result = $this->TimeMachine->wasYesterday('-2 days');
		$this->assertFalse($result);
	}
	/**
 * testIsTomorrow method
 *
 * @return void
 */
	public function testIsTomorrow() {
		$result = $this->TimeMachine->isTomorrow('+1 day');
		$this->assertTrue($result);
		$result = $this->TimeMachine->isTomorrow('+1 days');
		$this->assertTrue($result);
		$result = $this->TimeMachine->isTomorrow('+0 day');
		$this->assertFalse($result);
		$result = $this->TimeMachine->isTomorrow('-1 day');
		$this->assertFalse($result);
	}

/**
 * testWasWithinLast method
 *
 * @return void
 */
	public function testWasWithinLast() {
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 day', '-1 day'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 week', '-1 week'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 year', '-1 year'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 second', '-1 second'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 minute', '-1 minute'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 year', '-1 year'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 month', '-1 month'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 day', '-1 day'));

		$this->assertTrue($this->TimeMachine->wasWithinLast('1 week', '-1 day'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('2 week', '-1 week'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 second', '-1 year'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('10 minutes', '-1 second'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('23 minutes', '-1 minute'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('0 year', '-1 year'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('13 month', '-1 month'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('2 days', '-1 day'));

		$this->assertFalse($this->TimeMachine->wasWithinLast('1 week', '-2 weeks'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 second', '-2 seconds'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 day', '-2 days'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 hour', '-2 hours'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 month', '-2 months'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 year', '-2 years'));

		$this->assertFalse($this->TimeMachine->wasWithinLast('1 day', '-2 weeks'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('1 day', '-2 days'));
		$this->assertFalse($this->TimeMachine->wasWithinLast('0 days', '-2 days'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 hour', '-20 seconds'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1 year', '-60 minutes -30 seconds'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('3 years', '-2 months'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('5 months', '-4 months'));

		$this->assertTrue($this->TimeMachine->wasWithinLast('5 ', '-3 days'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1   ', '-1 hour'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1   ', '-1 minute'));
		$this->assertTrue($this->TimeMachine->wasWithinLast('1   ', '-23 hours -59 minutes -59 seconds'));
	}

/**
 * test fromString()
 *
 * @return void
 */
	public function testFromString() {
		$result = $this->TimeMachine->fromString('');
		$this->assertFalse($result);

		$result = $this->TimeMachine->fromString(0, 0);
		$this->assertFalse($result);

		$result = $this->TimeMachine->fromString('+1 hour');
		$expected = new DateTime('+1 hour');
		$this->assertEqual($expected, $result);
	}

/**
 * test converting time specifiers using a time definition localfe file
 *
 * @return void
 */
	public function testConvertSpecifiers() {
		App::build(array(
			'locales' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Locale' . DS)
		), true);
		Configure::write('Config.language', 'time_test');
		$time = strtotime('Thu Jan 14 11:43:39 2010');

		$result = $this->TimeMachine->convertSpecifiers('%a', $time);
		$expected = 'jue';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%A', $time);
		$expected = 'jueves';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%c', $time);
		$expected = 'jue %d ene %Y %H:%M:%S %Z';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%C', $time);
		$expected = '20';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%D', $time);
		$expected = '%m/%d/%y';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%b', $time);
		$expected = 'ene';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%h', $time);
		$expected = 'ene';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%B', $time);
		$expected = 'enero';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%n', $time);
		$expected = "\n";
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%n', $time);
		$expected = "\n";
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%p', $time);
		$expected = 'AM';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%P', $time);
		$expected = 'am';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%r', $time);
		$expected = '%I:%M:%S AM';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%R', $time);
		$expected = '11:43';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%t', $time);
		$expected = "\t";
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%T', $time);
		$expected = '%H:%M:%S';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%u', $time);
		$expected = 4;
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%x', $time);
		$expected = '%d/%m/%y';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%X', $time);
		$expected = '%H:%M:%S';
		$this->assertEqual($expected, $result);
	}

/**
 * test convert %e on windows.
 *
 * @return void
 */
	public function testConvertPercentE() {
		$this->skipIf(DIRECTORY_SEPARATOR !== '\\', 'Cannot run windows tests on non-windows OS.');

		$time = strtotime('Thu Jan 14 11:43:39 2010');
		$result = $this->TimeMachine->convertSpecifiers('%e', $time);
		$expected = '14';
		$this->assertEqual($expected, $result);

		$result = $this->TimeMachine->convertSpecifiers('%e', strtotime('2011-01-01'));
		$expected = ' 1';
		$this->assertEqual($expected, $result);
	}

/**
 * test formatting dates taking in account preferred i18n locale file
 *
 * @return void
 */
	public function testI18nFormat() {
		App::build(array(
			'locales' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Locale' . DS)
		), true);
		Configure::write('Config.language', 'time_test');
		$timeString = strtotime('Thu Jan 14 13:59:28 2010');
		$timeObject = new DateTime('Thu Jan 14 13:59:28 2010');

		foreach(array($timeString, $timeObject) as $time) {
			$result = $this->TimeMachine->i18nFormat($time);
			$expected = '14/01/10';
			$this->assertEqual($expected, $result);

			$result = $this->TimeMachine->i18nFormat($time, '%c');
			$expected = 'jue 14 ene 2010 13:59:28 ' . strftime('%Z', is_object($time) ? $time->format('U') : $time);
			$this->assertEqual($expected, $result);

			$result = $this->TimeMachine->i18nFormat($time, 'Time is %r, and date is %x');
			$expected = 'Time is 01:59:28 PM, and date is 14/01/10';
			$this->assertEqual($expected, $result);

			$result = $this->TimeMachine->i18nFormat('invalid date', '%x', 'Date invalid');
			$expected = 'Date invalid';
			$this->assertEqual($expected, $result);
		}
	}

/**
 * test new format() syntax which inverts first and secod parameters
 *
 * @return void
 */
	public function testFormatNewSyntax() {
		$time = time();
		$this->assertEqual($this->TimeMachine->format($time), $this->TimeMachine->i18nFormat($time));
		$this->assertEqual($this->TimeMachine->format($time, '%c'), $this->TimeMachine->i18nFormat($time, '%c'));
	}

/**
 * test serverOffset() which returns the server offset from UTC in seconds
 *
 * @return void
 */
	public function testServerOffset() {
		$result = $this->TimeMachine->serverOffset();
		$serverTimezone = new DateTimeZone(date_default_timezone_get());
		$expected = $serverTimezone->getOffset(new DateTime('now'));
		$this->assertEqual($expected, $result);
	}
	
/**
 * test if the value Config.timezone is currently used in the helper
 *
 * @return void
 */
	public function testConfiguredTimezone() {
		Configure::write('Config.timezone', 'Europe/Brussels');
		$date = new DateTime('2010-10-10 10:10:10', new DateTimeZone('UTC'));
		$result = $this->TimeMachine->fromString($date);
		$expected = 'Europe/Brussels';
		$this->assertEqual($expected, $result->getTimeZone()->getName());
		$expected = '2010-10-10 12:10:10';
		$this->assertEqual($expected, $result->format('Y-m-d H:i:s'));
	}
}
