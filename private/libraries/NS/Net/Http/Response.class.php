<?php
/*
	Copyright (C) 2008 - 2019 Inanta Martsanto
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

use NS\SingletonObject;
use NS\Core\Config;

/**
 *Handle HTTP response
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Response {
	private $_statusCode = 200;
	private $_statusText = '';
	private $_headers = array(
		'content-type' => array('text/html; charset=utf-8')
	);
	private $_content = '';

	function __construct($content = '', $status_code = 200, $headers = array()) {
		$this->_statusCode = $status_code;

		if (is_array($headers) && count($headers) > 0) {
			foreach ($headers as $name => $value) {
				$this->setHeader($name, $value);
			}
		}

		$this->_content = $content;
	}

	function getContent() {
		return $this->_content;
	}

	function getHeader($key) {
		$key = strtolower($key);

		return (isset($this->_headers[$key]) ? $this->_headers[$key] : array());
	}

	function hasHeader($key, $value = null) {
		$key = strtolower($key);

		if (isset($this->_headers[$key])) {
			if ($value === null) {
				return true;
			}

			foreach ($this->_headers[$key] as $header_value) {
				if ($header_value == $value) {
					return true;
				}
			}

			return false;
		}

		return false;

		return (isset($this->_headers[$key]) ? $this->_headers[$key] : array());
	}

	function setContent($content) {
		$this->_content = $content;
	}

	function setHeader($key, $values, $replace = true) {
		$key = strtolower($key);

		if (!is_array($values)) {
			$values = array($values);
		}

		if ($replace || !isset($this->_headers[$key])) {
			$this->_headers[$key] = $values;
		} else {
			$this->_headers[$key] = array_merge($this->_headers[$key], $values);
		}
	}
 
	function sendHeaders() {
		if (headers_sent()) {
			return;
		}

		include(NS_SYSTEM_PATH . '/' .  Config::getInstance()->ConfigFolder . '/HttpCode.inc.php');

		$this->_statusText = isset($HttpCode[$this->_statusCode]) ? $HttpCode[$this->_statusCode] : 'Unknown Status';

		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->_statusCode . ' ' . $this->_statusText);

		foreach ($this->_headers as $name => $values) {
			foreach ($values as $value) {
				header(ucwords($name, '-') . ': ' . $value);
			}
		}
	}

	function sendContent() {
		echo $this->_content;
	}

	function send() {
		$this->sendHeaders();
		$this->sendContent();
	}
}
?>
