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
 *Create text box form element for password input
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Password extends UI {	
	function __construct($name, $value = '', $placeholder = null, $validators = null, $args = array()) {
		$this->_attr['class'] = 'NS-Text NS-Password';

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$this->_attr['id'] = $name;
		$this->_attr['type'] = 'password';
		$this->_attr['name'] = $name;
		$this->_attr['value'] = $value;

		if($validators != null) ValidatorManager::getInstance()->initializeValidator($name, $validators);

		if($placeholder != null) {
			$name = 'WidgetPassword-' . $this->getUICount('WidgetPassword');
			$this->_attr['class'] .= ' ' . $name;
			$this->_attr['placeholder'] = $placeholder;

			$sm = ScriptManager::getInstance();
			$sm->addSource(NS_JQUERY_PATH);
			$sm->addScript(
				"jQuery(document).ready(function() {
					jQuery(function() {
						if(typeof jQuery.support.placeholder == 'undefined') {
								jQuery.support.placeholder = false;
	
								var el = document.createElement('input');
								if('placeholder' in el) jQuery.support.placeholder = true;
							
						}

						if(!jQuery.support.placeholder) {
							jQuery('.$name').after(jQuery('.$name').clone().addClass('NS-Widget-Placeholder').attr('id', '').attr('name', '').attr('type', 'text').removeClass('$name').addClass('$name-placeholder').val('$placeholder'));

							jQuery('.$name-placeholder').show();
							jQuery('.$name').hide();
		
							jQuery('.$name-placeholder').focus(function() {
								jQuery('.$name').show().focus();
								jQuery('.$name-placeholder').hide();
							});
		
							jQuery('.$name').blur(function() {
								if (this.value == '') {
									jQuery('.$name').hide().focus();
									jQuery('.$name-placeholder').show().blur();
								}
							});
						}
					});
				});"
			);
		}

		parent::__construct($this->constructUI('input'));
	}
}
?>