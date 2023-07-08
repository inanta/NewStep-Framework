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

use NS\Exception\ObjectException;

/**
 *Base class for object
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class BaseObject {
	private $_var;

	function __call($m, $args) { throw new ObjectException(array('code' => ObjectException::UNDEFINED_METHOD, 'method' => $m, 'args' => $args)); }

	function __get($k) {
		if(isset($this->_var[$k])) {
			if(!isset($this->_var[$k]['w'])) return $this->_var[$k]['v'];
			
			throw new ObjectException(array('code' => ObjectException::WO_PROPERTY, 'property' => $k));
		}

		throw new ObjectException(array('code' => ObjectException::UNDEFINED_GET, 'property' => $k));
	}

	function __set($k, $v) {
		if(isset($this->_var[$k])) {
			if(!isset($this->_var[$k]['r'])) {
				$this->_var[$k]['v'] = $v;
				return;
			}
			
			throw new ObjectException(array('code' => ObjectException::RO_PROPERTY, 'property' => $k, 'value' => $v));
		}
		
		throw new ObjectException(array('code' => ObjectException::UNDEFINED_SET, 'property' => $k, 'value' => $v));
	}

	function __toString() { return get_class($this); }

	/**
	*Create object property and bind property value to another variable
	*
	*@param string $k Property name
	*@param mixed $v Property value
	*/
	final protected function bindProperty($k, &$v) { $this->_var[$k]['v'] =& $v; }

	/**
	*Create object properties and bind property values to another variables
	*
	*@param array $p Associative array that array key will be property name and array  value will be property value
	*/
	final protected function bindProperties(&$p) { foreach($p as $k => $v) $this->bindProperty($k, $p[$k]); }

	/**
	*Create object property
	*
	*@param string $k Property name
	*@param mixed $v Property value
	*@param boolean $nested If true newly created object property will be nested, otherwise property value will return as array when called
	*/
	final protected function createProperty($k, $v = null, $nested = false) {
		if(is_array($v) && $nested) {
			$this->_var[$k]['v'] = new BaseObject();
			$this->_var[$k]['r'] = 1;
			$this->_var[$k]['v']->createProperties($v, $nested);
		}
		else $this->_var[$k]['v'] = $v;
	}

	/**
	*Create object properties
	*
	*@param array $p Associative array that array key will be property name and array value will be property value
	*@param boolean $nested If true newly created object property will be nested, otherwise property value will return array when called
	*/
	final protected function createProperties($p, $nested = false) { foreach($p as $k => $v) $this->createProperty($k, $v, $nested); }

	/**
	*Set object property to read only
	*
	*@param array $k Property name
	*/
	final protected function setReadOnlyProperty($k) {
		if(!isset($this->_var[$k])) throw new ObjectException(array('code' => ObjectException::UNDEFINED_PROPERTY, 'property' => $k));
		else {
			if(isset($this->_var[$k]['w'])) throw new ObjectException(array('code' => ObjectException::ALREADY_WO_PROPERTY, 'property' => $k));
			else $this->_var[$k]['r'] = 1;
		}
	}

	/**
	*Set object properties to read-only
	*
	*@param array $p List of object property name
	*/
	final protected function setReadOnlyProperties($p) { foreach($p as $k) $this->setReadOnlyProperty($k); }

	/**
	*Set object property to write-only
	*
	*@param array $k Property name
	*/
	final protected function setWriteOnlyProperty($k) {
		if(!isset($this->_var[$k])) throw new ObjectException(array('code' => ObjectException::UNDEFINED_PROPERTY, 'property' => $k));
		else {
			if(isset($this->_var[$k]['r'])) throw new ObjectException(array('code' => ObjectException::ALREADY_RO_PROPERTY, 'property' => $k));
			else $this->_var[$k]['w'] = 1;
		}
	}

	/**
	*Set object properties to write-only
	*
	*@param array $p List of object property name
	*/
	final protected function setWriteOnlyProperties($p) { foreach($p as $k) $this->setReadOnlyProperty($k); }
}
?>