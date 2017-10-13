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

namespace NS\Net\Http;

use NS\SingletonObject;
use NS\Core\Session;

/**
 *Handle client request
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property ClientRequest $Post Get value from $_POST variable
 *@property ClientRequest $Get Get value from $_GET variable
 *@property ClientRequest $QueryString Get value from $_GET variable
 */
class ClientRequest extends SingletonObject {
	private $_input;

	/**
	*Initialize client request
	*
	*/
	function __construct() {
		$this->_input =& $_REQUEST;
	}

	function __get($k) {
		if($k == 'Post') {
			$this->_input =& $_POST;
			return $this;
		} else if($k == 'Get' || $k == 'QueryString') {
			$this->_input =& $_GET;
			return $this;
		}

		return parent::__get($k);
	}

	/**
	*Retrieve user request data from $_GET, $_POST or $_REQUEST
	*
	*@param mixed $keys Value key that that will be retrieved, it can be string or associative array to get multiple data
	*/
	function value($keys = null) {
		if(is_array($keys)) {
			foreach($keys as $key => $value) {
				$value = trim($value);

				$keys[$value] = $this->_input[$value];
				unset($keys[$key]);
			}
		} else if($keys == null) {
			$keys = $this->_input;
		} else {
			$keys = trim($keys);
			$keys = (isset($this->_input[$keys]) ? $this->_input[$keys] : false);
		}

		return $keys;
	}

	/**
	*Validate  user request data from $_GET, $_POST or $_REQUEST
	*
	*@param mixed $keys Value key that that will be validated, it can be string or associative array to get multiple data
	*@param array $validators Array of valid Validator object
	*@param boolean $is_returned Determine if validated data is returned or not  
	*/
	function validate($keys, $validators, $is_returned = false) {
		$single = false;
		if(!is_array($keys)) { $keys = array($keys); $single = true; }
		if(!is_array($validators)) $validators = array($validators);

		$return;
		foreach($keys as $key) {
			foreach($validators as $validator) {
				if(!$validator->validate($this->_input[$key])) return false;

				if($is_returned) $return[$key] = $this->_input[$key];
			}
		}

		return ($is_returned ? ($single ? current($return) : $return) : true);
	}

	/**
	*Clone data from $_GET, $_POST or $_REQUEST to session ($_SESSION)
	*
	*/
	function saveToSession() {
		$session = Session::getInstance();

		foreach($this->_input as $key => $param) { 
			$session->set($key, $param);
		}
	}

	/**
	*Create or retrieve object instance
	*
	*@return ClientRequest
	*/
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>
