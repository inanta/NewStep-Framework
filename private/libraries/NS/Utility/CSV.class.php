<?php
/*
	Copyright (C) 2008 - 2013 Inanta Martsanto
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

namespace NS\Utility;

use NS\Iterable;
use NS\Exception\IOException;

/**
 *CSV (Comma-separated values) file processing
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class CSV extends Iterable {
	private $_columns;

	function __construct($filename, $separator = ',') {
		if(!is_readable($filename)) {
			if(!is_file($filename)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $filename));

			throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $filename));
		}

		$rows = explode("\n", str_replace(array("\n\r", "\r") , "\n", file_get_contents($filename)));

		$columns =  preg_split("/" . $separator . "(?!(?:[^\\\"" . $separator . "]|[^\\\"]" . $separator . "[^\\\"])+\\\")/", $rows[0]);
		unset($rows[0]);

		foreach($columns as $index => $column) {
			$this->_columns[preg_replace("/[^A-Za-z0-9]/", '', $column)] = $index;
		}

		$iterator = 0;

		foreach($rows as $row) {
			$columns = preg_split("/" . $separator . "(?!(?:[^\\\"" . $separator . "]|[^\\\"]" . $separator . "[^\\\"])+\\\")/", $row);

			if(count($columns) != count($this->_columns)) continue;

			foreach($this->_columns as $column => $index) {
				$this->_collection[$iterator][$column] = $columns[$index];
			}

			++$iterator;
		}

		parent::__construct();
	}

	function getColumns() {
		$columns = array();

		foreach($this->_columns as $key => $value) {
			$columns[] = $$key;
		}

		return $columns;
	}

	function __get($k) {
		if(isset($this->_columns[$k])) {
			return ($this->_collection[$this->_iterator][$k]);
		}

		return parent::__get($k);
	}

	function __set($k, $v) {
		if(isset($this->_columns[$k])) {
			$this->_collection[$this->_iterator][$this->_columns[$k]] = $v;
		} else {
			parent::__set($k, $v);
		}
	}
}
?>