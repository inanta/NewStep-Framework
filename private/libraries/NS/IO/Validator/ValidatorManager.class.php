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

namespace NS\IO\Validator;

use NS\SingletonObject;
use NS\UI\ScriptManager;

/**
 *Manage validator for client side script (Javascript)
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class ValidatorManager extends SingletonObject {
	private $_id, $_messages = array(), $_rules = array(), $_scm = null;

	function initializeValidator($id, $validators = array()) {
		$this->_scm = ScriptManager::getInstance();
		$this->_scm->addSource(NS_JQUERY_PATH);
		$this->_scm->addSource(NS_PUBLIC_PATH . '/ns/asset/3rdparty/jquery.validate/jquery.validate.min.js');

		$rules = array();
		$messages = array();

		if(count($validators) > 0) {
			foreach($validators as $validator) {
				if($validator instanceof Validator) {
					$rules[''.$validator] = $validator->Param;
					$messages[''.$validator] = $validator->Message; 
				}
			}

			if(count($rules) > 0) {
				$this->_rules[$id] = $rules;
				$this->_messages[$id] = $messages;
			}
		}
	}

	function addValidate($id) {
		$this->_scm->addScript('jQuery(document).ready(function() { jQuery("#'.$id.'").validate(' . ValidatorManager::getInstance()->getValidator() . '); });');
	}

	function getValidator() {
		return json_encode(array('ignore' => '', 'rules' => $this->_rules, 'messages' => $this->_messages));
	}

	static function getInstance() {
		return self::createInstance(__CLASS__);
	}
}
?>