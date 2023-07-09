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

namespace NS\Database\Driver;

use NS\Database\Database;
use NS\Database\IDatabaseDriver;
use NS\Exception\LibraryException;
use NS\Exception\DatabaseException;

/**
 *Driver for connecting and handle request to MySQL database sever
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class MySQLDriver extends Database implements IDatabaseDriver
{
	public $FieldQuote = '`';

	/**
	 *Initialize database driver
	 *
	 *@throws LibraryException If MySQL library is not installed
	 */
	function __construct($args = array())
	{
		if (!function_exists('mysql_connect'))
			throw new LibraryException(array('code' => NS_EX_LIB_NOT_INSTALLED, 'class' => __CLASS__, 'library' => 'MySQL'));

		parent::__construct($args);
	}

	/**
	 *Destroying database driver obeject and close database connection to database
	 *
	 */
	function __destruct()
	{
		$this->close();
	}

	/**
	 *Get number of affected rows after query
	 *
	 */
	function affectedRows()
	{
		return @mysql_affected_rows($this->Connection);
	}

	/**
	 *Close database connection to server
	 *
	 */
	function close()
	{
		if (!$this->Persistent)
			@mysql_close($this->Connection);
	}

	/**
	 *Open connection to database server
	 *
	 *@throws DatabaseException When failed to connect and use database
	 */
	function connect()
	{
		$this->Connection = ($this->Persistent ? @mysql_pconnect($this->Host, $this->Username, $this->Password) : @mysql_connect($this->Host, $this->Username, $this->Password));

		if (!is_resource($this->Connection)) {
			switch (mysql_errno()) {
				case 1045:
					throw new DatabaseException(array('code' => DatabaseException::UNABLE_TO_ACCESS, 'user' => $this->Username, 'database' => $this->Database));
					break;
				case 2005:
				default:
					throw new DatabaseException(array('code' => DatabaseException::UNABLE_TO_CONNECT, 'host' => $this->Host));
			}
		}

		if (!@mysql_selectdb($this->Database, $this->Connection)) {
			switch (mysql_errno()) {
				case 1044:
					throw new DatabaseException(array('code' => DatabaseException::UNABLE_TO_ACCESS, 'user' => $this->Username, 'database' => $this->Database));
					break;
				case 1049:
				default:
					throw new DatabaseException(array('code' => DatabaseException::UNABLE_TO_USE, 'database' => $this->Database));
			}
		}
	}

	/**
	 *Native database string escape function
	 *
	 */
	function escape($param)
	{
		return @mysql_real_escape_string($param, $this->Connection);
	}

	/**
	 *Fetch query result as array
	 *
	 */
	function fetchArray($result)
	{
		return @mysql_fetch_array($result);
	}

	/**
	 *Fetch query result as associative array
	 *
	 */
	function fetchAssoc($result)
	{
		return @mysql_fetch_assoc($result);
	}

	/**
	 *Fetch query result as array with numerical key
	 *
	 */
	function fetchRow($result)
	{
		return @mysql_fetch_row($result);
	}

	/**
	 *Get columns name that selected in last query
	 *
	 */
	function fieldName($result, $offset)
	{
		return @mysql_field_name($result, $offset);
	}

	/**
	 *Get columns name that selected in last query
	 *
	 */
	function fieldFlags($result, $offset)
	{
		return @mysql_field_flags($result, $offset);
	}

	/**
	 *Get all tables from current database
	 *
	 */
	function getTables()
	{
		$result = $this->query('SHOW TABLES');
		$tables = array();

		while ($row = $this->fetchRow($result)) {
			$tables[] = $row[0];
		}

		return $tables;
	}

	/**
	 *Get last inserted auto increment ID
	 *
	 */
	function lastInsertID()
	{
		return @mysql_insert_id($this->Connection);
	}

	/**
	 *Get number of columns name that selected in last query
	 *
	 */
	function numFields($result)
	{
		return @mysql_num_fields($result);
	}

	/**
	 *Get number of result after query
	 *
	 */
	function numRows($result)
	{
		return @mysql_num_rows($result);
	}

	/**
	 *Query to database with SQL command
	 *
	 *@param string $query Query that will be executed 
	 *@throws DatabaseException If query to database is failed
	 */
	function query($query)
	{
		if ($result = @mysql_query($query, $this->Connection))
			return $result;

		switch (@mysql_errno($this->Connection)) {
			case 1054:
				throw new DatabaseException(array('code' => DatabaseException::QUERY_COLUMN_NOT_EXIST, 'query' => $query));
				break;
			case 1064:
				throw new DatabaseException(array('code' => DatabaseException::QUERY_ERROR, 'query' => $query));
				break;
			case 1146:
				throw new DatabaseException(array('code' => DatabaseException::QUERY_TABLE_NOT_EXIST, 'query' => $query));
				break;
			default:
				throw new DatabaseException(array('code' => @mysql_errno($this->Connection), 'message' => @mysql_error($this->Connection), 'query' => $query));
		}

		return false;
	}

	/**
	 *Native database random function
	 *
	 */
	function rand($seed = '')
	{
		return 'RAND(' . $seed . ')';
	}
}
?>