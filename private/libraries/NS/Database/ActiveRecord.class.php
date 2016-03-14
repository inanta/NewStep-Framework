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

namespace NS\Database;

use NS\Exception\DatabaseException;
use NS\Exception\ActiveRecordException;

/**
 *Active record / ORM
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class ActiveRecord {
	const ORDER_ASC = 1;
	const ORDER_DESC = 2;

	const JOIN_INNER = 1;
	const JOIN_LEFT = 2;
	const JOIN_RIGHT = 3;

	/**
	 *@var Database Database driver connection object
	 */
	public $Database;

	/**
	 *@var string Database table name for current object
	 */
	public $Table;

	/**
	 *@var string Primary key column name
	 */
	public $PrimaryKey; 

	/**
	 *@var string Last queried command   
	 */
	public $LastQuery;

	/**
	 *@var DatabaseFunction Database function
	 */
	public $Function;

	protected $_isUsedInRelation = null;
	private $_multiRowResult = null, $_columns, $_originalColumns, $_lastQueriedColumns,
		$_numRows = 0, $_rowIterator, $_isDataInitialized = false, $_hasRelation = false, $_dataShadow,
		$_hasOne = array(), $_hasMany = array(), $_relationHasMany = array(),
		$_hasOneQuery = array(), $_hasManyQuery = array(), $_queryWithRelation = false, $_lastQueryFromRelation = false,
		$_column_aliases = array();

	/**
	 *Initialize active record with table name, primary key (if any) and database connection configuration (if any)
	 *
	 *@param string $table Database table that will be used for this object
	 *@param string $pk Auro increment primary key column name (if any)
	 *@param array $connection_data Associative array that contains database connection parameters
	 */
	function __construct($table, $pk = null, $connection_data = '') {
		$this->Table = $table;
		$this->PrimaryKey = $pk;
		$this->Function = new DatabaseFunction($this);

		$this->Database = Database::getInstance($connection_data);
		$this->initializeColumns();
		$this->_originalColumns = $this->_columns;
	}

	function __get($property) {
		if(array_key_exists($property, $this->_columns)) return $this->_columns[$property];

		throw new ActiveRecordException(array('code' => ActiveRecordException::COLUMN_NOT_EXIST, 'column' => $property, 'table' => $this->Table));
	}

	function __set($property, $value) {
		if(!array_key_exists($property, $this->_columns)) throw new ActiveRecordException(array('code' => ActiveRecordException::COLUMN_NOT_EXIST, 'column' => $property, 'table' => $this->Table));

		$this->_columns[$property] = $value;
	}

	function __toString() {
		return $this->Table;
	}

	/**
	 *Add alias to database table column
	 *
	 *@param string $column Database table column name
	 *@param string Alias for database table column
	 */
	function addColumnAlias($column, $alias) {
		$this->_column_aliases[$column] = $alias;
	}

	/**
	 *Set relation query for one to many relation
	 *
	 */
	function clearHasManyQuery($table) {
		unset($this->_hasManyQuery[$table]);
	}

	/**
	 *Count query result by condition
	 *
	 *@param array|DatabaseFilterCriteria $condition Filter or conditon
	 *@return integer Return number of rows in database table
	 */
	function count($condition = null, $with_relation = true) {
		reset($this->_columns);

		$constructed = array();

		if(count($this->_hasOne) != 0) {
			$this->_constructJoin($this, $with_relation, false, $constructed);
		}

		if(isset($constructed['join']) && count($constructed['join']) > 0) {
			$this->LastQuery = 'SELECT COUNT(' . $this->quote(key($this->_columns)) . ') FROM ' . $this->Table . ' ' . implode(' ', $constructed['join']);
		} else {
			$this->LastQuery = 'SELECT COUNT(' . $this->quote(key($this->_columns)) . ') FROM ' . $this->Table;
		}

		if($condition != null || $this->_hasRelation) {
			$this->LastQuery .= $this->_constructCondition($condition);
		}

		if(($row = $this->Database->fetchArray($this->Database->query($this->LastQuery)))
		) { return (int)$row[0]; }

		return $this->_numRows;
	}

	/**
	 *Create filter object for current active record
	 *
	 *@return DatabaseFilterCriteria
	 */
	function createFilterCriteria() {
		return new DatabaseFilterCriteria($this);
	}

	/**
	 *Delete single record in database
	 *
	 *@return boolean
	 */
	function delete($condition = null, $with_relation = false) {
		if($condition == null) {
			if(!$this->_isDataInitialized) throw new ActiveRecordException(array('code' => ActiveRecordException::NOT_INITIALIZED, 'table' => $this->Table));

			$this->_resetColumns();

			return $this->deleteAll($this->_columns, 1, $with_relation);
		}

		return $this->deleteAll($condition, 1, $with_relation);
	}

	/**
	 *Delete all records in database
	 *
	 *@return boolean
	 */
	function deleteAll($condition = null, $limit = null, $with_relation = false) {
		if(!empty($with_relation)) {
			if(count($this->_hasOne) > 0 || count($this->_hasMany) > 0) $this->findAll(null, (is_object($condition) ? (clone $condition) : $condition));
		}

		$this->LastQuery = 'DELETE FROM ' . $this->Table;

		if($condition != null) {
			$this->LastQuery .= $this->_constructCondition($condition);
		}

		if($limit != null) $this->LastQuery .= ' LIMIT ' . $limit;

		if($this->Database->query($this->LastQuery)) {
			while($this->hasNext() && !empty($with_relation)) {
				foreach($this->_hasOne as $relation) {
					if(is_array($with_relation) && !in_array($relation['ar']->Table, $with_relation)) continue;

					$relation['ar']->deleteAll(array($relation['fk'] => $this->{$relation['pk']}), 1);
				}

				foreach($this->_hasMany as $relation) {
					if($relation['ar']->findAll($relation['fk'], array($relation['fk'] => $this->{$relation['pk']}))) {
						while($relation['ar']->hasNext()) {
							$relation['ar']->deleteAll(array($relation['fk'] => $this->{$relation['fk']}));
							$relation['ar']->next();
						}
					}
				}
				
				$this->next();
			}

			return true;
		}

		return false;
	}

	/**
	 *Delete single record by Primary Key
	 *
	 *@return boolean
	 */
	function deleteByPK($id, $with_relation = false) {
		if($this->PrimaryKey == null) throw new ActiveRecordException(array('code' => ActiveRecordException::UNDEFINED_PRIMARY_KEY, 'table' => $this->Table));

		return $this->deleteAll(array($this->PrimaryKey => $id), 1, $with_relation);
	}

	/**
	 *Query single record from database
	 *
	 *@return boolean
	 */
	function find($column = '*', $condition = null, $with_relation = true) {
		return $this->_findAll($column, $condition, null, 1, null, $with_relation);
	}

	/**
	 *Query all records in database
	 *
	 *@return boolean
	 */
	function findAll($column = '*', $condition = null, $order = null, $offset = null, $limit = null, $with_relation = true, $distinct = false) {
		return $this->_findAll($column, $condition, $order, $offset, $limit, $with_relation, $distinct);
	}

	/**
	 *Query single record by Primary Key
	 *
	 *@throws ActiveRecordException If primary key is not defined in this object 
	 *@return boolean
	 */
	function findByPK($id, $with_relation = true) {
		if($this->PrimaryKey == null) throw new ActiveRecordException(array('code' => ActiveRecordException::UNDEFINED_PRIMARY_KEY, 'table' => $this->Table));

		return $this->_findAll(null, array($this->PrimaryKey => $id), null, 1, null, $with_relation);
	}

	/**
	 *Query first record in database
	 *
	 *@return boolean
	 */
	function findFirst($column = '*', $with_relation = true) {
		if($this->PrimaryKey == null) throw new ActiveRecordException(array('code' => ActiveRecordException::UNDEFINED_PRIMARY_KEY, 'table' => $this->Table));

		return $this->_findAll($column, null, array($this->PrimaryKey => self::ORDER_ASC), 1, null, $with_relation);
	}

	/**
	 *Query first record in database
	 * 
	 *@return boolean
	 */
	function findLast($column = '*', $with_relation = true) {
		if($this->PrimaryKey == null) throw new ActiveRecordException(array('code' => ActiveRecordException::UNDEFINED_PRIMARY_KEY, 'table' => $this->Table));

		return $this->_findAll($column, null, array($this->PrimaryKey => self::ORDER_DESC), 1, null, $with_relation);
	}

	function getHasOne($with_relation = true) {
		$return = array();
		$this->_getHasOne($this, $with_relation, $return);

		return $return;
	}

	/**
	 *Get all columns name for current active record (relation columns included)
	 *
	 *@return array
	 */
	function getAllColumns() {
		$columns = array_keys($this->_originalColumns);

		if(count($this->_hasOne) > 0) {
			foreach($this->_hasOne as $relation) {
				$columns = array_merge($columns, $relation['ar']->getColumns());
			}
		}

		return $columns;
	}

	/**
	 *Get all columns name for current active record (relation columns not included)
	 *
	 *@return array
	 */
	function getColumns() {
		return array_keys($this->_originalColumns);
	}

	/**
	 * 
	 * @param string $column Column name
	 * @return boolean
	 */
	function hasColumn($column, $with_relation = false) {
		if(!$with_relation) {
			if(array_key_exists($column, $this->_originalColumns)) {
				return true;
			}
		} else {
			if(array_key_exists($column, $this->_columns)) {
				return true;
			}
		}

		return false;
	}
	
	/**
	 *Set relation query for one to many relation
	 *
	 */
	function hasManyQuery($table, $columns = null, $condition = null, $order = null) {
		$this->_hasManyQuery[$table] = array('columns' => $columns, 'condition' => $condition, 'order' => $order);
	}

	/**
	 *Check if this object has next record
	 *
	 *@return boolean
	 */
	function hasNext() {
		return ($this->_rowIterator <= $this->_numRows);
	}

	/**
	 *Set relation query for one to many relation
	 *
	 */
	function hasOneQuery($table, $columns = null, $condition = null) {
		$this->_hasOneQuery[$table] = array('columns' => $columns, 'condition' => $condition);
	}

	/**
	 *Insert new record to database
	 *
	 *@return boolean
	 */
	function insert() {
		$this->_resetColumns();

		$column = array();
		foreach($this->_columns as $key => $value) { if($value !== null && $key != $this->PrimaryKey) $column[$key] = ($value !== null ? "'" . $this->Database->escape($value) . "'" : 'NULL'); }

		if(count($column) == 0) throw new ActiveRecordException(array('code' => ActiveRecordException::DATA_NOT_INITIALIZED_FOR_INSERT, 'table' => $this->Table));

		if($this->Database->query($this->LastQuery = "INSERT INTO " . $this->Table . " (" . $this->Database->FieldQuote . implode($this->Database->FieldQuote . ', ' . $this->Database->FieldQuote, array_keys($column)) . $this->Database->FieldQuote . ") VALUES (" . implode(', ', $column) . ")")) {
			if($this->PrimaryKey != null) return $this->{$this->PrimaryKey} = $this->Database->lastInsertID();

			return true;
		}

		return false; 
	}

	/**
	 *Check if current record is first record
	 *
	 *@return boolean
	 */
	function isFirst() {
		return ($this->_rowIterator == 1);
	}

	/**
	 *Check if current record is last record
	 *
	 *@return boolean
	 */
	function isLast() {
		return ($this->_rowIterator == $this->_numRows);
	}

	/**
	 *Move to next record
	 *
	 */
	function next() {
		if($this->hasNext()) {
			if(!$this->isLast()) {
				$data = $this->Database->fetchAssoc($this->_multiRowResult);

				foreach($this->_lastQueriedColumns as $column) {
					if(isset($data[$column])) $this->_columns[$column] = $data[$column];
					else $this->_columns[$column] = null;
				}

				if($this->PrimaryKey != null) $this->_columns[$this->PrimaryKey] = $data[$this->PrimaryKey];
				else $this->_dataShadow = $this->_columns;

				if($this->_queryWithRelation) {
					if(count($this->_hasMany) > 0) {
						foreach($this->_hasMany as $relation) {
							$relation['ar']->_findAll(
								isset($this->_hasManyQuery[$relation['ar']->Table]['columns']) ? $this->_hasManyQuery[$relation['ar']->Table]['columns'] : null,
								isset($this->_hasManyQuery[$relation['ar']->Table]['condition']) && is_array($this->_hasManyQuery[$relation['ar']->Table]['condition']) ? $this->_hasManyQuery[$relation['ar']->Table]['condition'] + array($relation['fk'] => $this->{$relation['pk']}) : array($relation['fk'] => $this->{$relation['pk']}),
								isset($this->_hasManyQuery[$relation['ar']->Table]['order']) ? $this->_hasManyQuery[$relation['ar']->Table]['order'] : null,
								null, null, true, false, true
							);
	
							$this->_columns[$relation['ar']->Table] = $relation['ar'];
							if(!in_array($relation['ar']->Table, $this->_lastQueriedColumns)) $this->_lastQueriedColumns[] = $relation['ar']->Table;
						}
					}
	
					if(count($this->_relationHasMany) > 0) {
						foreach($this->_relationHasMany as $relation) {
							$relation['ar']->_findAll(
								isset($this->_hasManyQuery[$relation['ar']->Table]['columns']) ? $this->_hasManyQuery[$relation['ar']->Table]['columns'] : null,
								isset($this->_hasManyQuery[$relation['ar']->Table]['condition']) && is_array($this->_hasManyQuery[$relation['ar']->Table]['condition']) ? $this->_hasManyQuery[$relation['ar']->Table]['condition'] + array($relation['fk'] => $this->{$relation['pk']}) : array($relation['fk'] => $this->{$relation['pk']}),
								isset($this->_hasManyQuery[$relation['ar']->Table]['order']) ? $this->_hasManyQuery[$relation['ar']->Table]['order'] : null,
								null, null, true, false, true
							);
	
							$this->_columns[$relation['ar']->Table] = $relation['ar'];
							if(!in_array($relation['ar']->Table, $this->_lastQueriedColumns)) $this->_lastQueriedColumns[] = $relation['ar']->Table;
						}
					}
				}
			}

			++$this->_rowIterator;
		}

		if(!$this->hasNext()) $this->_isDataInitialized = false;
	}

	/**
	 *Populate array value to current record column
	 *
	 */
	function populate($columns, $explicit = true) {
		if(is_array($columns)) {
			if($explicit) foreach($columns as $k => $v) $this->{$k} = $v;
			else foreach($this->_columns as $k => $v)  if(isset($columns[$k])) $this->{$k} = $columns[$k];
		}
	}

	/**
	 * Manual query to database
	 * 
	 * @param string $query Query that will be executed
	 * @return boolean
	 */
	function query($query) {
		$this->_multiRowResult = $this->Database->query($this->LastQuery = $query);
		if(($this->_numRows = $this->Database->numRows($this->_multiRowResult))) {
			$column_count = $this->Database->numFields($this->_multiRowResult);

			$this->_lastQueriedColumns = array();

			for ($i = 0; $i < $column_count; $i++) {
				$this->_lastQueriedColumns[] = $this->Database->fieldName($this->_multiRowResult, $i);
			}

			$this->_isDataInitialized = true;
			$this->_rowIterator = 0;
			$this->next();

			return true;
		}

		$this->_rowIterator = 1;
		$this->_isDataInitialized = false;

		return false;
	}

	/**
	 *Quote selected column with native database quote character
	 *
	 *@return string
	 */
	function quote($column, $with_table = true) {
		return (($with_table ? $this->Database->FieldQuote . $this->Table . $this->Database->FieldQuote . '.' : '') . $this->Database->FieldQuote . $column . $this->Database->FieldQuote);
	}

	/**
	 *Save current record, if current record not found in database it will insert new record, otherwise the record in database will be updated
	 *
	 *@return boolean
	 */
	function save() {
		if($this->_isDataInitialized && !$this->_lastQueryFromRelation) return $this->update();
		else return $this->insert();
	}

	/**
	 *Convert current record to array
	 *
	 *@return array
	 */
	function toArray($limit = null) {
		$array = array();
		
		if(!$this->_isDataInitialized) return $array;

		if($this->PrimaryKey != null) unset($this->_lastQueriedColumns[0]);

		if($limit != 1) {
			if($limit == null || $limit > $this->_numRows) $limit = $this->_numRows;

			$single = (count($this->_lastQueriedColumns) == 1);

			for($i = ($this->_rowIterator - 1); $i < $limit; ++$i) {
				$key = ($this->PrimaryKey != null ? $this->_columns[$this->PrimaryKey] : $i);

				if($single) {
					$array[$key] = (is_string($this->_columns[end($this->_lastQueriedColumns)]) ? $this->_columns[end($this->_lastQueriedColumns)] : ($this->_columns[end($this->_lastQueriedColumns)] instanceof ActiveRecord ? $this->_columns[end($this->_lastQueriedColumns)]->toArray() : null));
				} else {
					foreach($this->_lastQueriedColumns as $column) {
						$array[$key][$column] = (is_string($this->_columns[$column]) ? $this->_columns[$column] : ($this->_columns[$column] instanceof ActiveRecord ? $this->_columns[$column]->toArray() : null));
					}
				}
	
				$this->next();
			}
		} else {
			foreach($this->_lastQueriedColumns as $column) {
				$array[$column] = (is_string($this->_columns[$column]) ? $this->_columns[$column] : ($this->_columns[$column] instanceof ActiveRecord ? $this->_columns[$column]->toArray() : null));
			}

			$this->next();
		}

		return $array;
	}

	/**
	 *Update single record in database
	 *
	 *@throws ActiveRecordException If object data never been initialized before
	 *@return boolean
	 */
	function update($values = null, $condition = null) {
		if($condition == null) {
			if(!$this->_isDataInitialized) throw new ActiveRecordException(array('code' => ActiveRecordException::NOT_INITIALIZED, 'table' => $this->Table));

			$this->_resetColumns();
			return $this->updateAll($this->_columns, (isset($this->PrimaryKey) ? array($this->PrimaryKey => $this->_columns[$this->PrimaryKey]) : $this->_dataShadow) , 1);
		}

		return $this->updateAll($values, $condition, 1);
	}

	/**
	 *Update all records in database
	 *
	 *@return boolean
	 */
	function updateAll($values = null, $condition = null, $limit = null) {
		$column = array();

		foreach($values as $key => $value) {
			if($value !== null && $key != $this->PrimaryKey) {
				$column[] = $this->quote($key) . " = '" . $this->Database->escape($value) . "'";
			}
		}

		$this->LastQuery = "UPDATE " . $this->Table . " SET " . implode(', ', $column);

		if($condition != null) $this->LastQuery .= $this->_constructCondition($condition);
		if($limit != null) $this->LastQuery .= ' LIMIT ' . $limit;

		return $this->Database->query($this->LastQuery);
	}

	/**
	 *Update single record by Primary Key
	 *
	 *@return boolean
	 */
	function updateByPK($values, $id) {
		if($this->PrimaryKey == null) throw new ActiveRecordException(array('code' => ActiveRecordException::UNDEFINED_PRIMARY_KEY, 'table' => $this->Table));

		return $this->updateAll($values, array($this->PrimaryKey => $id), 1);
	}

	/**
	 *Get active record object for relation
	 *
	 *@return ActiveRecord
	 */
	protected function getRelationObject($table) {
		if(isset($this->_hasOne[$table])) {
			$this->_hasOne[$table]['ar']->_isDataInitialized = false;
			$this->_hasOne[$table]['ar']->_resetColumns();

			return $this->_hasOne[$table]['ar'];
		} else if(isset($this->_hasMany[$table])) {
			$this->_hasMany[$table]['ar']->_isDataInitialized = false;
			$this->_hasMany[$table]['ar']->_resetColumns();

			return $this->_hasMany[$table]['ar'];
		}

		throw new ActiveRecordException(array('code' => ActiveRecordException::RELATION_NOT_EXISTS, 'table' => $table));
	}

	/**
	 *Add one to many database relation
	 *
	 */
	protected function hasMany($ar, $pk, $fk = null) {
		$this->_addRelation('_hasMany', $ar, $pk, $fk);
	}

	/**
	 *Add one to one database relation
	 *
	 */
	protected function hasOne($ar, $pk, $fk = null, $join_type = self::JOIN_INNER) {
		$this->_addRelation('_hasOne', $ar, $pk, $fk, $join_type);
	}

	/**
	 *Initialize database table columns for current object
	 *
	 */
	protected function initializeColumns($columns = null, $table = null) {
		try {
			if($table == null) $table = $this->Table;

			if($columns == null) {
				$result = $this->Database->query("SELECT * FROM " . $table . " LIMIT 1"); 

				for($i = 0, $j = $this->Database->numFields($result); $i < $j; ++$i) { $this->_columns[$this->Database->fieldName($result, $i)] = null; }
			} else {
				foreach($columns as $column) $this->_columns[$column] = null;
			}

			if($this->PrimaryKey != null) if(!array_key_exists($this->PrimaryKey, $this->_columns)) throw new ActiveRecordException(array('code' => ActiveRecordException::COLUMN_NOT_EXIST, 'column' => $this->PrimaryKey, 'table' => $this->Table));
		} catch(DatabaseException $ex) {
			if($ex->ErrorCode == DatabaseException::QUERY_TABLE_NOT_EXIST) throw new ActiveRecordException(array('code' => ActiveRecordException::TABLE_NOT_EXIST, 'table' => $this->Table, 'database' => $this->Database->Database));
			else throw $ex;
		}
	}

	/**
	 *Add one to many database relation
	 *
	 */
	protected function removeHasMany($ar) {
		$this->_removeRelation('_hasMany', $ar);
	}

	/**
	 *Add one to one database relation
	 *
	 */
	protected function removeHasOne($ar) {
		$this->_removeRelation('_hasOne', $ar);
	}

	private function _addRelation($relation, &$ar, &$pk, &$fk, &$join_type = null) {
		if(!$ar instanceof ActiveRecord) throw new ActiveRecordException(array('code' => ActiveRecordException::INSTANCE_NOT_ACTIVE_RECORD, 'object' => get_class($ar)));

		$this->_hasRelation = true;
		$ar->_isUsedInRelation = $this->Table;

		if($fk == null) $fk = $pk;
		$this->{$relation}[$ar->Table] = array('ar' => $ar, 'pk' => $pk, 'fk' => $fk, 'join_type' => $join_type);
	}

	function _constructColumn($relation) {
		$ar_key_columns = isset($this->_hasOneQuery[$relation['ar']->Table]) ? (isset($this->_hasOneQuery[$relation['ar']->Table]['columns']) ? $this->_hasOneQuery[$relation['ar']->Table]['columns'] : $relation['ar']->getColumns()) : $relation['ar']->getColumns();
		$ar_columns = isset($this->_hasOneQuery[$relation['ar']->Table]) ? (isset($this->_hasOneQuery[$relation['ar']->Table]['columns']) ? array_fill_keys($this->_hasOneQuery[$relation['ar']->Table]['columns'], null) : array_fill_keys($relation['ar']->getColumns(), null)) : array_fill_keys($relation['ar']->getColumns(), null);

		$this->_columns = array_merge($this->_columns, $ar_columns);
		$this->_lastQueriedColumns = array_merge($this->_lastQueriedColumns, $ar_key_columns);

		$column = '';

		foreach($ar_key_columns as $ar_key_column) {
			$column .= ', ' . $relation['ar']->quote($ar_key_column);

			if(isset($relation['ar']->_column_aliases[$ar_key_column])) {
				$column .= ' AS ' . $relation['ar']->quote($relation['ar']->_column_aliases[$ar_key_column], false);

				$this->_lastQueriedColumns[] = $relation['ar']->_column_aliases[$ar_key_column];
			}
		}

		return $column;
	}

	private function _constructCondition($condition) {
		if(is_array($condition)) {
			foreach($condition as $k => $v) {
				if($v instanceof DatabaseFilterCriteria) {
					$condition[$k] = '' . $v;

					if($condition[$k] === '') unset ($condition[$k]);
				} else {
					$condition[$k] = $this->quote($k) . " = '" . $this->Database->escape($v) . "'";
				}
			}

			if(!empty($condition)) {
				return (' WHERE ' . implode(' AND ', $condition));
			}
		} else if($condition instanceof DatabaseFilterCriteria) {
			if($condition != '') return (' WHERE ' . $condition);
		}

		return '';
	}

	function _constructJoin($parent_relation, $with_relation, $construct_column, &$return) {
		if(count($parent_relation->_hasOne) > 0) {
			foreach($parent_relation->_hasOne as $relation) {
				if($with_relation === false || (is_array($with_relation) && !in_array($relation['ar']->Table, $with_relation))) continue;

				$return['join'][] = $this->_getJoinType($relation['join_type']) . ' ' . $relation['ar']->quote($relation['ar']->Table, false) . ' ON ' . $relation['ar']->quote($relation['fk']) . ' = ' . $parent_relation->quote($relation['pk']);

				if($construct_column) {
					$return['column'] .= $this->_constructColumn($relation);
				}

				if(count($relation['ar']->_hasMany) > 0) {
					foreach($relation['ar']->_hasMany as $has_many) {
						$this->_relationHasMany[$has_many['ar']->Table] = $has_many;
					}
				}

				$this->_constructJoin($relation['ar'], $with_relation, $construct_column, $return);
			}
		}
	}

	private function _findAll($column = '*', $condition = null, $order = null, $offset = null, $limit = null, $with_relation = true, $distinct = false, $query_from_relation = false) {
		$this->_lastQueryFromRelation = $query_from_relation;
		$this->_queryWithRelation = $with_relation;

		$this->_resetColumns();

		$constructed = array('column' => '');

		if(is_string($column) || $column == null) {
			if($column == '*' || $column == null) $column = array_keys($this->_columns);
			else $column = array($column);
		}

		if($this->PrimaryKey != null) $column = array_merge(array($this->PrimaryKey), $column);
		$this->_lastQueriedColumns = array();

		foreach($column as $k => $v) {
			if(!(is_object($v) && $v instanceof DatabaseFunction) && !array_key_exists($v, $this->_columns)) throw new ActiveRecordException(array('code' => ActiveRecordException::COLUMN_NOT_EXIST, 'column' => $v, 'table' => $this->Table));

			if(is_object($v)) {
				$v .= '';
				$column[$k] = $v;
			} else {
				$column[$k] = $this->quote($v);
			}

			if(isset($this->_column_aliases[$v])) {
				$column[$k] .= ' AS ' . $this->quote($this->_column_aliases[$v], false);
				$this->_lastQueriedColumns[] = $this->_column_aliases[$v];
			} else {
				$this->_lastQueriedColumns[] = $v;
			}
		}

		if(count($this->_hasOne) == 0) {
			$column = implode(', ', $column);
		} else {
			$column = implode(', ', $column);

			$constructed = array('column' => '');
			$this->_constructJoin($this, $with_relation, true, $constructed);

			$column .= $constructed['column'];
		}

		if(isset($constructed['join'])) {
			$this->LastQuery = 'SELECT ' . ($distinct ? 'DISTINCT ' : '') . $column . ' FROM ' . $this->Table . ' ' . implode(' ', $constructed['join']);
		} else {
			$this->LastQuery = 'SELECT ' . ($distinct ? 'DISTINCT ' : '') . $column . ' FROM ' . $this->Table;
		}

		if($condition != null || $this->_hasRelation) {
			$this->LastQuery .= $this->_constructCondition($condition);
		}

		if(is_array($order)) {
			foreach($order as $k => $v) {
				if(is_numeric($k)) {
					$order[$k] = $v;
					continue;
				}

				$order[$k] = $this->quote($k) . ($v == self::ORDER_DESC ? ' DESC' : ' ASC');
			}

			$this->LastQuery .= ' ORDER BY ' . implode(', ', $order);
		}

		if($offset !== null) {
			$this->LastQuery .= ' LIMIT ' . $offset;
			if($limit != null) $this->LastQuery .= ', ' . $limit;
		}

		$this->_multiRowResult = $this->Database->query($this->LastQuery);
		if($this->_numRows = $this->Database->numRows($this->_multiRowResult)) {
			$this->_isDataInitialized = true;
			$this->_rowIterator = 0;
			$this->next();

			return true;
		}

		$this->_rowIterator = 1;
		$this->_isDataInitialized = false;
    
		return false;
	}

	private function _getHasOne($parent_relation, $with_relation, &$return) {
		if(count($parent_relation->_hasOne) > 0) {
			foreach($parent_relation->_hasOne as $relation) {
				if($with_relation === false || (is_array($with_relation) && !in_array($relation['ar']->Table, $with_relation))) continue;

				$return[] = $relation['ar'];

				$this->_getHasOne($relation['ar'], $with_relation, $return);
			}
		}
	}


	/**
	 * Get join type
	 * 
	 */
	private function _getJoinType($join_type) {
		switch($join_type) {
			case self::JOIN_LEFT:
				return 'LEFT JOIN';
			case self::JOIN_RIGHT:
				return 'RIGHT JOIN';
			case self::JOIN_INNER:
			default:
				return 'INNER JOIN';
		}
	}

	/**
	 *Remove database relation
	 *
	 */
	private function _removeRelation($relation, &$ar) {
		if(!$ar instanceof ActiveRecord) throw new ActiveRecordException(array('code' => ActiveRecordException::INSTANCE_NOT_ACTIVE_RECORD, 'object' => get_class($ar)));

		if(empty($this->_hasMany) && empty($this->_hasOne)) $this->_hasRelation = false;
		$ar->_isUsedInRelation = null;

		if(isset($this->{$relation}[$ar->Table])) unset($this->{$relation}[$ar->Table]);
	}

	/**
	 *Reset columns to original value and remove all relation columns
	 *
	 */
	private function _resetColumns() {
		foreach($this->_columns as $column => $value) {
			if(!array_key_exists($column, $this->_originalColumns)) {
				unset($this->_columns[$column]);

				if($this->PrimaryKey == null) unset($this->_dataShadow[$column]);
			}
		}

		reset($this->_columns);
	}
}
?>