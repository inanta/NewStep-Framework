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
 *Create breadcrumb
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Breadcrumb extends UI {
	function __construct($lists = array(), $args = array()) {
		$count = $this->getUICount(__CLASS__);
		$args['class'] = isset($args['class']) ? $args['class'] . ' NS-Bootstrap-Breadcrumb ' . 'NS-Bootstrap-Breadcrumb-' . $count .  ' breadcrumb' : 'NS-Bootstrap-Breadcrumb ' . 'NS-Bootstrap-Breadcrumb-' . $count .  ' breadcrumb';

		StyleManager::getInstance()->addSource(NS_PUBLIC_PATH . '/ns/asset/3rdparty/bootstrap/css/bootstrap.min.css');
		$scm = ScriptManager::getInstance();
		$scm->addSource(NS_JQUERY_PATH);
		$scm->addSource(NS_BOOTSTRAP_PATH);

		$menu_count = count($lists);
		$counter = 0;
		$content = '';

		foreach($lists as $label => $link) {
			++$counter;

			if($counter < $menu_count) {
				$content .= '<li><a href="' . $link . '">' . $label . '</a> <span class="divider">/</span></li>';
			} else {
				$content .= '<li class="active">' . $label . '</li>';
			}
		}

		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		parent::__construct($this->constructUI('ul', true, $content));
	}
}
?>
