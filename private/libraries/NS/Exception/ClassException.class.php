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

final class ClassException extends Exception {
	/**
	 *Class not found error code
	 */
	const CLASS_NOT_FOUND = 1;

	/**
	 *Exception constructor
	 *
	 *@param array $args Exception parameter to show appropriate message
	 */
	function __construct($args) {
		$message = null;
		$this->ErrorCode = $args['code'];
		
		switch ($args['code']) {
			case self::CLASS_NOT_FOUND:
				$message = sprintf($this->_('Class [%s] is not exist'), $args['class']);
				break;
			default:
				$message = $this->_('Unknown NewStep class error');
		}
		
		parent::__construct($message);
	}
}
?>
