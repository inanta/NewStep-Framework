<?php
/*
	Copyright (C) 2008 - 2014 Inanta Martsanto
	Inanta Martsanto (inanta@inationsoft.com)

	This file is part of NewStep Framework.

	NewStep Framework is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	NewStep Framework is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with NewStep Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace NS;

/**
 *Handle date and time manipulation
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property int $Date Represent date in integer
 *@property int $Month Represent month  in integer
 *@property int $Year Represent year in 4 digit number
 *@property int $TwoDigitYear Represent year in 2 digit number
 *@property int $Hour Represent hour in integer using 24 hour format
 *@property int $Minute Represent minute in integer
 *@property int $Second Represent second in ineteger
 *@property int $Timestamp Represent timestamp
 *@property int $IsLeapYear Represent whenever current year is leap year
 */

class DateTime extends Object {
	const ONE_MINUTE = 60;
	const ONE_HOUR = 3600;
	const ONE_DAY = 86400;
	const ONE_WEEK = 604800;
	const ONE_YEAR = 31536000;
	const ONE_LEAP_YEAR = 31622400;

	private $_timestamp;
	private static $_locale = null;

	/**
	 * 
	 * @param integer $date
	 * @param integer $month
	 * @param integer $year
	 * @param integer $hour
	 * @param integer $minute
	 * @param integer $second
	 */
	function __construct($date = null, $month = null, $year = null, $hour = null, $minute = null, $second = null) {
		if($date != null && $month != null && $year != null) {
			$this->_timestamp = mktime($hour, $minute, $second, $month, $date, $year);
		} else {
			$this->_timestamp = time();	
		}

		$this->createProperties(array('Date' => @date('j', $this->_timestamp), 'Month' => @date('n', $this->_timestamp), 'Year' => @date('Y', $this->_timestamp), 'TwoDigitYear' => @date('y', $this->_timestamp),
			'Hour' => @date('h', $this->_timestamp), 'Minute' => @date('i', $this->_timestamp), 'Second' => @date('s', $this->_timestamp),
			'Timestamp' => $this->_timestamp));

		$this->createProperties(array('IsLeapYear' => self::isLeapYear($this->Year)));
	}

	function __get($property) {
		switch($property) {
			case 'DayStartTime':
				return mktime(0, 0, 0, $this->Month, $this->Date, $this->Year);
			case 'DayEndTime':
				return mktime(23, 59, 59, $this->Month, $this->Date, $this->Year);
			case 'MonthStartTime':
				return mktime(0, 0, 0, $this->Month, 1, $this->Year);
			case 'MonthEndTime':
				return (in_array($this->Month, array(1, 3, 5, 7, 8, 10)) ? mktime(23, 59, 59, $this->Month, 31, $this->Year) : (in_array($this->Month, array(4, 6, 9, 11)) ? mktime(23, 59, 59, $this->Month, 30, $this->Year) : (self::isLeapYear($this->Year) ? mktime(23, 59, 59, $this->Month, 29, $this->Year) : mktime(23, 59, 59, $this->Month, 28, $this->Year)))) ;
			case 'YearStartTime':
				return mktime(0, 0, 0, 1, 1, $this->Year);
			case 'YearEndTime':
				return mktime(23, 59, 59, 12, 31, $this->Year);
			default:
				return parent::__get($property);
		}
	}

	function __set($property, $value) {
		if($property == 'Timestamp') { $this->_timestamp = $value; $this->assignDateTimeValue(); }

		parent::__set($property, $value);
	}

	 function __toString() {
		return @date('d/n/Y', $this->_timestamp);
	}

	/**
	 * 
	 * @param integer $day Number of days that will be added
	 */
	function addDays($day) { $this->Timestamp += ($day * self::ONE_DAY); }

	/**
	 * 
	 * @param integer $hour Number of hours that will be added
	 */
	function addHours($hour) { $this->Timestamp += ($hour * self::ONE_HOUR); }

	/**
	 * 
	 * @param integer $minute Number of minutes that will be added
	 */
	function addMinutes($minute) { $this->Timestamp += ($minute * self::ONE_MINUTE); }

	/**
	 * 
	 * @param integer $month Number of months that will be added
	 */
	function addMonths($month) {
		while($month >= 12) {
			$this->addYears(1);
			$month -= 12;
		}

		$timestamp = 0;

		for($i = 0; $i < $month; ++$i) {
			switch(($this->Month + $i) % 12) {
				case 1:
					if($i != 0) $this->Year += 1;
				case 3: case 5: case 7:
				case 8: case 10: case 0:
					$timestamp += (31 * self::ONE_DAY); break;
				case 4: case 6: case 9: case 11:
					$timestamp += (30 * self::ONE_DAY); break;
				case 2:
					if(self::isLeapYear($this->Year)) $timestamp += (29 * self::ONE_DAY);
					else $timestamp += (28 * self::ONE_DAY);
			}
		}

		if($timestamp != 0) $this->Timestamp += $timestamp;
	}

	/**
	 * 
	 * @param integer $sec Number of seconds that will be added
	 */
	function addSeconds($sec) { $this->Timestamp += $sec; }

	/**
	 * 
	 * @param integer $week Number of weeks that will be added
	 */
	function addWeeks($week) { $this->Timestamp += ($week * self::ONE_WEEK); }

	/**
	 * 
	 * @param integer $year Number of years that will be added
	 */
	function addYears($year) {
		$i = 0;
		$timestamp = 0;

		if($this->Month > 2) { ++$i; $year += 1; }

		for($i; $i < $year; ++$i) {
			$timestamp += (!self::isLeapYear($this->Year + $i) ? self::ONE_YEAR : self::ONE_LEAP_YEAR);
		}
		$this->Timestamp += $timestamp;
	}

	/**
	 * 
	 * @param integer $day Number of days that will be subtracted
	 */
	function subtractDays($day) { $this->Timestamp -= ($day * self::ONE_DAY); }

	/**
	 * 
	 * @param integer $hour Number of hours that will be added
	 */
	function subtractHours($hour) { $this->Timestamp -= ($hour * self::ONE_HOUR); }

	/**
	 * 
	 * @param integer $minute Number of minutes that will be added
	 */
	function subtractMinutes($minute) { $this->Timestamp -= ($minute * self::ONE_MINUTE); }

	/**
	 * 
	 * @param integer $month Number of months that will be added
	 */
	function subtractMonths($month) {
		while($month >= 12) {
			$this->subtractYears(1);
			$month -= 12;
		}

		$timestamp = 0;

		for($i = 0; $i < $month; ++$i) {
			switch(($this->Month + $i) % 12) {
				case 1:
					if($i != 0) $this->Year -= 1;
				case 3: case 5: case 7:
				case 8: case 10: case 0:
					$timestamp -= (31 * self::ONE_DAY); break;
				case 4: case 6: case 9: case 11:
					$timestamp -= (30 * self::ONE_DAY); break;
				case 2:
					if(self::isLeapYear($this->Year)) $timestamp -= (29 * self::ONE_DAY);
					else $timestamp -= (28 * self::ONE_DAY);
			}
		}

		if($timestamp != 0) $this->Timestamp += $timestamp;
	}

	/**
	 * 
	 * @param integer $sec Number of seconds that will be added
	 */
	function subtractSeconds($sec) { $this->Timestamp -= $sec; }

	/**
	 * 
	 * @param integer $week Number of weeks that will be added
	 */
	function subtractWeeks($week) { $this->Timestamp -= ($week * self::ONE_WEEK); }

	/**
	 * 
	 * @param integer $year Number of years that will be added
	 */
	function subtractYears($year) {
		$i = 0;
		$timestamp = 0;

		if($this->Month < 2) { ++$i; $year += 1; }

		for($i; $i < $year; ++$i) {
			$timestamp -= (!self::isLeapYear($this->Year - $i) ? self::ONE_YEAR : self::ONE_LEAP_YEAR);
		}

		$this->Timestamp += $timestamp;
	}
	
	private function assignDateTimeValue() {
		$this->Date = @date('j', $this->_timestamp);
		$this->Month = @date('n', $this->_timestamp);
		$this->Year = @date('Y', $this->_timestamp);
		$this->Hour = @date('H', $this->_timestamp);
		$this->Minute = @date('i', $this->_timestamp);
		$this->Second = @date('s', $this->_timestamp);
		$this->IsLeapYear = self::isLeapYear($this->Year);
	}

	static function assignLocale($locale) {
		self::$_locale = $locale;
	}

	/**
	 * 
	 * @param string $date Date string in dd/mm/yyyy format
	 * @return DateTime
	 */
	static function fromString($date) {
		$date_time = explode(' ', $date);

		$date = explode('/', $date_time[0]);
		$time = array();

		if(isset($date_time[1])) {
			$time = explode(':', $date_time[1]);
		}

		if(count($date) < 3) $date = explode('/', date('d/m/Y'));

		switch(count($time)) {
			case 0:
				$time = explode(':', date('H:i:s'));
				break;
			case 1:
				$time = array_merge($time, explode(':', date('i:s')));
				break;
			case 2:
				$time = array_merge($time, array(date('s')));
				break;
		}

		return new DateTime($date[0], $date[1], $date[2], $time[0], $time[1], $time[2]);
	}

	/**
	 * 
	 * @param integer $timestamp Unix timestamp
	 * @return DateTime
	 */
	static function fromTimestamp($timestamp) {
		$dt = new DateTime();
		$dt->Timestamp = $timestamp;

		return $dt; 
	}

	public static function assignLocale($locale) {
		self::$_locale = $locale;
	}

	static function getMonthNames() {
		self::loadLocale();
		return self::$_locale['MonthNames'];
	}

	/**
	 * 
	 * @param integer $year Year that will be checked if it is a leap year
	 * @return boolean
	 */
	static function isLeapYear($year) {
		return ($year % 400 == 0 ? true : ($year % 100 == 0 ? true : ($year % 4 == 0 ? true : false)));	
	}

	private static function loadLocale() {
		if(self::$_locale == null) {
			require(NS_SYSTEM_PATH . '/' . Config::getInstance()->ConfigFolder . '/Locale.inc.php');
			self::$_locale = $Locale;
		}
	}
}
?>