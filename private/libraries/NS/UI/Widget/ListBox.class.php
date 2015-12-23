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
use NS\IO\Validator\ValidatorManager;

/**
 *Create combo box or list box form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class ListBox extends UI {
	public $Options = array();
	private $_previousSelected = null;

	function __construct($name, $value = null, $selected = null, $validators = null, $args = array()) {
		$this->_attr['class'] = 'NS-ListBox';

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$this->_attr['id'] = $name;
		$this->_attr['name'] = isset($args['multiple']) ? $name . '[]' : $name;

		$this->createProperties(array('Selected' => ($selected == null) ? null : $selected));

		if($value != null) {
			$this->addItem($value);
		} else {
		    parent::__construct($this->constructUI('select', true, implode('', $this->Options)));
		}

		if($validators != null) ValidatorManager::getInstance()->initializeValidator($name, $validators);
	}

	function addItem($item, $name = '') {
		if(is_array($item)) {
			foreach($item as $value => $text) {
				$this->Options[$value] = '<option value="'.$value.'">'.$text.'</option>';
			}
		} else {
			$name != '' ? $this->Options[$name] = '<option value="'.$name.'">'.$item.'</option>' : $this->Options[$item] = '<option value="'.$item.'">'.$item.'</option>';
		}

		$this->changeSelected();
		parent::__construct($this->constructUI('select', true, implode('', $this->Options)));
	}

	function __set($property, $value) {
		if($property == 'Selected') {
			$this->_previousSelected = $this->Selected;
			parent::__set($property, $value);
			$this->changeSelected();

			return;
		}

		parent::__set($property, $value);
	}

	private function changeSelected() {
		if($this->_previousSelected != null) $this->Options[$this->_previousSelected] = str_replace(' selected="selected"', '', $this->Options[$this->_previousSelected]);
		if($this->Selected != null && isset($this->Options[$this->Selected])) $this->Options[$this->Selected] = str_replace('<option', '<option selected="selected"', $this->Options[$this->Selected]);
	}
}
?>
