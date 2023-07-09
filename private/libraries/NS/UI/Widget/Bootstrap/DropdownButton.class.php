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
use NS\UI\Widget\Hyperlink;

/**
 *Create dropdown button
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class DropdownButton extends UI {
	const BUTTON_DIVIDER = '<li class="divider"></li>';

	function __construct($name = null, $lists = array(), $split_button = false, $args = array()) {
		$count = $this->getUICount(__CLASS__);
		$args['class'] = isset($args['class']) ? $args['class'] . ' NS-Bootstrap-DropdownButton ' . 'NS-Bootstrap-DropdownButton-' . $count .  ' btn-group' : 'NS-Bootstrap-DropdownButton ' . 'NS-Bootstrap-DropdownButton-' . $count .  ' btn-group';

		StyleManager::getInstance()->addSource(NS_PUBLIC_PATH . '/ns/asset/3rdparty/bootstrap/css/bootstrap.min.css');
		$scm = ScriptManager::getInstance();
		$scm->addSource(NS_JQUERY_PATH);
		$scm->addSource(NS_BOOTSTRAP_PATH);

		$content = '';
		$first_button = key($lists);

		if(is_string($lists[$first_button])) $lists[$first_button] = new Hyperlink(null, $lists[$first_button], $first_button);

		if($split_button) {
			$lists[$first_button]->_attr['class'] = 'btn';
			$content .= $lists[$first_button]->constructUI();

			if($lists[$first_button] instanceof \NS\UI\Widget\Button || $lists[$first_button] instanceof \NS\UI\Widget\Bootstrap\Button) $content .= new UI\Widget\Button('split', null, UI\Widget\Button::BUTTON_NORMAL, '<span class="caret">', array('class' => 'btn dropdown-toggle', 'data-toggle' => 'dropdown'));
			else $content .= new Hyperlink(null, '#', '<span class="caret">', array('class' => 'btn dropdown-toggle', 'data-toggle' => 'dropdown'));
		} else {
			$lists[$first_button]->_attr['class'] = 'btn dropdown-toggle';
			$lists[$first_button]->_attr['data-toggle'] = 'dropdown';
			$content .= $lists[$first_button]->constructUI($lists[$first_button]->_tag, $lists[$first_button]->_isTagClosed, $lists[$first_button]->_content . '<span class="caret"></span>');
		}

		$content .= '<ul class="dropdown-menu">';

		unset($lists[$first_button]);
		foreach($lists as $label => $link) {
			if(empty($link)) continue;

			$content .= '<li>' . (($link instanceof \NS\UI\Widget\Hyperlink) || ($link instanceof \NS\UI\Widget\Button) || ($link instanceof \NS\UI\Widget\Bootstrap\Button) ? $link : '<a href="' . $link . '">' . $label . '</a>') . '</li>';
		}

		$content .= '</ul>';

		if($name != null) $args['id'] = $name;
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		parent::__construct($this->constructUI('div', true, $content));
	}
}
?>