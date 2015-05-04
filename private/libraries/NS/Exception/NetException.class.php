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

namespace NS\Exception;

class NetException extends Exception {
	/**
	 *Connection cannot be initialized
	 */
	const NOT_CONNECTED = 1;
	
	/**
	 *Unable to login to network resource
	 */
	const UNABLE_TO_LOGIN = 2;

	/**
	 *Exception constructor
	 *
	 *@param array $args Exception parameter to show appropriate message
	 */
	function __construct($args = array()) {
		$message = null;
		$this->ErrorCode = $args['code'];

		switch ($args['code']) {
			case self::NOT_CONNECTED:
				$message = sprintf($this->_('Cannot connect to server [%s]'), $args['server']); break;
			case self::UNABLE_TO_LOGIN:
				$message = sprintf($this->_('Unable to login  with user name [%s]'), $args['username']); break;
			default:
				if(!isset($args['code'])) $args['code'] = 'NO ERROR CODE RETURNED';
				$message = sprintf($this->_('Unknown network error with code [%s]'), $args['code']);
		}

		parent::__construct($message);
	}
}
?>