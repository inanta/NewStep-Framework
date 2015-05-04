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
use NS\UI\Widget\Hyperlink;
use NS\UI\ScriptManager;

/**
 *Create widget collection
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class WidgetCollection extends UI {
	function __construct($widget, $attrs = array()) {
		if(!($widget instanceof UI)) throw new IllegalArgumentException($widget);

		$count = $this->getUICount(__CLASS__);

		$this->UI = '<div class="NS-WidgetCollection-Container NS-WidgetCollection-Container-' . $count . '">';

		$widget->_attr['name'] = $widget->_attr['name'] . '[]';
		unset($widget->_attr['id']);

		$widget_attr = array();

		foreach($attrs as $attr => $values) {
			foreach($values as $key => $value) {
				$widget_attr[$key][$attr] = $value;
			}
		}

		foreach($widget_attr as $widget_data) {
			$widget->_attr = array_merge($widget->_attr, $widget_data);

			$this->UI .= '<div class="NS-WidgetCollection-Element NS-WidgetCollection-Element-' . $count . '">' . $widget->constructUI() . '</div>';
		}

		$this->UI .= '</div>';
	}
}
?>
