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

namespace NS\Exception;

final class ObjectException extends Exception {
	/**
	 *Undefined property when getting value error code
	 */
	const UNDEFINED_GET = 1;
	/**
	 *Undefined property when setting value error code
	 */
	const UNDEFINED_SET = 2;
	/**
	 *Property is not defined before error code
	 */
	const UNDEFINED_PROPERTY = 3;
	/**
	 *Property already et as write-only propery error code
	 */
	const ALREADY_WO_PROPERTY = 4;
	/**
	 *Property already et as read-only propery error code
	 */
	const ALREADY_RO_PROPERTY = 5;
	/**
	 *Write only property and cannot be read error code
	 */
	const WO_PROPERTY = 6;
	/**
	 *Read only property and cannot be write error code
	 */
	const RO_PROPERTY = 7;
	/**
	 *Undefined method error code
	 */
	const UNDEFINED_METHOD = 8;

	/**
	 *Exception constructor
	 *
	 *@param array $args Exception parameter to show appropriate message
	 */
	public function __construct($args = array()) {
		$message = null;
		$this->ErrorCode = $args['code'];

		switch ($args['code']) {
			case self::UNDEFINED_GET:
				$message = sprintf($this->_('Property [%s] is not exist and cannot return a value'), $args['property']); break;
			case self::UNDEFINED_SET:
				$message = sprintf($this->_('Property [%s] not defined before and cannot be set to [%s]'), $args['property'], $args['value']); break;
			case self::UNDEFINED_PROPERTY:
				$message = sprintf($this->_('Property [%s] not defined before and cannot be set to read-only or write-only property'), $args['property']); break;
			case self::ALREADY_WO_PROPERTY:
				$message = sprintf($this->_('Property [%s] already defined as write-only property and cannot be set as read-only property'), $args['property']); break;
			case self::ALREADY_RO_PROPERTY:
				$message = sprintf($this->_('Property [%s] already defined as read-only property and cannot be set as write-only property'), $args['property']); break;
			case self::WO_PROPERTY:
				$message = sprintf($this->_('Property [%s] is write-only property and cannot return a value'), $args['property']); break;
			case self::RO_PROPERTY:
				$message = sprintf($this->_('Property [%s] is read-only property and cannot be set to [%s]'), $args['property'], $args['value']); break;
			case self::UNDEFINED_METHOD:
				$argsline = implode(', ', $args['args']);

				if($argsline == '') $message = sprintf($this->_('Call to undefined method [%s]'), $args['method']);
				else $message = sprintf($this->_('Call to undefined method [%s] with [%s] arguments [%s]'), $args['method'], count($args['args']), $argsline);

				break;
			default:
			    if(!isset($args['code'])) $args['code'] = 'NO ERROR CODE RETURNED';
			    $message = sprintf($this->_('Unknown NewStep Object error with code[%s]'), $args['code']);
		}

		parent::__construct($message);
	}
}
?>
