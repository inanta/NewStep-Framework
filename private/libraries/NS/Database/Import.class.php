<?php
/*
	Copyright (C) 2008 - 2016 Inanta Martsanto
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

use NS\SingletonObject;
use NS\Exception\IOException;

class Import extends SingletonObject {
	private $_conn = array(), $_quries = array();

	function __construct($conn = null) {
		$this->_conn = $conn;

		$this->createProperties(array(
			'File' => null
		));
	}

	function import($file) {
		$executed = 0;

		$this->File = $file;
		$this->_process();

		$db = Database::getInstance($this->_conn);

		foreach($this->_quries as $query) {
			if($db->query($query)) {
				++$executed;
			}
		}

		return $executed;
	}
	
	private function _getFileContents() {
		if(!is_file($this->File)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $this->File));
		if(!is_readable($this->File)) throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $this->File));

		return file_get_contents($this->File);
	}

	private function _process() {
		$lines = explode("\n", $this->_getFileContents());
		$query_counter = 0;

		foreach($lines as $line) {
			if(trim($line) == '' || strpos($line, '--') !== false) {
				continue;
			}

			if(!isset($this->_quries[$query_counter])) $this->_quries[$query_counter] = '';

			$this->_quries[$query_counter] .= $line;

			if(preg_match("/(.*);/", $line)) {
				++$query_counter;
			}
		}
	}

	static function getInstance() {
		return self::createInstance(__CLASS__);
	}
}
?>
