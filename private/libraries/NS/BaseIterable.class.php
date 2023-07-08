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

namespace NS;

use NS\Exception\UnsupportedOperationException;

/**
 *Iterable base class
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
abstract class BaseIterable extends BaseObject {
	protected $_iterator, $_end, $_collection;

	function __construct($collection = null) {
		if($collection != null) $this->_collection = $collection;

		$this->_end = (count($this->_collection) - 1);
		$this->_iterator = -1;

		$this->bindProperty('Count', $this->_end);
		$this->setReadOnlyProperty('Count');

		$this->next();
	}

	/**
	*Iterate / move pointer to next data
	*
	*/
	function next() {
		++$this->_iterator;
	}

	function hasNext() {
		return ($this->_iterator <= $this->_end);
	}

	function isLast() {
		return ($this->_iterator == $this->_end);
	}

	function isFirst() {
		return ($this->_iterator === 0);
	}

	/**
	*Translate current object data to array
	*
	*/
	function toArray() { return $this->_collection; }
}
?>