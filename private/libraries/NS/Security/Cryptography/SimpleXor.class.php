<?php
/*
	Copyright (C) 2008 - 2016 Inanta Martsanto
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

namespace NS\Security\Cryptography;

/**
 *Class encrype / decrype string using simple XOR
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class SimpleXor {
	private $_key = 'NS';
	
	function __construct($key = null) {
		if(!empty($key)) 
			$this->_key = $key;
	}

	function encrypt($string) {
		return base64_encode($this->_simpleXor($string, $this->_key));
	}

	function decrypt($string) {
		return $this->_simpleXor(base64_decode($string), $this->_key);
	}

	private function _simpleXor($string, $key) {
		$key_list = array();	// Initialise key array
		$output = '';	// Initialise out variable

		// Convert $key into array of ASCII values
		for($i = 0; $i < strlen ($key); $i++) {
			$key_list[$i] = ord (substr ($key, $i, 1));
		}

		// Step through string a character at a time
		for($i = 0; $i < strlen ($string); $i++) {
			// Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
			// % is MOD (modulus), ^ is XOR
			$output .= chr(ord(substr($string, $i, 1)) ^ ($key_list[$i % strlen ($key)]));
		}

		// Return the result
		return $output;
	}	
}
?>