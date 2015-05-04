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

class DirectoryInfo extends Object {
	private $_entries = array();

	function __construct($path) {
		$this->createProperties(array('Path' => ''));
		$this->Path = $path;

	}

	function __set($property, $value) {
		parent::__set($property, $this->correctPath($value));
		if($property == 'Path') $this->initializeEntries();
	}
	
	function getFiles($showhidden = false) {
		$fileinfo = array();

		foreach($this->_entries as $entry) {
			if(!$showhidden && $entry[0] == '.') continue;

			if(is_file($this->Path.$entry)) {
				$fileinfo[$entry] = new FileInfo($this->Path.$entry);
			}
		}
		
		return $fileinfo;
	}

	function getDirectories($showhidden = false) {
		$dirinfo = array();
	
		foreach($this->_entries as $entry) {
			if(!$showhidden && $entry[0] == '.') continue;

			if(is_dir($this->Path.$entry)) {
				$dirinfo[$entry] = new DirectoryInfo($this->Path.$entry);
			}
		}
		
		return $dirinfo;
	}
	
	function getDirectoriesArray($showhidden = false) {
		$dirinfo = array();
	
		foreach($this->_entries as $entry) {
			if(!$showhidden && $entry[0] == '.') continue;

			if(is_dir($this->Path.$entry)) {
				$dirinfo[$entry] = $entry;
			}
		}
		
		return $dirinfo;
	}

	private function initializeEntries() {
		if(!is_dir($this->Path)) throw new IOException(array('code' => DIRECTORY_NOT_FOUND, 'dirname' => $this->Path));

		$dir = dir($this->Path);
		while (false !== ($entry = $dir->read())) {
			$this->_entries[$entry] = $entry;
		}
	}

	private function correctPath($path) { $path = str_replace('\\', '/', $path); if(substr($path, -1, 1) != '/') $path .= '/'; return $path; }
}
?>
