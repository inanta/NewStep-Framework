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

namespace NS\IO;

use NS\BaseIterable;
use NS\Exception\IOException;

/**
 *Directory functionality
 *
 *@author Inanta Martsanto <inanta@inationsoft.com
 *@property string $FileName Filename
 *@property bool $IsDirectory Determine if file is directory or folder
 */
class Directory extends BaseIterable {
	function __construct($path) {
		self::validate($path);

		$this->createProperties(array('Path' => (substr($path, -1, 1) == '/' ? $path : $path . '/'), $path, 'FileName' => null, 'IsDirectory' => null, 'IsHidden' => null));

		$dh = opendir($path);
		while(($file = readdir($dh)) !== false) {
			$this->_collection[] = $file;
		}
		closedir($dh);

		parent::__construct();
	}

	function next() {
		parent::next();

		if($this->hasNext()) {
			$this->FileName = $this->_collection[$this->_iterator];
			$this->IsDirectory = is_dir($this->Path . $this->_collection[$this->_iterator]);
			$this->IsHidden = (substr($this->FileName, 0, 1) == '.' ? true : false);
		}
	}

	// Based on http://www.aidanlister.com/2004/04/recursively-copying-directories-in-php/
	static function copy($source, $dest, $permissions = 0755) {
		// Check for symlinks
		if(is_link($source)) {
			return symlink(readlink($source), $dest);
		}

		// Simple copy for a file
		if(is_file($source)) {
			return copy($source, $dest);
		}

		// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest, $permissions);
		}

		// Loop through the folder
		$dir = dir($source);

		while (false !== $entry = $dir->read()) {
		    // Skip pointers
		    if ($entry == '.' || $entry == '..') {
			    continue;
		    }

		    // Deep copy directories
		    self::copy($source . '/' . $entry, $dest . '/' . $entry, $permissions);
		}
	    
		// Clean up
		$dir->close();

		return true;
	}
	
	static function create($path, $recursive = true, $permission = 0777) {
		if($recursive) {
			$folders = explode('/', $path);
			$path = '';

			foreach($folders as $folder) {
				$path .= $folder . '/';

				if(!is_dir($path)) {
					$folder = self::createDirectory($path);
				}
			}

			return $folder;
		} else {
			return self::createDirectory($path . '/');
		}

		return false;
	}

	static function delete($path, $recursive = false) {
		self::validate($path);

		if(!$recursive) {
			if(!self::isEmpty()) throw new IOException(array('code' => IOException::DIRECTORY_NOT_EMPTY, 'directory' => $path));
		}

		return rm_dir($path);
	}

	static function isEmpty($path) {
		self::validate($path);

		$file = true;
		$dh = opendir($path);

		while(($file = readdir($dh)) !== false) {
			if($file == '.' || $file == '..') continue;
			break;
		}
		closedir($dh);

		return (!$file ? true : false);
	}

	static function validate($path) {
		if(!is_dir($path)) throw new IOException(array('code' => IOException::DIRECTORY_NOT_FOUND, 'directory' => $path));
		if(!is_readable($path)) throw new IOException(array('code' => IOException::DIRECTORY_NOT_WRITEABLE, 'directory' => $path));
	}

	private static function createDirectory($path) {
		$path = substr($path, 0, -1);
		$parent_folder = '';

		$folders = explode('/', $path);
		$folder_count = count($folders) - 1;
		
		for($i = 0; $i < $folder_count; ++$i) {
			$parent_folder .= $folders[$i] . '/';
		}

		if(is_file($path)) throw new IOException(array('code' => IOException::FILE_ALREADY_EXIST, 'filename' => $path));
		if(!is_readable($parent_folder)) throw new IOException(array('code' => IOException::DIRECTORY_NOT_WRITEABLE, 'directory' => str_replace('/' . $parent_folder, '', $path)));

		return mkdir($path);
	}
}
?>