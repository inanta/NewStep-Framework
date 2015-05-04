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

class UIException extends Exception {
	const JS_NOT_RENDERED = 1;
	const CSS_NOT_RENDERED = 2;

	function __construct($args) {
		$message = null;
		$this->ErrorCode = $args['code'];

		switch ($args['code']) {
			case self::JS_NOT_RENDERED:
				$message = $this->_('Script manager is not rendered');
				break;
			case self::CSS_NOT_RENDERED:
				$message = $this->_('Style manager is not rendered');
				break;
			default:
				if(!isset($args['code'])) $args['code'] = 'NO ERROR CODE RETURNED';
				$message = sprintf($this->_('Unknown NewStep UI error with code[%s]'), $args['code']);
		}
    
		parent::__construct($message);
	}
}
?>
