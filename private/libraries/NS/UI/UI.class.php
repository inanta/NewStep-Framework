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

use NS\Object;

/**
 *User interface base class for widget and panel
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */

abstract class UI extends Object {
	protected static $_count;
	protected $_attr = array(), $_tag, $_isTagClosed, $_content;
	public $UI;

	/**
	*Initialize user interface
	*
	*/
	function __construct($ui) {
		$this->UI = $ui;
	}

	function __toString() {
		try { return ('' . $this->UI); }
		catch(Exception $e) { return __CLASS__; }
	}

	/**
	*Construct widget
	*
	*/
	protected function constructUI($tag = null, $close_tag = false, $content = '') {
		if($tag == null) {
			$tag = $this->_tag;
			$close_tag = $this->_isTagClosed;
			$content = $this->_content;
		} else {
			$this->_tag = $tag;
			$this->_isTagClosed = $close_tag;
			$this->_content = $content;
		}

		$w = '<' . $tag;

		foreach($this->_attr as $att => $value) {
			if($att == 'value' && $value === '') continue;
			if($att == 'value') $value = htmlentities($value);
			if($att == 'id') if(($pos = strpos($value, '[')) !== false) { $value = substr($value, 0, $pos); $value .= '-' . $this->getUICount($value); }

			$w .= ' ' . $att . '="' . $value . '"';
		}

		$w .= $close_tag ? '>' . $content . '</' . $tag . '>' : ' />' . $content;

		return $w;
	}

	function __set($p, $v) {
		if(isset($this->_attr[$p])) {
			$this->_attr[$p] = $v;
		}
		else parent::__set($p, $v);
	}

	function __get($p) {
		if(isset($this->_attr[$p])) return $this->_attr[$p];
		else return parent::__get($p);
	}

	/**
	*Counter for initialized user interface
	*
	*/
	protected function getUICount($name) {
		if(!isset(self::$_count[$name])) self::$_count[$name] = 0;

		return (++self::$_count[$name]);
	}
}
?>
