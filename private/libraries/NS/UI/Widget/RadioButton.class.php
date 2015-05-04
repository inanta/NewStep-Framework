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
use NS\UI\ScriptManager;
use NS\IO\Validator\ValidatorManager;

/**
 *Create radio button form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class RadioButton extends UI {
	private $_items;

	function __construct($name, $data = null, $selected = null, $validators = null, $args = array()) {
		$this->_attr['class'] = 'NS-RadioButton';

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$this->_attr['id'] = $name;
		$this->_attr['type'] = 'radio';
		$this->_attr['name'] = $name;

		if(is_array($data)) {
			foreach($data as $key => $value) {
				$this->_attr['value'] = $key;

				if($selected != null && $selected == $key) $this->_attr['checked'] = 'checked';
				else unset($this->_attr['checked']);

				$this->_items[$key] = '<label class="' . $this->_attr['class'] . '-Label">' . $this->constructUI('input') . (!empty($value) ? '&nbsp;' . $value : '') . '</label>';
			}
		}

		parent::__construct(implode('', $this->_items));;
	}

	function getItem($key) {
		if(isset($this->_items[$key])) {
			return $this->_items[$key];
		}

		return false;
	}
}
?>
