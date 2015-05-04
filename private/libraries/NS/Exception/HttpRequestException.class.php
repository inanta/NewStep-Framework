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

namespace NS\Exception;

use NS\Core\Config;

class HttpRequestException extends Exception {
	function __construct($code, $message = null) {
		@include(NS_SYSTEM_PATH . '/' .  Config::getInstance()->ConfigFolder . '/HttpCode.inc.php');

		if(isset($HttpCode[$code])) {
			$this->_httpHeader['code'] = $code;
			$this->_httpHeader['message'] = $HttpCode[$code];

			if($message == null) $message = $HttpCode[$code] . ' [%s]';
			$this->ErrorCode = $code;
		} else {
			$message = '500 Internal server error [%s]';
			$this->ErrorCode = 500;
		}

		parent::__construct(sprintf($message, NS_CURRENT_URL));
	}
}
?>
