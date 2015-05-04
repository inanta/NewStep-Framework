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

namespace NS\Template;

use NS\Object;
use NS\Core\Config;

define('NS_TPL_PHP', 'php');
define('NS_TPL_SMARTY', 'smarty');

/**
 *Template base class
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $File Template file name and path that will be used
 */
abstract class Template extends Object {
	protected $_vars = array();

	static private $_lastInstance = array();
	static private $_enginesInstance = array();

	function __construct($args) {
		$this->createProperties(array('Path' => '', 'File' => ''));
	}

	/**
	*Assign variable to template
	*
	*@param string|array $tpl_var Variable name or associative array of variables that will be assigned to template
	*/
	function assign($tpl_var, $value = null) {
		if(is_array($tpl_var)) {
			foreach($tpl_var as $k => $v) { 
				$this->_vars[$k] = $v; 
			}
		} else { 
			$this->_vars[$tpl_var] = $value; 
		}
	}

	/**
	*Remove all variables from template
	*
	*/
	function clearAllAssign() {
		$this->_vars = array();
	}

	/**
	*Clear variable or variables from template
	*
	*@param string|array $tpl_var Variable name or associative array of variables that will removed from template
	*/
	function clearAssign($tpl_var) {
		if(is_array($tpl_var)) {
			foreach($tpl_var as $k => $v) { unset($this->_vars[$v]); }
		} else { unset($this->_vars[$tpl_var]); }
	 }

	/**
	*Print rendered template
	*
	*/
	function display($resource_name) {
		echo $this->fetch($resource_name);
	}

	/**
	*Return rendered template
	*
	*@return string Rendered content
	*/
	abstract function fetch($resource_name);

	static function getInstance($tpl_type = null, $args = array(), $id  = 0) {
		if($tpl_type == null) $tpl_type = Config::getInstance()->Application->TemplateEngine;

		if(isset(self::$_lastInstance[$id])) self::$_lastInstance[$id] = $tpl_type;
		if(isset(self::$_enginesInstance[$tpl_type][$id])) return self::$_enginesInstance[$tpl_type][$id];

		switch(self::$_lastInstance) {
			case NS_TPL_SMARTY:
				return (self::$_enginesInstance[NS_TPL_SMARTY][$id] = new Engine\SmartyTemplate($args));
			case NS_TPL_PHP:
			default:
				return (self::$_enginesInstance[NS_TPL_PHP][$id] = new Engine\PHPTemplate($args));
		}
	}

	static function newInstance($tpl_type = NS_TPL_PHP, $args = array()) {
		switch($tpl_type) {
			case NS_TPL_SMARTY:
				return (new Engine\SmartyTemplate($args));
				break;
			case NS_TPL_PHP:
			default:
				return (new Engine\PHPTemplate($args));
		}
	}
}
?>