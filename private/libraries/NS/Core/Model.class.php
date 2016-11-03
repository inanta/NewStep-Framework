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

use NS\Database\ActiveRecord;
use NS\Exception\IOException;
use NS\Exception\ClassException;

/**
 *Model base class
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
abstract class Model extends ActiveRecord {
	static private $_modelsInstance = array();

	/**
	 *Get single database record
	 *
	 *@param mixed $columns Array of column names that will be retrieved
	 *@param mixed $condition Associated array or DatabaseFilterCriteria object to filter data that will be retrieved
	 *@param boolean $with_relation Determine if query will be including related table
	 *@return array
	 */
	function get($columns = null, $condition = null, $with_relation = true) {
		$this->find($columns, $condition, $with_relation);
		return $this->toArray(1);
	}

	/**
	 *Get all or filtered database records
	 *
	 *@param mixed $columns Array of column names that will be retrieved
	 *@param mixed $condition Associated array or DatabaseFilterCriteria object to filter data that will be retrieved
	 *@param array $order Associated array to sort data that will be retrieved
	 *@param integer $offset Offset of data that will be retrieved
	 *@param integer $limit Limit number of data that will be retrieved
	 *@param boolean $with_relation Determine if query will be including related table
	 *@return array
	 */
	function getAll($columns = null, $condition = null, $order = null, $offset = null, $limit = null, $with_relation = true) {
		$this->findAll($columns, $condition, $order, $offset, $limit, $with_relation);
		return $this->toArray();
	}

	/**
	 *Get single database record by primary key
	 *
	 *@param mixed $id Primary key value
	 *@throws ActiveRecordException If primary key is not defined in this object 
	 *@return array
	 */
	function getByPK($id, $with_relation = true) {
		$this->findByPK($id, $with_relation);
		return $this->toArray(1);
	}

	/**
	 *Get first database record
	 *
	 *@param mixed $columns Array of column names that will be retrieved
	 *@param boolean $with_relation Determine if query will be including related table
	 *@throws ActiveRecordException If primary key is not defined in this object 
	 *@return array
	 */
	function getFirst($columns = null, $condition = null, $with_relation = true) {
		$this->findFirst($columns, $with_relation);
		return $this->toArray(1);
	}

	/**
	 *Get last database record
	 *
	 *@param mixed $columns Array of column names that will be retrieved
	 *@param boolean $with_relation Determine if query will be including related table
	 *@throws ActiveRecordException If primary key is not defined in this object 
	 *@return array
	 */
	function getLast($columns = null, $with_relation = true) {
		$this->findLast($columns, $with_relation);
		return $this->toArray(1);
	}

	/**
	 *Delete object instance
	 *
	 *@param string $model Model class name
	 */
	static function deleteInstance($model) {
		unset(self::$_modelsInstance[$model]);
	}

	/**
	 *Create or retrieve object instance
	 *
	 *@return self
	 */
	static function getInstance($model, $path = '') {
		if(isset(self::$_modelsInstance[$model]['0'])) {
			if(self::$_modelsInstance[$model]['0']->_isUsedInRelation == null) {
				return self::$_modelsInstance[$model]['0'];
			} else {
				self::$_modelsInstance[$model][self::$_modelsInstance[$model]['0']->_isUsedInRelation] = clone self::$_modelsInstance[$model]['0'];
			}
		}

		$class = explode('\\', $model);

		if($path != '') $path .= '/';
		$path = NS_SYSTEM_PATH . '/' . Config::getInstance()->ApplicationFolder . '/models/' . $path . end($class) . '.php';

		if(!is_file($path)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $path));
		if(!is_readable($path)) throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $path));
		require_once($path);

		if(!class_exists($model)) throw new ClassException(array('code' => ClassException::CLASS_NOT_FOUND, 'class' => $model));

		return (self::$_modelsInstance[$model]['0'] = new $model);
	}
}
?>