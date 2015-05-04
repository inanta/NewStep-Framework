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

use NS\Exception\UnsupportedOperationException;

/**
 *Validate 2 value and compare to specific comparation
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Compare extends Validator {
	const EQUALS = 0;
	const GREATER_THAN = 1;
	const LESS_THAN = 2;
	const NOT_EQUALS = 3;

	function __construct($id, $operator = self::EQUALS, $message = 'Not match') {
		switch($operator) {
			case self::EQUALS:
				parent::__construct('equalTo', $message); break;
			case self::GREATER_THAN:
				parent::__construct('greaterThan', $message); break;
			case self::LESS_THAN:
				parent::__construct('lessThan', $message); break;
			case self::NOT_EQUALS:
				parent::__construct('notEqualTo', $message); break;
		}

		$this->Param = '#'.$id;
	}

	function validate(&$data) {
		throw new UnsupportedOperationException();
	}
}

?>
