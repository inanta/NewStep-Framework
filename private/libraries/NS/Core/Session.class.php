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

namespace NS\Core;

use NS\SingletonObject;

/**
 *Session handler
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Session extends SingletonObject {
	/**
	 *Initialize session
	 *
	 */
	function __construct() {
		if(Config::getInstance()->Application->UseDatabaseBasedSession ? Config::getInstance()->Application->SessionBasedDatabaseConenction : false) DatabaseSessionHandler::getInstance();
		if(!isset($_SESSION)) session_start();

		if(isset($_SESSION['NS'])) {
			foreach($_SESSION['NS'] as $k => $v) {
				if(isset($_SESSION['NS'][$k]['e']) && $_SESSION['NS'][$k]['e'] < time()) {
					unset($_SESSION['NS'][$k]);
					continue;
				}
				
				if(isset($_SESSION['NS'][$k]['u'])) {
					if((is_string($_SESSION['NS'][$k]['u']) && $_SESSION['NS'][$k]['u'] != NS_CURRENT_URL) ||
						(is_array($_SESSION['NS'][$k]['u']) && !in_array(NS_CURRENT_URL, $_SESSION['NS'][$k]['u'])))
					unset($_SESSION['NS'][$k]);
				}
			}
		}
	}

	/**
	 *Set new session data
	 *
	 *@param string $key Key name
	 *@param mixed $val Value that will be saved to session
	 *@param mixed $expire Number of seconds session will be expired
	 *@param mixed $url Specify allowed URL that can access this session data
	 */
	function set($key, $val, $expire = null, $url = null) {
		$_SESSION['NS'][$key]['v'] = $val;

		if($expire !== null) $_SESSION['NS'][$key]['e'] = time() + $expire;
		if($url !== null) $_SESSION['NS'][$key]['u'] = $url;
	}

	/**
	 *Set new session data directly to $_SESSION variable
	 *
	 *@param string $key Key name
	 *@param mixed $val Value that will be saved to session
	 */
	function setRaw($key, $val, $expire = null, $url = null) {
		$_SESSION[$key] = $val;
	}

	/**
	 *Get stored session data
	 *
	 *@param string Key name
	 *@return mixed Return value stored in session
	 */
	function get($key) { return  (isset($_SESSION['NS'][$key]['v']) ? $_SESSION['NS'][$key]['v'] : false); }

	/**
	 *Get stored session data directly from $_SESSION variable
	 *
	 *@param string Key name
	 *@return mixed Return value stored in session
	 */
	function getRaw($key) { return  (isset($_SESSION[$key]) ? $_SESSION[$key] : false); }

	/**
	 *Get and remove stored session data
	 *
	 *@param string Key name
	 *@return mixed Return value stored in session
	 */
	function flush($key) {
		if(!isset($_SESSION['NS'][$key])) return false;

		$ret = $_SESSION['NS'][$key]['v'];
		unset($_SESSION['NS'][$key]);

		return $ret;
	}

	/**
	 *Get and remove stored session data directly from $_SESSION variable
	 *
	 *@param string Key name
	 *@return mixed Return value stored in session
	 */
	function flushRaw($key) {
		if(!isset($_SESSION[$key])) return false;

		$ret = $_SESSION[$key];
		unset($_SESSION[$key]);

		return $ret;
	}

	/**
	 *Create or retrieve object instance
	 *
	 *@return self 
	 */
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>
