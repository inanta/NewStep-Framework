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

use NS\BaseObject;
use NS\Exception\ActiveRecordException;

/**
 *Filtering active record result
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class DatabaseFilterCriteria extends BaseObject {
	const EXP_AND = 1;
	const EXP_OR = 2;

	private $_conditions, $_ar, $_exp = ' AND ';

	/**
	*Initialize filter criteria
	*
	*/
	function __construct(&$ar) { $this->_ar =& $ar; }

	/**
	*Filter query result that have equal value
	*
	*/
	function equals($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " = '" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Filter query result that not have equal value
	*
	*/
	function notEquals($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " != '" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Filter query result that contain selected text
	*
	*/
	function contains($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " LIKE '%" . $this->_ar->Database->escape($value) . "%'");
	}

	/**
	*Filter query result that start with selected text
	*
	*/
	function startsWith($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " LIKE '" . $this->_ar->Database->escape($value) . "%'");
	}

	/**
	*Filter query result that end with selected text
	*
	*/
	function endsWith($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " LIKE '%" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Filter query result that in selected array
	*
	*/
	function in($column, $value) {
		if(is_array($value)) {
			foreach($value as $k => $v) {
				$value[$k] = $this->_ar->Database->escape($v);
			}

			if(($in = @implode("', '", $value)) != '') $this->addCondition($column, $this->_ar->quote($column) . " IN ('" . $in . "')");
		}
	}

	/**
	*Filter query result that not in selected array
	*
	*/
	function notIn($column, $value) {
		foreach($value as $k => $v) {
			$value[$k] = $this->_ar->Database->escape($v);
		}

		if(($not_in = @implode("', '", $value)) != '') $this->addCondition($column, $this->_ar->quote($column) . " NOT IN ('" . $not_in . "')");
	}

	/**
	*Filter query result that greater than selected value
	*
	*/
	function greaterThan($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " > '" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Filter query result that less that selected value
	*
	*/
	function lessThan($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " < '" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Filter query result that greater than selected value
	*
	*/
	function greaterThanOrEquals($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " >= '" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Filter query result that less that selected value
	*
	*/
	function lessThanOrEquals($column, $value) {
		$this->addCondition($column, $this->_ar->quote($column) . " <= '" . $this->_ar->Database->escape($value) . "'");
	}

	/**
	*Add custom filter
	*
	*/
	function addCondition($column, $condition) {
		// if(!array_key_exists($column, $this->_ar->getAllColumns())) throw new ActiveRecordException(array('code' => ActiveRecordException::COLUMN_NOT_EXIST, 'column' => $column, 'table' => $this->_ar->Table));
		if(!in_array($column, $this->_ar->getAllColumns())) throw new ActiveRecordException(array('code' => ActiveRecordException::COLUMN_NOT_EXIST, 'column' => $column, 'table' => $this->_ar->Table));

		$this->_conditions[md5($condition)] = $condition;
	}

	/**
	*Change expression to OR or AND
	*
	*/
	function setExpression($exp) { $this->_exp =  ($exp == self::EXP_OR ? ' OR ' : ' AND '); }

	/**
	*Merge another filter criteria to this object
	*
	*/
	function merge($fc) {
		if(!$fc instanceof DatabaseFilterCriteria) {
			// TODO: Add error message or create new exception class
			throw new Exception();
		}

		if($this->_ar->Table != $fc->_ar->Table) {
			// TODO: Add error message or create new exception class
			throw new Exception();
		}

		$this->_conditions = array_merge($this->_conditions, $fc->_conditions);
	}

	function __toString() {
		if(count($this->_conditions) == 0) return '';

		return ('(' . implode($this->_exp, $this->_conditions) . ')');
	}
}
?>