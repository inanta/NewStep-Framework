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
 *Create textarea form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class TextArea extends UI {
	function __construct($name, $value = '', $placeholder = null, $validators = null, $args = array()) {
		$this->_attr['class'] = 'NS-TextArea';

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$this->_attr['id'] = $name;
		$this->_attr['name'] = $name;

		if($validators != null) ValidatorManager::getInstance()->initializeValidator($name, $validators);

		if($placeholder != null) {
			$name = 'WidgetTextArea-' . $this->getUICount('WidgetTextArea');
			$this->_attr['class'] .= ' ' . $name;

			$sm = ScriptManager::getInstance();
			$sm->addSource(NS_JQUERY_PATH);
			$sm->addScript(
				"jQuery(document).ready(function() {
					jQuery('.$name').addClass('NS-Widget-Placeholder').val('$placeholder').focus(function() {
						if (this.value == '$placeholder') {
							jQuery(this).removeClass('NS-Widget-Placeholder').val('');
						};
					}).blur(function() {
						if (this.value == '') {
							jQuery(this).addClass('NS-Widget-Placeholder').val('$placeholder');
						};
					});
				});"
			);
		}

		parent::__construct($this->constructUI('textarea', true, $value));
	}
}
?>
