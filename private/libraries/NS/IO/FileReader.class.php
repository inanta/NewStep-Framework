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

namespace NS\IO;

use NS\BaseObject;
use NS\Exception\IOException;

/**
 *Handle file reading from system
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class FileReader extends BaseObject {
	private $_pos = 0, $_fp, $_length;

	function __construct($filename){
		if(!is_file($filename)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $filename));

		$this->createProperty('Length', @filesize($filename));
		$this->bindProperty('CurrentPosition', $this->_pos);
		$this->setReadOnlyProperties(array('Length', 'CurrentPosition'));

		$this->_fp = fopen($filename, 'rb');
		if(!$this->_fp) throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $filename));
	}

	function __destruct() {
		fclose($this->_fp);
	}

	function read($bytes = null) {
		if(!isset($bytes)) $bytes = $this->Length;

		fseek($this->_fp, $this->_pos);

		$data = '';
		while ($bytes > 0) {
			$chunk  = fread($this->_fp, $bytes);
			$data  .= $chunk;
			$bytes -= strlen($chunk);
		}
		$this->_pos = ftell($this->_fp);

		return $data;
	}

	function seek($pos) {
		fseek($this->_fp, $pos);
		return ($this->_pos = ftell($this->_fp));
	}
}
?>