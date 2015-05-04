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
use NS\Core\Config;
use NS\Exception\IOException;

/**
 *Handle file download processing for client
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $ContentType MIME content type for the file
 *@property string $FileExtension File extension
 *@property string $FileName File name for download header
 *@property string $Size Size of the file
 */
class Download extends Object {
	private $_filename;

	function startTransfer() {
		include(NS_SYSTEM_PATH . '/' .  Config::getInstance()->ConfigFolder . '/MimeType.inc.php');
		if(isset($MimeType[$ext])) $this->ContentType = $MimeType[$ext];

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $this->ContentType);
		header('Content-Disposition: attachment; filename="' . $this->FileName . '"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . $this->Size);

		ob_end_clean();
		readfile($this->_filename);

		exit;
	}

	function __construct($filename) {
		if(!is_file($filename)) throw new IOException(array('code' => NS_EX_IO_FILE_NOT_FOUND, 'filename' => $filename));
		if(!is_readable($filename)) throw new IOException(array('code' => NS_EX_IO_FILE_NOT_READABLE, 'filename' => $filename));

		$this->_filename = $filename;

		$filename_part = explode('.', $this->_filename);
		$file_path = explode('/', $this->_filename);

		$this->createProperties(array(
			'FileName' => end($file_path),
			'FileExtension' => end($filename_part),
			'Size' => filesize($this->_filename),
			'ContentType' => 'application/force-download'
		));

		
	}
}
?>
