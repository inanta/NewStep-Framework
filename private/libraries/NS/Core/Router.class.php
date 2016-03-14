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

namespace NS\Core;

use NS\SingletonObject;
use NS\Exception\SecurityException;
use NS\Exception\PageNotFoundException;

/**
 *Router to dispatch request from user
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Router extends SingletonObject {
	public $UseAlias = false, $App, $File;

	/**
	 *Initialize router from user request
	 *
	 */
	function initialize(&$cf) {
		if(!preg_match($cf->Application->PermittedURLChar, NS_CURRENT_URL)) {
			throw new SecurityException(array('code' => SecurityException::INVALID_URL));
		}

		$segments = null; $dir = ''; $idx = 0; $class = null;

		if($pos = strpos($_SERVER['PHP_SELF'], 'index.php/')) $segments = explode('/', (substr($_SERVER['PHP_SELF'] . '/', $pos + 10)), -1);
		$count = count($segments);

		for($idx; $idx < $count; ++$idx) {
			if($segments[$idx] == '') break;
			if(!is_dir(NS_SYSTEM_PATH . '/' . $cf->ApplicationFolder . '/controllers' .  $dir . '/' . $segments[$idx])) break;

			$dir .= '/' . $segments[$idx];
		}

		if(!isset($segments[$idx]) || $segments[$idx] == '') $segments[$idx] = $cf->Application->DefaultController;

		if(($handle = @opendir(NS_SYSTEM_PATH . '/' . $cf->ApplicationFolder . '/controllers' . $dir))) {
			while(false !== ($entry = readdir($handle))) {
				if(strtolower($segments[$idx] . '.php') === strtolower($entry)) {
					$this->File = NS_SYSTEM_PATH . '/' . $cf->ApplicationFolder . '/controllers' . $dir . '/' . $entry;
					$class = substr($entry, 0, -4);
					break;
				}
			}

			closedir($handle);
		}

		ns_gettext_init($class, NS_SYSTEM_PATH . '/' . $cf->ApplicationFolder . '/locales');
		if(!is_readable($this->File)) throw new PageNotFoundException();

		require($this->File);
		if(defined('_NAMESPACE_')) $class = _NAMESPACE_ . '\\' . $class;

		if(!class_exists($class)) throw new PageNotFoundException();

		$this->App = new $class;
		if(!$this->App instanceof Controller && !$this->App instanceof RESTController) throw new PageNotFoundException();

		$this->App->Path = NS_SYSTEM_PATH . '/' . $cf->ApplicationFolder;

		if(preg_match('@\\\\([\w]+)$@', ($dir .  '/' . strtolower(get_class($this->App))), $matches)) {
			$this->App->ControllerPath = $matches[1];
		}

		if(!isset($segments[++$idx]) || $segments[$idx] == '') $segments[$idx] = $cf->Application->DefaultControllerAction;

		if(!method_exists($this->App, $segments[$idx]) || $segments[$idx][0] == '_') throw new PageNotFoundException();
		else $this->App->Action = $segments[$idx]; ++$idx;

		for($idx; $idx < $count; ++$idx) { if($segments[$idx] == '') break; $this->App->Params[] = $segments[$idx]; }
	}

	/**
	 *Create or retrieve object instance
	 *
	 */
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>