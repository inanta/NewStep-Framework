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

/**
 *Manage and handle cookie
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Cookie extends SingletonObject {
	const EXPIRE_ONE_HOUR = 3600;
	const EXPIRE_ONE_DAY = 86400;
	const EXPIRE_ONE_WEEK = 604800;
	
	function __construct() {
		$this->createProperties(array('Expire' => time() + self::EXPIRE_ONE_HOUR, 'Path' => '/', 'Domain' => '', 'Secure' => false, 'HTTPOnly' => false));
	}
	
	function get($name) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
	}
	
	function set($name, $value = '') {
		setcookie($name, $value, $this->Expire, $this->Path, $this->Domain, $this->Secure, $this->HTTPOnly);
	}
	
	function flush($name) {
		setcookie($name, $value, time() - self::EXPIRE_ONE_HOUR);
		return $this->get($name);
	}
 
	function delete($name) {
		if(isset($_COOKIE[$name])) setcookie($name, '', time() - self::EXPIRE_ONE_HOUR, $this->Path, $this->Domain, $this->Secure, $this->HTTPOnly);
	}
 
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>
