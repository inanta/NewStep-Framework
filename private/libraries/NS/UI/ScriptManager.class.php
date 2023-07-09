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

namespace NS\UI;

use NS\SingletonObject;
use NS\IO\FileWriter;
use NS\Exception\IOException;

/**
 *Manage Javascript aggregation
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class ScriptManager extends SingletonObject {
	private $_scripts = array(); private $_sources = array(), $_externalSources = array();
	
	function __construct() { define('NS_JS', true); }
	function addSource($location) { if(!file_exists($location)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $location)); $this->_sources[md5($location)] = $location; }
	function addScript($script) { $this->_scripts[md5($script)] = $script; }
	function removeSource($location) { unset($this->_sources[md5($location)]); }
	function removeScript($script) { unset($this->_scripts[md5($script)]); }
	function getSources() { return $this->_sources; }
	function getScripts() { return $this->_scripts; }

	function addExternalSource($location) { $this->_externalSources[md5($location)] = $location; }
	function removeExternalSource($location) { unset($this->_externalSources[md5($location)]); }
	function getExternalSources() { return $this->_externalSources; }

	function render() {
		return '<script type="text/javascript">' . $this->get() . '</script>';
	}

	function renderTag($aggregator_path) {
		$js = '<script type="text/javascript" src="' . $aggregator_path . '/' . $this->getHash() .'"></script>';
		foreach($this->_externalSources as $s) { $js .= '<script type="text/javascript" src="' . $s . '"></script>'; }
		
		return $js;
	}

	function renderToFile($path) {
		if(!is_file($file = ($path . '/' . $this->getHash() . '.js')) || NS_CACHE == false) {
			$fw = new FileWriter($file);
			$fw->write($this->get());
			$fw->close();
		} else {
			if(!defined('NS_JS_RENDERED')) define('NS_JS_RENDERED', true);
		}
	}

	function get() {
		if(!defined('NS_JS_RENDERED')) define('NS_JS_RENDERED', true);

		$contents = '';
		foreach($this->_sources as $k => $source) { $contents .= file_get_contents($source) . "\n"; unset($this->_sources[$k]); }
		foreach($this->_scripts as $k => $script) { $contents .= $script  . "\n"; unset($this->_scripts[$k]); }

		return $contents;
	}

	private function getHash() { return md5(serialize($this)); }
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>
