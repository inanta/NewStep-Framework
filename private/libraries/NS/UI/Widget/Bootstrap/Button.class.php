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

namespace NS\UI\Widget\Bootstrap;

use NS\UI\UI;

/**
 *Create button form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Button extends UI {
	/**
	*Normal HTML button
	*/ 
	const BUTTON_NORMAL = 'Button';
	
	/**
	*Submit form button
	*/ 
	const BUTTON_SUBMIT = 'Submit';
	
	/**
	*Reset form button
	*/ 
	const BUTTON_RESET = 'Reset';

	function __construct($name, $value = '', $type = self::BUTTON_NORMAL, $text = null, $args  = array()) {
		$this->_attr['class'] = 'NS-Bootstrap-Button btn';
		$this->_attr['name'] = $name;

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		if(!isset($this->_attr['id'])) $this->_attr['id'] = $name;
		$this->_attr['type'] = strtolower($type);
		$this->_attr['value'] = $value;

		if($value == '') $value = $type;
		parent::__construct($this->constructUI('button', true, ($text == null ? ucwords($value) : $text)));
	}
}
?>
