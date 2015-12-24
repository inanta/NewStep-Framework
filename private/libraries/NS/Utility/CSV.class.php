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

	function addColumn($column_name) {
		$this->addColumns($column_name);
	}

	function addColumns($columns) {
		if(!is_array($columns)) $columns = array($columns);
		
		$column_index = count($this->_columns);
		
		foreach($columns as $column) {
			$this->_columns[$column] = $column_index;
			++$column_index;
		}
	}

	function addData($data) {
		foreach($data as $datum) {
			if(!is_array($datum)) {
				$this->_addSingleData($data);
				break;
			}

			$this->_addSingleData($datum);	
		}
		
		parent::__construct();
	}
	
	function download($filename = 'CSV.csv') {
		$csv = 'sep=;';
		$csv .= "\r\n";

		foreach($this->_columns as $column => $index) {
			$csv .= $column . ';';
		}

		$csv .= "\r\n";

		foreach($this->_collection as $collection) {
			$csv .= implode(';', $collection);
			$csv .= "\r\n";
		}

		header('Content-Description: File Transfer');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($csv));

		ob_end_clean();
		echo $csv;

		exit;
	}
		
	function getColumns() {
		$columns = array();

		// $value is unused
		foreach($this->_columns as $key => $value) {
			$columns[] = $$key;
		}

		return $columns;
	}

	function readFile($filename, $separator = ',') {
		if(!is_readable($filename)) {
			if(!is_file($filename)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $filename));

			throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $filename));
		}

		$rows = explode("\n", str_replace(array("\n\r", "\r\n", "\r") , "\n", file_get_contents($filename)));
		$column_row = 0;
		
		if(($sep_pos = strpos($rows[$column_row], 'sep=')) !== false) {
			$separator = substr($rows[$column_row], $sep_pos + 4, 1);

			unset($rows[$column_row]);
			$column_row = 1;
		}

		$columns =  preg_split("/" . $separator . "(?!(?:[^\\\"" . $separator . "]|[^\\\"]" . $separator . "[^\\\"])+\\\")/", $rows[$column_row]);
		unset($rows[$column_row]);

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

	function _addSingleData($columns) {
		if(count($columns) != count($this->_columns)) return;

		$data = array();
		
		foreach($this->_columns as $column => $index) {
			// $this->_collection[][$column] = $columns[$index];
			$data[$column] = $columns[$index];
		    
		}

		$this->_collection[] = $data;
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