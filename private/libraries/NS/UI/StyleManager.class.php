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
 *Manage CSS aggregation
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class StyleManager extends SingletonObject {
	private $_styles = array(); private $_sources = array(), $_externalSources = array();

	function __construct() { define('NS_CSS', true); }

	function addSource($location) { if(!file_exists($location)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $location)); $this->_sources[md5($location)] = $location; }
	function addStyle($style) { $this->_styles[md5($style)] = $style; }
	function removeSource($location) { unset($this->_sources[md5($location)]); }
	function removeStyle($style) { unset($this->_styles[md5($style)]); }
	function getSources() { return $this->_sources; }
	function getStyles() { return $this->_styles; }

	function addExternalSource($location) { $this->_externalSources[md5($location)] = $location; }
	function removeExternalSource($location) { unset($this->_externalSources[md5($location)]); }
	function getExternalSources() { return $this->_externalSources; }

	function render() {
		return '<style>' . $this->get() . '</style>';
	}

	function renderTag($aggregator_path) {
		$css = '<link rel="stylesheet" type="text/css" href="' . $aggregator_path . '/' . $this->getHash() .'" />';
		foreach($this->_externalSources as $s) { $css .= '<link rel="stylesheet" type="text/css" href="' . $s . '" />'; }
		
		return $css;
	}

	function renderToFile($path) {
		if(!is_file($file = ($path . '/' . $this->getHash() . '.css')) || NS_CACHE == false) {
			$fw = new FileWriter($file);
			$fw->write($this->get());
			$fw->close();
		} else {
			if(!defined('NS_CSS_RENDERED')) define('NS_CSS_RENDERED', true);
		}
	}

	function get($is_compressed = true) {
		if(!defined('NS_CSS_RENDERED')) define('NS_CSS_RENDERED', true);

		$contents = '';
		foreach($this->_sources as $k => $source) { $contents .= file_get_contents($source); unset($this->_sources[$k]); }
		foreach($this->_styles as $k => $style) { $contents .= $style; unset($this->_styles[$k]); }

		if($is_compressed) {
			$contents = preg_replace('/\/\*(.*?)\*\//is', '', $contents);
			$contents = preg_replace('/;?\s*}/', '}', $contents); 
			$contents = preg_replace('/\s*([\{:;,])\s*/', '$1', $contents); 
			$contents = preg_replace('/^\s*|\s*$/m', '', $contents); 
			$contents = preg_replace('/\n/', '', $contents); 
		}

		return $contents;
	}

	private function getHash() { return md5(serialize($this)); }
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>
