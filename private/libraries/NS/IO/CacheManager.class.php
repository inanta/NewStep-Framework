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

use NS\SingletonObject;
use NS\Exception\IOException;

if(!defined('NS_CACHE_EXPIRE')) define('NS_CACHE_EXPIRE', 86400);

/**
 *Manage cache for framework
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class CacheManager extends SingletonObject {
	function __construct() {
		define('NS_CACHE_PATH', NS_SYSTEM_PATH . '/asset/cache');
		if(!is_writeable(NS_CACHE_PATH)) throw new IOException(array('code' => IOException::DIRECTORY_NOT_WRITEABLE, 'directory' => NS_CACHE_PATH));
	}

	function write($file, $contents) {
		$cache_fp = null;

		if(!file_exists(NS_CACHE_PATH . '/' . $file)) {
			$cache_fp = true;
		} elseif((filemtime(NS_CACHE_PATH . '/' . $file) + NS_CACHE_EXPIRE) < time()) {			
			$cache_fp = true;
		}

		if($cache_fp) {
			$cache_fp = fopen(NS_CACHE_PATH . '/' . $file , 'w');
			fwrite($cache_fp, $contents);
			
			return true;
		}

		return false;
	}

	function read($file) {
		if(file_exists(NS_CACHE_PATH . '/' . $file)) {
			if((filemtime(NS_CACHE_PATH . '/' . $file) + NS_CACHE_EXPIRE) < time()) return false;
			
			return file_get_contents(NS_CACHE_PATH . '/' . $file);
		}

		return false;
	}

	static function getInstance() {
		return self::createInstance(__CLASS__);
	}
}
?>
