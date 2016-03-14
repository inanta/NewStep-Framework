<?php
/*
	Copyright (C) 2008 - 2015 Inanta Martsanto
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

namespace NS\Database;

class DatabaseFunction {
	private $_ar, $_render = '';

	function __construct(&$ar) { $this->_ar =& $ar; }

	function concat($args, $as = null) {
		$columns = array();
		
		foreach($args as $column) {
			$columns[] = $this->_quote($column);
		}

		$this->_render = 'CONCAT(' .@implode(', ', $columns) . ')';
		
		if($as !== null) $this->_ar->addColumnAlias($this->_render, $as);

		return clone $this;
	}

	function sum($column, $as) {
		$this->_render = 'SUM(' . $this->_quote($column) . ')';
		
		if($as !== null) $this->_ar->addColumnAlias($this->_render, $as);

		return clone $this;
	}
	
	private function _quote($column) {
		if(in_array($column, $this->_ar->getColumns())) {
			return $this->_ar->quote($column);
		} else if(in_array($column, $this->_ar->getAllColumns())) {
			$ars = $this->_ar->getHasOne(true);

			foreach($ars as $ar) {
				if($ar->hasColumn($column)) {
					return $ar->quote($column);
				}
			}
		} else {
			return '"' . $column . '"';
		}
	    
		
	}

	function __toString() {
		return $this->_render;
	}
}
