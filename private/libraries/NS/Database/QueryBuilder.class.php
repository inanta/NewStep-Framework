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

/**
 *Handle SQL command creation with object style
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class QueryBuilder extends Object {
	private $_fields, $_tables, $_conditions;

	function select($fields = '*') {
		$this->_fields = !is_array($fields) ? array($fields) : $fields;
		return $this;
	}

	function from($tables) {
		$this->_tables = $tables;
		return $this;
	}

	function where($conditions) {
		$this->_conditions = $conditions;
		return $this;
	}

	function execute() {
		$db = DatabaseFactory::getInstance();

		$sql = "SELECT " . implode(", ", $this->_fields) . " FROM " . implode(", ", $this->_tables);
		if(count($this->_conditions)) foreach($this->_conditions as $condition) { $s;}
		$db->query("SELECT " . implode(", ", $this->_fields) . " FROM " . implode(", ", $this->_tables));
	}
}
?>