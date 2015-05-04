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
use NS\Core\Config;

/**
 *Detect client's internet browser
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $BrowserName Client browser name
 *@property string $Device Client device name (if available)
 *@property string $IsMobile Determine if client access using mobile device
 *@property string $OS Client operating systen
 *@property string $Version Client browser version
 */
class ClientBrowser extends SingletonObject {	
	function __construct() {
		$this->createProperties(array(
			'BrowserName' => 'Unknown',
			'Device' => 'Unknown',
			'IsMobile' => false,
			'OS' => 'Unknown',
			'Version' => 'Unknown',
		));
		$this->detect();
	}
 
	/**
	*Create or retrieve object instance
	*
	*/
	static function getInstance() { return self::createInstance(__CLASS__); }

	private function detect() {
		require(NS_SYSTEM_PATH . '/' . Config::getInstance()->ConfigFolder . '/UserAgent.inc.php');

		foreach($UserAgent['OS'] as $k => $v) {
			if(preg_match('/' . $k . '/i', $_SERVER['HTTP_USER_AGENT'])) {
				$this->OS = $v;
				if(isset($UserAgent['Device'][$v])) {
					foreach($UserAgent['Device'][$v] as $dk => $d) {
						if(preg_match('/' . $dk . '/i', $_SERVER['HTTP_USER_AGENT'])) { $this->Device = $d; break; }
			    		}
				}

				break;
			}
		}

		foreach($UserAgent['Browser'] as $k => $v) {
			if(preg_match('/' . $k . '/i', $_SERVER['HTTP_USER_AGENT'])){
				$this->BrowserName = $v[0];
				$this->Version = end(explode($v[1][1], current(explode($v[1][0], stristr($_SERVER['HTTP_USER_AGENT'], $v[1][2])))));
				break;
			}
		}
	}
}
?>