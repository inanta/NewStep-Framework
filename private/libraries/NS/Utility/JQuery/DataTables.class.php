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

namespace NS\Utility\JQuery;

use NS\Object;
use NS\Database\DatabaseFilterCriteria;
use NS\Database\ActiveRecord;

/**
 * JQuery Data Table processing
 *
 * @author Inanta Martsanto <inanta@inationsoft.com>
 * @property boolean $Debug Return debug information
 * @property-read integer $Draw Number of draw request
 */
class DataTables extends Object {
	const REQUEST_POST = 0;
	const REQUEST_GET = 1;

	private $_ar;
	private $_columns;
	private $_length;
	private $_order = array();
	private $_orderableColumns;
	private $_search;
	private $_searchableColumns;
	private $_start;

	/**
	 * 
	 * @param ActiveRecord $ar
	 */
	function __construct($ar, $request = self::REQUEST_POST) {
		$this->_ar = $ar;

		$data = array();

		if($request == self::REQUEST_GET) {
			$data = $_GET;
		} else {
			$data = $_POST;
		}

		$this->createProperties(array(
			'Draw' => $data['draw'],
			'Debug' => false
		));

		$this->setReadOnlyProperties(array(
			'Draw'
		));

		foreach($data['columns'] as $key => $columns) {
			$this->_columns[] = $columns['data'];

			if($columns['orderable'] === 'true') {
				$this->_orderableColumns[$key] = $columns['data'];
			}

			if($columns['searchable'] === 'true') {
				$this->_searchableColumns[$key] = $columns['data'];
			}
		}

		$this->_length = $data['length'];
		$this->_start = $data['start'];

		foreach ($data['order'] as $order) {
			if(isset($this->_orderableColumns[$order['column']])) {
				$this->_order[$order['column']] = $order['dir'];
			}
		}

		$this->_search = $data['search']['value'];
	}

	function getAllOrderableColumns() {
		return $this->_orderableColumns;
	}

	function getAllSearchableColumns() {
		return $this->_searchableColumns;
	}

	function isOrderableColumn($column_name) {
		return in_array($column_name, $this->_orderableColumns);
	}

	function isSearchableColumn($column_name) {
		return in_array($column_name, $this->_searchableColumns);
	}

	function setSearchableColumns($columns = array()) {
		$this->_searchableColumns = $columns;
	}

	function setOrderableColumns($columns = array()) {
		$this->_orderableColumns = $columns;
	}

	/**
	 * 
	 * @param array $additional_condition Additional database query condition
	 * @return array Database records result
	 */
	function getResult($additional_condition = array(), $method = 'getAll', $with_relation = false) {
		$result = array();

		$criteria = $this->_ar->createFilterCriteria();
		$criteria->setExpression(DatabaseFilterCriteria::EXP_OR);

		foreach($this->getAllSearchableColumns() as $column) {
			if($this->_ar->hasColumn($column) && $this->_search != '') {
				$criteria->contains($column, $this->_search);
			}
		}

		$orders = null;

		foreach($this->_order as $key => $dir) {
			if($this->_ar->hasColumn($this->_orderableColumns[$key])) {
				$orders[$this->_orderableColumns[$key]] = ($dir == 'asc' ? ActiveRecord::ORDER_ASC : ActiveRecord::ORDER_DESC);
			}
		}
		
		if($with_relation) {
			$columns = $this->_ar->getColumns();

			foreach($this->_ar->getHasOne() as $has_one) {
				$has_one_criteria = $has_one->createFilterCriteria();
				$has_one_criteria->setExpression(DatabaseFilterCriteria::EXP_OR);

				foreach($this->getAllSearchableColumns() as $column) {
					if($has_one->hasColumn($column) && $this->_search != '') {
						$criteria->addCondition(key($columns), $has_one->quote($column) . " LIKE '%" . $this->_search . "%'");
					}
				}
			}
		}

		$result['draw'] = $this->Draw;

		$result['recordsTotal'] = $this->_ar->count($additional_condition);
		if($this->Debug) $result['rt_query'] = $this->_ar->LastQuery;

		$additional_condition[] = $criteria;

		$result['recordsFiltered'] = $this->_ar->count($additional_condition);
		if($this->Debug) $result['rf_query'] = $this->_ar->LastQuery;

		$items = $this->_ar->{$method}(null, $additional_condition, $orders, $this->_start, $this->_length);
		$data = array();

		foreach($items as $item) {
			$data[] = $item;
		}
		
		$result['data'] = $data;
		if($this->Debug) $result['data_query'] = $this->_ar->LastQuery;

		return $result;
	}
}
?>