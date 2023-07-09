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
 *Handle file writing to system
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class FileWriter extends BaseObject {
	const MODE_WRITE = 'w';
	const MODE_APPEND = 'a';

	private $_fp, $_isClosed = false;

	function __construct($filename, $mode = self::MODE_WRITE) {
		$this->_fp = @fopen($filename, $mode);
		if(!$this->_fp) throw new IOException(array('code' => IOException::FILE_NOT_WRITEABLE, 'filename' => $filename));
	}

	function __destruct() {
		$this->close();
	}

	function write($contents) {
		fwrite($this->_fp, $contents);
	}

	function close() {
		if(!$this->_isClosed) fclose($this->_fp);
		return ($this->_isClosed = true);
	}
}
?>