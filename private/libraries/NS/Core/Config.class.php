<?php
/*
	Copyright (C) 2008 - 2014 Inanta Martsanto
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

namespace NS\Core;

use NS\SingletonObject;
use NS\Database\ActiveRecord;

/**
 *Handle configuration values from multiplle file in configuration folder, database table  and run-time configuration
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Config extends SingletonObject {
	/**
	 *Load configuration file by name in configuration folder
	 *
	 *@param string $name Configuration name that will be loaded 
	 */
	function load($name) {
		require(NS_SYSTEM_PATH . '/' . $this->ConfigFolder . '/' . $name . '.inc.php');
		$this->createProperties(array($name => ${$name}), true);
	}

	/**
	 *Load configuration file by name in configuration folder
	 *
	 *@param string $table Database table name for configuration source
	 *@param string $column_key Column in database table for configuration key
	 *@param string $column_value Column in database table for configuration value
	 *@param array|NS\Database\DatabaseFilterCriteria $condition Filter result for configuration
	 *@param string $name Mapping name to configuration object
	 */
	function loadFromDB($table, $column_key, $column_value, $condition, $name) {
		$mapper_config = array();
		$ar = new ActiveRecord($table);
		
		$ar->findAll(array($column_key, $column_value), $condition);
		while($ar->hasNext()) {
			$mapper_config[$ar->{$column_key}] = $ar->{$column_value};
			$ar->next();
		}

		$this->createProperties(array($name => $mapper_config), true);
	}

	/**
	 *Bind value to class property on run time
	 *
	 *@param string $value Value that will be binded to configuration object
	 */
	function bind(&$value) {
		$this->bindProperties($value);
	}

	/**
	 *Add new configuration on run time
	 *
	 *@param string $value Value that will be added to configuration object
	 */
	function add($value) {
		$this->createProperties($value, true);
	}

	/**
	 *Create or retrieve object instance
	 *
	 *@return self
	 */
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>
