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

define('NS_EX_DB_UNABLE_TO_CONNECT', 1);
define('NS_EX_DB_UNABLE_TO_ACCESS', 2);
define('NS_EX_DB_UNABLE_TO_USE', 3);
define('NS_EX_DB_QUERY_ERROR', 4);
define('NS_EX_DB_QUERY_TABLE_NOT_EXIST', 5);
define('NS_EX_DB_QUERY_COLUMN_NOT_EXIST', 6);
define('NS_EX_DB_TABLE_NOT_EXIST', 7);
define('NS_EX_DB_COLUMN_NOT_EXIST', 8);
define('NS_EX_DB_UNDEFINED_CONNECTION_NAME', 9);
define('NS_EX_DB_UNDEFINED_PRIMARY_KEY', 10);
define('NS_EX_DB_AR_NOT_INITIALIZED', 11);

class LDAPException extends Exception {
	function __construct($args) {
		$message = null;
		$this->ErrorCode = $args['code'];

		switch ($args['code']) {
			case NS_EX_DB_UNABLE_TO_CONNECT:
				$message = sprintf($this->_('Unable to connect to server host [%s]'), $args['host']); break;
			case NS_EX_DB_UNABLE_TO_ACCESS:
				$message = sprintf($this->_('Access denied for user [%s] to database [%s]'), $args['user'], $args['database']); break;
			case NS_EX_DB_UNABLE_TO_USE:
				$message = sprintf($this->_('Unable to select or open database [%s]'), $args['database']); break;
			case NS_EX_DB_QUERY_ERROR:
				$message = sprintf($this->_('Unable to execute query [%s] please check if the query is correct'), $args['query']); break;
			case NS_EX_DB_QUERY_TABLE_NOT_EXIST:
				$message = sprintf($this->_('Unable to execute query [%s] please check table name in the query'), $args['query']); break;
			case NS_EX_DB_QUERY_COLUMN_NOT_EXIST:
				$message = sprintf($this->_('Unable to execute query [%s] please check column name in the query'), $args['query']); break;
			case NS_EX_DB_TABLE_NOT_EXIST:
				$message = sprintf($this->_('Table [%s] is not exist in database [%s]'), $args['table'], $args['database']); break;
			case NS_EX_DB_COLUMN_NOT_EXIST:
				$message = sprintf($this->_('Column name [%s] is not exist in table [%s]'), $args['column'], $args['table']); break;
			case NS_EX_DB_UNDEFINED_CONNECTION_NAME:
				$message = sprintf($this->_('Connection name [%s] is not defined'), $args['connection']); break;
			case NS_EX_DB_UNDEFINED_PRIMARY_KEY:
				$message = sprintf($this->_('Primary key is not defined in table [%s]'), $args['table']); break;
			case NS_EX_DB_AR_NOT_INITIALIZED:
				$message = sprintf($this->_('Active record that using table [%s] is not initialized'), $args['table']); break;
			default:
				$message = sprintf($this->_('Unknown database error [%s] with code [%s] when executing query [%s]'), $args['message'], $args['code'], $args['query']);
		}

		parent::__construct($message);
	}
}
?>