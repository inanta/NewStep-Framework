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

namespace NS;

/**
 *Main debugger for framework
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Debugger extends Object {
	private $_headerPrinted = false;
	
	function __construct($params = null) {
		$this->createProperties(array(
			'Buffer' => (isset($params['buffer']) ? $params['buffer'] : true),
			'DebugHeader' => (isset($params['header']) ? $params['header'] : T_('Unknown')),
			'DebugInfo' => ''
		));
	}
	
	function debug($message) {
		$out = '<span class="debugger">'.$message.'</span>';
		
		if($this->Buffer) {
			$this->DebugInfo .= $out;
		} else {
			echo $out;
		}
	}
	
	function header() {
		if(!$this->_headerPrinted) {
			$out = '<span class="debugger">'.strtoupper(sprintf(T_('Debugger Started For %s')), $this->DebugHeader).'</span>';
			
			if($this->Buffer) {
				$this->DebugInfo .= $out;
			} else {
				echo $out;
			}
		}
		
	}
	
	function resetBuffer() {
		$this->Buffer = false;
		$this->DebugInfo = '';
	}
	
	function flush() {
		echo $this->DebugInfo;
		$this->DebugInfo = '';
	}
}
?>
