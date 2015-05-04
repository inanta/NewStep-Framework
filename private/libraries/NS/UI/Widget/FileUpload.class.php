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

namespace NS\UI\Widget;

use NS\UI\UI;
use NS\UI\Widget\Hidden;

/**
 *Create file upload form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class FileUpload extends UI {
	function __construct($name, $value = '', $maxfilesize = 102400000, $args = array()) {
		$this->_attr['class'] = 'NS-FileUpload';

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$this->_attr['type'] = 'file';
		$this->_attr['name'] = $name;
		$this->_attr['value'] = $value;
		
		parent::__construct(new Hidden('MAX_FILE_SIZE', $maxfilesize) . $this->constructUI('input'));
	}
}
?>
