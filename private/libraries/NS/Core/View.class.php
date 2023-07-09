<?php
/*
	Copyright (C) 2008 - 2019 Inanta Martsanto
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

use NS\Core\Router;
use NS\Template\Template;

/**
 *Model base class
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class View {
	private $_template = null;

	function __construct($file, $assign, $paths = null) {
		$config = Config::getInstance();

		$this->_template = Template::getInstance();
		$this->_template->File = $file;
		$this->_template->assign($assign);

		$router = Router::getInstance();

		if ($paths === null) {
			$paths = array($router->App->Path . '/views');
		} else {
			$paths[] = $router->App->Path . '/views';
		}

		$paths[] = NS_SYSTEM_PATH . '/' . $config->ApplicationFolder . '/views';

		foreach ($paths as $path) {
			// echo $path . '/' . $file . '<br>';
			if(is_readable($path . '/' . $file)) {
				$this->_template->Path = $path;

				break;
			}
		}

		// die($this->_template->Path);

		$this->_template->display($this->_template->Path . '/' . $this->_template->File);
	}
}
?>