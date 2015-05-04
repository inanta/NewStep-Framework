<?php
/*
	Copyright (C) 2008 - 2012 Inanta Martsanto
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

use NS\Core\Config;

/**
 *Handle function to manipulate number
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Number extends Object {
	const FORMAT_MONEY = 1;
	const FORMAT_MONEY_WITH_CODE = 2;
	const FORMAT_MONEY_WITH_SYMBOL = 3;
	const FORMAT_DECIMAL = 4;
	const FORMAT_SHORTENED_NOTATION = 5;

	const CONVERT_TO_ROMAN = 1;
	const CONVERT_FROM_ROMAN = 2;
	const CONVERT_TO_WORDS = 3;

	private static $_locale = null;

	static function format($value, $format) {
		self::loadLocale();

		switch($format) {
			case self::FORMAT_MONEY:
			case self::FORMAT_MONEY_WITH_CODE:
			case self::FORMAT_MONEY_WITH_SYMBOL:
				return (($format == self::FORMAT_MONEY ? '' : ($format == self::FORMAT_MONEY_WITH_SYMBOL ? self::$_locale['CurrencySymbol'] . ' ' : self::$_locale['CurrencyCode'] . ' ')) . number_format($value, self::$_locale['MoneyFractionDigit'], self::$_locale['MoneyDecimalPoint'], self::$_locale['MoneyThousandSeparator']));
			case self::FORMAT_DECIMAL:
				return (floatval($value));
			case self::FORMAT_SHORTENED_NOTATION:
				$negative = false;

				if($value < 0) {
					$value = abs($value);
					$negative = true;
				}

				if ($value >= 1000000000) {
					return (($negative ? '-' : '') . (floatval($value / 1000000000) . 'G'));
				} elseif ($value >= 1000000) {
					return (($negative ? '-' : '') . (floatval($value / 1000000) . 'M'));
				} elseif ($value >= 1000) {
					return (($negative ? '-' : '') . (floatval($value / 1000) . 'K'));
				}

				return $value;
		}
	}

	static function convert($value, $convert) {
		switch($convert) {
			case self::CONVERT_FROM_ROMAN:
				return self::fromRoman($value);
			case self::CONVERT_TO_ROMAN:
				return self::toRoman($value);
			case self::CONVERT_TO_WORDS:
				return self::toWords($value);
		}
	}

	/**
	*Convert number from Roman numerals
	*
	*/
	static function fromRoman($number) {
		$result = 0;
		$romans = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90,
			'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
		);

		if(!preg_match('/^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/', $number)) return $result;

		foreach ($romans as $key => $value) {
		    while (strpos($number, $key) === 0) {
			    $result += $value;
			    $number = substr($number, strlen($key));
		    }
		}

		return $result;
	}

	/**
	*Convert number to Roman numerals
	*
	*/
	static function toRoman($number) {
		$n = $number;
		$result = '';
		$romans = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90,
			'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
		);

		foreach ($romans as $roman => $number) {
		    $matches = $n / $number;
		    $result .= str_repeat($roman, $matches);
		    $n = $n % $number;
		}
	    
		return $result;
	}

	/**
	*Convert number to words
	*
	*/
	static function toWords($value) {
		// Based on: http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/

		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' comma ';
		$dictionary  = self::$_locale['NumberDictionary'];
    
		if (!is_numeric($value)) {
			return false;
		}
    
		if (($value >= 0 && (int) $value < 0) || (int) $value < 0 - PHP_INT_MAX) {
			throw new Exception('Error');
			return false;
		}
    
		if ($value < 0) {
			return $negative . self::toWords(abs($value));
		}
    
		$string = $fraction = null;
    
		if (strpos($value, '.') !== false) {
			list($value, $fraction) = explode('.', $value);
		}
    
		switch (true) {
			case $value < 21:
				$string = $dictionary[$value];
				break;
			case $value < 100:
				$tens   = ((int) ($value / 10)) * 10;
				$units  = $value % 10;
				$string = $dictionary[$tens];
				if ($units) {
				    $string .= $hyphen . $dictionary[$units];
				}
				break;
			case $value < 1000:
				$hundreds  = $value / 100;
				$remainder = $value % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
				    $string .= $conjunction . self::toWords($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($value, 1000)));
				$numBaseUnits = (int) ($value / $baseUnit);
				$remainder = $value % $baseUnit;
				$string = self::toWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
				    $string .= $remainder < 100 ? $conjunction : $separator;
				    $string .= self::toWords($remainder);
				}
				break;
		}
    
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $value) {
			    $words[] = $dictionary[$value];
			}
			$string .= implode(' ', $words);
		}
    
		return $string;
	}

	public static function assignLocale($locale) {
		self::$_locale = $locale;
	}
	
	private static function loadLocale() {
		if(self::$_locale == null) {
			require(NS_SYSTEM_PATH . '/' . Config::getInstance()->ConfigFolder . '/Locale.inc.php');
			self::$_locale = $Locale;
		}
	}
}
?>