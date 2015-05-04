<?php
/*
	Copyright (C) 2008 - 2014 Inanta Martsanto
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
use NS\UI\StyleManager;

/**
 *Create datepicker user interface
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class DatePicker extends UI {
	/**
	 * 
	 * @param string $id Widget ID and name
	 * @param string $value Widget predefined value
	 * @param string $placeholder Widget placeholder text
	 * @param array $validators Array or validator
	 * @param array $args Optional widget HTML attribute
	 * @param array $options Optional widget option
	 */
	function __construct($id, $value = null, $placeholder = null, $validators = null, $args = array(), $options = array()) {
		StyleManager::getInstance()->addExternalSource(NS_JQUERY_UI_STYLE_URL);
		$scm = ScriptManager::getInstance();
		$scm->addSource(NS_JQUERY_PATH);
		$scm->addSource(NS_JQUERY_UI_PATH);

		if(!isset($options['changeYear'])) $options['changeYear'] = true;
		if(!isset($options['changeMonth'])) $options['changeMonth'] = true;
		if(!isset($options['dateFormat'])) $options['dateFormat'] = 'dd/mm/yy';

		$count = $this->getUICount(__CLASS__);
		$args['class'] = 'NS-Datepicker NS-DatePicker-' . $count;
		parent::__construct(new Text($id, $value, $placeholder, $validators, $args));
		$scm->addScript('jQuery(function(){ jQuery(".NS-DatePicker-' . $count . '").datepicker(' . json_encode($options) . '); });');
	}
}
?>
