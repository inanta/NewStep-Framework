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
namespace NS\Database;

use NS\Object;

/**
 *Handle SQL command creation with object style
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class QueryBuilder extends Object {
	private $_fields, $_tables, $_conditions;

	/**
	 *@var string Last generated command   
	 */
	public $LastQuery;

	function select($fields = '*') {
		$this->_fields = !is_array($fields) ? array($fields) : $fields;
		return $this;
	}

	function from($tables) {
		$this->_tables = !is_array($tables) ? array($tables) : $tables;
		return $this;
	}

	function where($conditions) {
		$this->_conditions = $conditions;
		return $this;
	}

	function execute() {
		$db = DatabaseFactory::getInstance();
		$this->_compile();

		$db->query($this->LastQuery);
	}
	
	function getSQL() {
		$this->_compile();
		return $this->LastQuery;
	}

	private function _compile() {
		$this->LastQuery = "SELECT " . implode(", ", $this->_fields) . " FROM " . implode(", ", $this->_tables);

		if(count($this->_conditions)) {
			$where = array();

			foreach($this->_conditions as $column => $value) { 
				$where[] = $column . " = " . $value; 
			}

			$this->LastQuery .= " WHERE " . implode(' AND ', $where);
		}
	}
}
?>