<?php
/*
	Copyright (C) 2008 - 2016 Inanta Martsanto
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

namespace NS\Security\Cryptography;

use NS\Exception\SecurityException;

/**
 *Class encrype / decrype string using simple XOR
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Hashids extends \Hashids\Hashids {
	public function __construct($key) {
		parent::__construct($key, 10);
	}

	public function encrypt($number) {
		return $this->encode($number);
	}

	public function decrypt($hash) {
		return $this->decode($hash)[0];
	}
}
?>