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

/**
 *Create label form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Sortable extends UI {	
	function __construct($name, $list = array(), $args = array()) {
		$this->_attr['id'] = $name;
		$this->_attr['name'] = $name;

		$name = 'NS-Sortable-' . $this->getUICount(__CLASS__);
		$this->_attr['class'] = 'NS-Sortable ' . $name;

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$content = '';
		foreach($list as $value) {
			$content .= '<li>' . $value . '</li>';
		}

		$sm = ScriptManager::getInstance();
		    $sm->addSource(NS_JQUERY_PATH);
		    $sm->addSource(NS_JQUERY_UI_PATH);
		    $sm->addExternalSource(NS_JQUERY_UI_STYLE_URL);
		    $sm->addScript(
			    "jQuery(document).ready(function() {
				    jQuery(function() {
					    jQuery('." . $name . "').sortable();
				    });
			    });"
		    );

		parent::__construct($this->constructUI('ul', true, $content));
	}
}
?>
