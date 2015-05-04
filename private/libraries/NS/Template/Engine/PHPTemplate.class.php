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

namespace NS\Template\Engine;

use NS\Template\Template;
use NS\Exception\IOException;

/**
 *Native PHP templating engine
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
final class PHPTemplate extends Template {
	function __construct($args = array()) {
		parent::__construct($args);
	}

	function fetch($resource_name) {
		if(!is_file($resource_name)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $resource_name));
		if(!is_readable($resource_name)) throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $resource_name));

		ob_start();
		extract($this->_vars, EXTR_REFS);

		include($resource_name);
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	function append($tpl_var, $value = null) {
		$this->_vars[$tpl_var][] = $value;
	}
}
?>
