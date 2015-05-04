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

/**
 *Validate if value is between larger that specific value
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Min extends Validator {	
	function __construct($min, $message = 'Please enter a valid number and minimum value is %s') {
		parent::__construct('min', sprintf($message, $min));
		$this->Param = $min;
	}
	
	function validate(&$data) {
		return ($data >= $this->Param);
	}
}
?>