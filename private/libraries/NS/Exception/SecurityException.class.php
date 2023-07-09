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

class SecurityException extends Exception {
	const INVALID_URL = 1;
	const INVALID_ENCRYPTION = 2;

	function __construct($args) {
		$message = null;
		$this->ErrorCode = $args['code'];
		
		switch ($args['code']) {
			case self::INVALID_URL:
				$message = sprintf($this->_('Invalid URL format [%s], please make sure URL only contains allowed characters'), (strlen(NS_CURRENT_URL) > 20 ? substr(NS_CURRENT_URL, 0, 15) . ' ... ' . substr(NS_CURRENT_URL, -5) : NS_CURRENT_URL));
				break;
			case self::INVALID_ENCRYPTION:
				$message = $this->_('Invalid encryption');
				break;
			default:
				$message = $this->_('Unknown security exception');
		}
    
		parent::__construct($message);
	}
}
?>
