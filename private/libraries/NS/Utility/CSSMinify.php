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

use NS\Object;
use NS\Exception\IOException;

/**
 *CSS minify
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class CSSMinify extends Object {
	private $_filename = null;

	function __construct($filename) {
		if(!is_readable($filename)) {
			if(!is_file($filename)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $filename));

			throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $filename));
		}

		$this->_filename = $filename;
	}

	function minify() {
		$contents = file_get_contents($this->_filename);
		$contents = preg_replace('/\/\*(.*?)\*\//is', '', $contents);
		$contents = preg_replace('/;?\s*}/', '}', $contents); 
		$contents = preg_replace('/\s*([\{:;,])\s*/', '$1', $contents); 
		$contents = preg_replace('/^\s*|\s*$/m', '', $contents); 
		$contents = preg_replace('/\n/', '', $contents);

		return $contents;
	}
}
?>