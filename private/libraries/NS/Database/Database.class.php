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

use NS\ClassMapper;
use NS\Object;
use NS\Core\Config;
use NS\Exception\DatabaseException;

/**
 *Database connection handler
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
abstract class Database extends Object {
	const DRIVER_MYSQL = 'mysql';
	const DRIVER_SQLITE2 = 'sqlite2';
	const DRIVER_SQLITE = 'sqlite';
	const DRIVER_POSTGRESQL = 'postgresql';

	static private $_driversInstance = array();
	public $FieldQuote = '', $Connection;

	/**
	*Initialize database connection parameter and open database connection to server
	*
	*/
	function __construct(&$args) {
		$this->createProperties($args);
		$this->connect();
	}

	/**
	*Handle if database table has prefix
	*
	*/
	function prefix($table_name = '') {
		if($table_name != '') {
			if($this->Prefix != '') $table_name = '_' . $table_name;
			return $this->Prefix . $table_name;
		}

		return $this->Prefix;
	}

	/**
	*Create or retrieve object instance
	*
	*@return self
	*/
	static function getInstance($conn = null) {
		$Database;

		if(is_string($conn) || $conn == null) {
			require(NS_SYSTEM_PATH . '/' . Config::getInstance()->ConfigFolder . '/Database.inc.php');

			if($conn == null) $conn = key($Database);
			else {
				if(!isset($Database[$conn])) throw new DatabaseException(array('code' => DatabaseException::UNDEFINED_CONNECTION_NAME, 'connection' => $conn));
			}
		} else if(is_array($conn)) {
			$Database['Temp'] = $conn;
			$conn = 'Temp';
		}

		if(isset(self::$_driversInstance[$conn])) return self::$_driversInstance[$conn];

		switch($Database[$conn]['Driver']) {
			case self::DRIVER_POSTGRESQL:
				return (self::$_driversInstance[$conn] = new Driver\PostgreSQLDriver($Database[$conn]));
				break;
			case self::DRIVER_SQLITE2:
				return (self::$_driversInstance[$conn] = new Driver\SQLite2Driver($Database[$conn]));
				break;
			case self::DRIVER_SQLITE:
				return (self::$_driversInstance[$conn] = new Driver\SQLiteDriver($Database[$conn]));
				break;
			case self::DRIVER_MYSQL:
			default:
				return (self::$_driversInstance[$conn] = new Driver\MySQLDriver($Database[$conn]));
		}
	}
}
?>