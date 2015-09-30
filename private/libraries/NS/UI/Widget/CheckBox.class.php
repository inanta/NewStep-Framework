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

/**
 *Create check box form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class CheckBox extends UI {
	function __construct($name, $value = '', $is_checked = false, $label = '', $args = array()) {
		if($is_checked) $this->_attr['checked'] = 'checked';

		$this->_attr['class'] = 'NS-Checkbox';

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		if($name != null) {
			$this->_attr['id'] = $name;
			$this->_attr['name'] = $name;
		}

		$this->_attr['type'] = 'checkbox';
		$this->_attr['value'] = $value;

		if(!empty($label)) {
			$this->_attr['label'] = $label;
		}

		parent::__construct($this->constructUI('input'), false, $label);;
	}

	protected function constructUI($tag = null, $close_tag = false, $content = '') {
		if(isset($this->_attr['label'])) {
			$content = '&nbsp;' . $this->_attr['label'];
			unset($this->_attr['label']);
		}

		if(!empty($content)) return '<label>' . parent::constructUI($tag, $close_tag) . $content . '</label>';
		else return parent::constructUI($tag, $close_tag);
	}
}
?>
