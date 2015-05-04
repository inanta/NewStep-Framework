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

namespace NS\Exception;

class DatabaseException extends Exception {
	/**
	 *Unable to connect database server error code
	 */
	const UNABLE_TO_CONNECT = 1;
	/**
	 *Unable to access database found error code
	 */
	const UNABLE_TO_ACCESS = 2;
	/**
	 *Unable to use database error code
	 */
	const UNABLE_TO_USE = 3;
	/**
	 *Query error error code
	 */
	const QUERY_ERROR = 4;
	/**
	 *Table not found in query error code
	 */
	const QUERY_TABLE_NOT_EXIST = 5;
	/**
	 *Column not found in query error code
	 */
	const QUERY_COLUMN_NOT_EXIST = 6;
	/**
	 *Undefined database connection set error code
	 */
	const UNDEFINED_CONNECTION_NAME = 7;

	function __construct($args = array()) {
		$message = null;
		$this->ErrorCode = $args['code'];

		switch ($args['code']) {
			case self::UNABLE_TO_CONNECT:
				$message = sprintf($this->_('Unable to connect to server host [%s]'), $args['host']); break;
			case self::UNABLE_TO_ACCESS:
				$message = sprintf($this->_('Access denied for user [%s] to database [%s]'), $args['user'], $args['database']); break;
			case self::UNABLE_TO_USE:
				$message = sprintf($this->_('Unable to select or open database [%s]'), $args['database']); break;
			case self::QUERY_ERROR:
				$message = sprintf($this->_('Unable to execute query [%s] please check if the query is correct'), $args['query']); break;
			case self::QUERY_TABLE_NOT_EXIST:
				$message = sprintf($this->_('Unable to execute query [%s] please check table name in the query'), $args['query']); break;
			case self::QUERY_COLUMN_NOT_EXIST:
				$message = sprintf($this->_('Unable to execute query [%s] please check column name in the query'), $args['query']); break;
			case self::UNDEFINED_CONNECTION_NAME:
				$message = sprintf($this->_('Connection name [%s] is not defined'), $args['connection']); break;
			default:
				if(!isset($args['code'])) $args['code'] = 'NO ERROR CODE RETURNED';
				$message = sprintf($this->_('Unknown database error [%s] with code [%s] when executing query [%s]'), $args['message'], $args['code'], $args['query']);
		}

		parent::__construct($message);
	}
}
?>