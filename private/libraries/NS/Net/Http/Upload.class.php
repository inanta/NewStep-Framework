<?php
/*
	Copyright (C) 2008 - 2024 Inanta Martsanto
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

use NS\BaseObject;
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
class Upload extends BaseObject
{
	private $_postName, $_fileIterator, $_fileNumber;

	function __construct($name, $strict = true)
	{
		if (!isset($_FILES[$name]) && $strict)
			throw new UploadException(
				[
					'code' => UploadException::UNDEFINED_FILES,
					'variable' => $name
				]
			);

		$this->_postName = $name;
		$this->_fileIterator = -1;

		$this->createProperties(
			[
				'Error' => 0,
				'FileExtension' => '',
				'FileName' => '',
				'Name' => '',
				'Size' => 0,
				'TemporaryFileName' => '',
				'Type' => ''
			]
		);

		if (@is_array($_FILES[$name]['name']))
			$this->_fileNumber = (count($_FILES[$name]['name']) - 1);

		$this->next();
	}

	function save($file = null, $folder = NS_PUBLIC_PATH)
	{
		if ($file == null)
			$file = $_FILES[$this->_postName]['name'][$this->_fileIterator];

		if ($_FILES[$this->_postName]['error'][$this->_fileIterator] != UPLOAD_ERR_OK && $_FILES[$this->_postName]['error'][$this->_fileIterator] != UPLOAD_ERR_NO_FILE)
			throw new UploadException(
				[
					'code' => $_FILES[$this->_postName]['error'][$this->_fileIterator]
				]
			);

		if (!is_writeable($folder))
			throw new IOException(
				[
					'code' => IOException::DIRECTORY_NOT_WRITEABLE,
					'directory' => $folder
				]
			);

		// TODO: Why we need this in cPanel, is it cPanel bug?
		getcwd();

		return move_uploaded_file($_FILES[$this->_postName]['tmp_name'][$this->_fileIterator], $folder . '/' . $file);
	}

	function hasNext()
	{
		return ($this->_fileIterator <= $this->_fileNumber);
	}

	function isLast()
	{
		return ($this->_fileIterator == $this->_fileNumber);
	}

	function isFirst()
	{
		return ($this->_fileIterator == 0);
	}

	function next()
	{
		if ($this->hasNext()) {
			++$this->_fileIterator;

			$filename_part = explode('.', basename($_FILES[$this->_postName]['name'][$this->_fileIterator]));
			$file_ext = array_pop($filename_part);
			$name = implode('.', $filename_part);

			$this->Error = $_FILES[$this->_postName]['error'][$this->_fileIterator];
			$this->FileExtension = $file_ext;
			$this->FileName = $_FILES[$this->_postName]['name'][$this->_fileIterator];
			$this->Name = $name;
			$this->Size = $_FILES[$this->_postName]['size'][$this->_fileIterator];
			$this->TemporaryFileName = $_FILES[$this->_postName]['tmp_name'][$this->_fileIterator];
			$this->Type = $_FILES[$this->_postName]['type'][$this->_fileIterator];
		}
	}
}