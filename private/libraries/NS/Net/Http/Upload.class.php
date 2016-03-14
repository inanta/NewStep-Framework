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

namespace NS\Net\Http;

use NS\Object;
use NS\Exception\UploadException;
use NS\Exception\IOException;

/**
 *Handle file upload from client
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $Error Error code if upload proccess fail
 *@property string $FileExtension File extension of uploaded file
 *@property string $FileName File name of uploaded file
 *@property string $Size Size of uploaded file
 *@property string $TemporaryFileName Temporary file name of uploaded file
 *@property string $Type MIME file type of uploaded file
 */
class Upload extends Object {
	private $_postName, $_fileIterator, $_fileNumber;

	function __construct($name, $strict = true) {
		if(!isset($_FILES[$name]) && $strict) throw new UploadException(array('code' => UploadException::UNDEFINED_FILES, 'variable' => $name));
		
		$this->_postName = $name;
		$this->_fileIterator = -1;
		
		$this->createProperties(array(
			'Error' => 0,
			'FileExtension' => '',
			'FileName' => '',
			'Size' => 0,
			'TemporaryFileName' => '',
			'Type' => ''
		));

		if(@is_array($_FILES[$name]['name'])) $this->_fileNumber = (count($_FILES[$name]['name']) - 1);

		$this->next();
	}

	function save($file = null, $folder = NS_PUBLIC_PATH) {
		if($file == null) $file = $_FILES[$this->_postName]['name'][$this->_fileIterator];
		if($_FILES[$this->_postName]['error'][$this->_fileIterator] != UPLOAD_ERR_OK && $_FILES[$this->_postName]['error'][$this->_fileIterator] != UPLOAD_ERR_NO_FILE) throw new UploadException(array('code' => $_FILES[$this->_postName]['error'][$this->_fileIterator]));
		if(!is_writeable($folder)) throw new IOException(array('code' => IOException::DIRECTORY_NOT_WRITEABLE, 'directory' => $folder));

		return move_uploaded_file($_FILES[$this->_postName]['tmp_name'][$this->_fileIterator], $folder . '/' . $file);
	}

	function hasNext() {
		return ($this->_fileIterator <= $this->_fileNumber);
	}

	function isLast() {
		return ($this->_fileIterator == $this->_fileNumber);
	}

	function isFirst() {
		return ($this->_fileIterator == 0);
	}

	function next() {
		if($this->hasNext()) {
			++$this->_fileIterator;

			$this->Error = $_FILES[$this->_postName]['error'][$this->_fileIterator];
			$this->FileName = $_FILES[$this->_postName]['name'][$this->_fileIterator];
			$this->Size = $_FILES[$this->_postName]['size'][$this->_fileIterator];
			$this->TemporaryFileName = $_FILES[$this->_postName]['tmp_name'][$this->_fileIterator];
			$this->Type = $_FILES[$this->_postName]['type'][$this->_fileIterator];

			$filename_part = explode('.', $_FILES[$this->_postName]['name'][$this->_fileIterator]);
			$this->FileExtension = end($filename_part);
		}
	}
}
?>
