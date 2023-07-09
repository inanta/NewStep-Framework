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
use NS\UI\StyleManager;
use NS\UI\ScriptManager; 
use NS\UI\Widget\CheckBox;

/**
 *Create switch button form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class SwitchButton extends UI {
	function __construct($name, $value = '', $checked = false, $label = '', $args = array()) {
		$count = $this->getUICount(__CLASS__);
		$this->_attr['class'] = 'NS-Bootstrap-SwitchButton-Wrapper NS-Bootstrap-SwitchButton-Wrapper-' . $count .  ' custom-control custom-switch';

		//$scm = ScriptManager::getInstance();
		//$scm->addSource(NS_JQUERY_PATH);
		//$scm->addSource(NS_BOOTSTRAP_PATH);

		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		parent::__construct($this->constructUI('div', true, '
			' . new CheckBox($name, $value, $checked, '', array('class' => 'custom-control-input')) . '
			<label class="custom-control-label" for="' . $name . '">' . $label . '</label>
		'));
	}
}
?>
