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

namespace NS\Utility;

use NS\BaseObject;

/**
 *Class to manipulate string
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class StringUtility extends BaseObject {
	/**
	*Join string in all given arguments
	*
	*@param string $str,... Input string that will be joined
	*@return string Returns a string containing all joined string
	*/
	static function concat() {
		return @implode('', func_get_args());
	}

	/**
	*Determine if input string is contains searched string
	*
	*@param string $str Input string
	*@param string $search String that will be search in input string
	*@return boolean Return true if input string contains searched string
	*/
	static function contains($str, $search) {
		if(!is_array($search)) $search = array($search);

		foreach($search as $item) {
			if(@preg_match('/' . $item . '/', $str) == 1) {
				return true;
			}
		}

		return false;
	}

	/**
	*Determine if input string is ends with searched string
	*
	*@param string $str Input string
	*@param string $search String that will be search in input string
	*@return boolean Return true if input string ends with searched string
	*/
	static function endsWith($str, $search) {
		if(!is_array($search)) $search = array($search);

		foreach($search as $item) {
			if(@preg_match('/' . $item . '$/', $str) == 1) {
				return true;
			}
		}

		return false;
	}

	/**
	*Return first index of searched character in input string
	*
	*@param string $str Input string
	*@param string $search String being searched for
	*@return integer Returns an integer index of searched string in input string
	*/
	static function indexOf($str, $search) {
		return @strpos($str, $search);
	}

	/**
	*Invert character case
	*
	*@param string $str Input string
	*@return string Return string that characters case has been inverted  
	*/
	static function invertCase($str) {
		return @strtolower($str) ^ @strtoupper($str) ^ $str;
	}

	/**
	*Join array value to one string
	*
	*@param array $strs Input array
	*@param string $glue String that will be inserted between every joinded array element
	*@return string Returns a string containing a string representation of all the array elements in the same order, with the glue string between each element
	*/
	static function join($strs, $glue = ' ') {
		return @implode($glue, $strs);
	}

	/**
	*Limit characters in string
	*
	*@param string $str Input string
	*@param integer $limit Number of characters
	*@param string $end_string String that will be added at the end of input string if input string length more that allowed limit
	*@return string Return truncated string
	*/
	static function limitCharacter($str, $limit, $end_string = '...') {
		return (@strlen($str) > $limit ? @substr($str, 0, $limit) . $end_string : $str);
	}

	/**
	*Limit characters in string
	*
	*@param string $str Input string
	*@param integer $limit Number of characters
	*@param string $end_string String that will be added at the end of input string if input string length more that allowed limit
	*@return string Return truncated string
	*/
	static function limitWord($str, $limit, $end_string = '...') {
		if(@count($word = @explode(' ', $str, $limit + 1)) > $limit) {
			$str = @implode(' ', @array_slice($word, 0, $limit)) . $end_string;
		}

		return $str;
	}

	/**
	*Pad input string to cetain length with another string in front of input string
	*
	*@param string $str Input string
	*@param string $length Length of pad characters that will be added in front of input string
	*@param string $pad Pad characters
	*@return string Returns a string with pad characters in front of input string
	*/
	static function padLeft($str, $length, $pad = ' ') {
		return str_pad($str, $length, $pad, STR_PAD_LEFT);
	}

	/**
	*Pad input string to cetain length with another string behind input string
	*
	*@param string $str Input string
	*@param string $length Length of pad characters that will be added behind input string
	*@param string $pad Pad characters
	*@return string Returns a string with pad characters behind input string
	*/
	static function padRight($str, $length, $pad = ' ') {
		return str_pad($str, $length, $pad, STR_PAD_RIGHT);
	}

	/**
	*Join string in all given arguments
	*
	*@param string $search String being searched for
	*@param string $replace String that will be replacement
	*@param string $str Input string
	*@return string Returns a string with replaced values
	*/
	static function replace($search, $replace, $str) {
		return @str_replace($search, $replace, $str);
	}

	/**
	*Determine if input string is starts with searched string
	*
	*@param string $str Input string
	*@param string $search String that will be search in input string
	*@return boolean Return true if input string starts with searched string
	*/
	static function startsWith($str, $search) {
		if(!is_array($search)) $search = array($search);

		foreach($search as $item) {
			if(@preg_match('/^' . $item . '/', $str) == 1) {
				return true;
			}
		}

		return false;
	}

	/**
	*Convert all characters in string to lower case
	*
	*@param string $str Input string
	*@return string Return input string in lower case
	*/
	static function toLowerCase($str) {
		return @strtolower($str);
	}

	/**
	*Convert first character in every word to upper case
	*
	*@param string $str Input string
	*@return string Return input string that replaced first character in every word to upper case
	*/
	static function toProperCase($str) {
		return @ucwords($str);
	}

	/**
	*Replace string so it can be used as URI
	*
	*@param string $str Input string
	*@param integer $replace String that will be replaced in returned string
	*@param string $delimiter Character that will be used to replace whitespace characters
	*@return string Return string that can be used as URI
	*/
	static function toSafeURI($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = @str_replace((array)$replace, ' ', $str);
		}

	       $clean = @iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	       $clean = @preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	       $clean = @strtolower(trim($clean, '-'));
	       $clean = @preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	       return $clean;
	}

	/**
	*Convert first character in string upper case
	*
	*@param string $str Input string
	*@return string Return input string that replaced first character in input string to upper case
	*/
	static function toSentenceCase($str) {
		return @ucfirst($str);
	}

	/**
	*Convert first character in every word to upper case
	*
	*@param string $str Input string
	*@return string Return input string that replaced first character in every word to upper case
	*/
	static function toTitleCase($str) {
		return @ucwords($str);
	}

	/**
	*Convert all characters in string to upper case
	*
	*@param string $str Input string
	*@return string Return input string in upper case
	*/
	static function toUpperCase($str) {
		return @strtoupper($str);
	}
}
?>