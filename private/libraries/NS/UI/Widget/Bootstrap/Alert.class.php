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

/**
 *Create alert
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Alert extends UI {
	const TYPE_SUCCESS = 'success';
	const TYPE_INFO = 'info';
	const TYPE_WARNING = 'warning';
	const TYPE_DANGER = 'danger';

	function __construct($name = null, $message, $show_close = true, $type = null, $args = array()) {
		if($type == null) $type = self::TYPE_SUCCESS;
		if($show_close) $message = '<button type="button" class="close" data-dismiss="alert">&times;</button>' . $message;

		$count = $this->getUICount(__CLASS__);
		$args['class'] = isset($args['class']) ? $args['class'] . ' NS-Bootstrap-Alert ' . 'NS-Bootstrap-Alert-' . $count .  ' alert alert-' . $type : 'NS-Bootstrap-Alert ' . 'NS-Bootstrap-Alert-' . $count .  ' alert alert-' . $type;

		StyleManager::getInstance()->addSource(NS_PUBLIC_PATH . '/ns/asset/3rdparty/bootstrap/css/bootstrap.min.css');
		$scm = ScriptManager::getInstance();
		$scm->addSource(NS_JQUERY_PATH);
		$scm->addSource(NS_BOOTSTRAP_PATH);

		if($name != null) $args['id'] = $name;
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);
		
		parent::__construct($this->constructUI('div', true, $message));
	}
}
?>