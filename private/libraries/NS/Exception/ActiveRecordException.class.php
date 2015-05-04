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

class ActiveRecordException extends Exception {
	/**
	 *Active record not initialized error code
	 */
	const NOT_INITIALIZED = 1;
	/**
	 *Undefined primary key error code
	 */
	const UNDEFINED_PRIMARY_KEY = 2;
	/**
	 *Table not exist in database error code
	 */
	const TABLE_NOT_EXIST = 3;
	/**
	 *Column not exist in database error code
	 */
	const COLUMN_NOT_EXIST = 4;
	/**
	 *Relation table not exist error code
	 */
	const RELATION_NOT_EXISTS = 5;
	/**
	 *Instance is not active record error code
	 */
	const INSTANCE_NOT_ACTIVE_RECORD = 6;
	/**
	 *Instance is not active record error code
	 */
	const DATA_NOT_INITIALIZED_FOR_INSERT = 7;

	/**
	 *Exception constructor
	 *
	 *@param array $args Exception parameter to show appropriate message
	 */
	function __construct($args = array()) {
		$message = null;
		$this->ErrorCode = $args['code'];

		switch ($args['code']) {
			case self::NOT_INITIALIZED:
				$message = sprintf($this->_('Active record that using table [%s] is not initialized'), $args['table']); break;
			case self::UNDEFINED_PRIMARY_KEY:
				$message = sprintf($this->_('Primary key is not defined in table [%s]'), $args['table']); break;
			case self::TABLE_NOT_EXIST:
				$message = sprintf($this->_('Table [%s] is not exist in database [%s]'), $args['table'], $args['database']); break;
			case self::COLUMN_NOT_EXIST:
				$message = sprintf($this->_('Column name [%s] is not exist in table [%s]'), $args['column'], $args['table']); break;
			case self::RELATION_NOT_EXISTS:
				$message = sprintf($this->_('Relation for table [%s] is not exist'), $args['table']); break;
			case self::INSTANCE_NOT_ACTIVE_RECORD:
				$message = sprintf($this->_('Object instance [%s] added for relation is not valid relation object instance'), $args['object']); break;
			case self::DATA_NOT_INITIALIZED_FOR_INSERT:
				$message = sprintf($this->_('Cannot perform insert operation to table [%s] please make sure data is initialized'), $args['table']); break;
			default:
				if(!isset($args['code'])) $args['code'] = 'NO ERROR CODE RETURNED';
				$message = sprintf($this->_('Unknown active record error with code [%s]'), $args['code']);
		}

		parent::__construct($message);
	}
}
?>