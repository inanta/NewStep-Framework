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

final class IOException extends Exception {
	/**
	 *File not found error code
	 */
	const FILE_NOT_FOUND = 1;
	/**
	 *Directory not found error code
	 */
	const DIRECTORY_NOT_FOUND = 2;
	/**
	 *File is not writeable error code
	 */
	const FILE_NOT_WRITEABLE = 3;
	/**
	 *Directory is not writeable error code
	 */
	const DIRECTORY_NOT_WRITEABLE = 4;
	/**
	 *File is not readable error code
	 */
	const FILE_NOT_READABLE = 5;
	/**
	 *Directory is not readable error code
	 */
	const DIRECTORY_NOT_READABLE = 6;
	/**
	 *File already exist error code
	 */
	const FILE_ALREADY_EXIST = 7;
	/**
	 *Directory already exits error code
	 */
	const DIRECTORY_ALREADY_EXIST = 8;
	/**
	 *Directory is empty error code
	 */
	const DIRECTORY_IS_EMPTY = 9;
	/**
	 *Directory is not empty error code
	 */
	const DIRECTORY_IS_NOT_EMPTY = 10;

	/**
	 *Exception constructor
	 *
	 *@param array $args Exception parameter to show appropriate message
	 */
	function __construct($args) {
		$message = null;
		$this->ErrorCode = $args['code'];
		
		switch ($args['code']) {
			case self::FILE_NOT_FOUND:
				$message = sprintf($this->_('File [%s] is not exist'), $args['filename']);
				break;
			case self::DIRECTORY_NOT_FOUND:
				$message = sprintf($this->_('Directory [%s] is not exist'), $args['directory']);
				break;
			case self::FILE_NOT_WRITEABLE:
				$message = sprintf($this->_('File [%s] is not writeable'), $args['filename']);
				break;
			case self::DIRECTORY_NOT_WRITEABLE:
				$message = sprintf($this->_('Directory [%s] is not writeable'), $args['directory']);
				break;
			case self::FILE_NOT_READABLE:
				$message = sprintf($this->_('File [%s] is not readable'), $args['filename']);
				break;
			case self::DIRECTORY_NOT_READABLE:
				$message = sprintf($this->_('Directory [%s] is not radeable'), $args['directory']);
				break;
			case self::FILE_NOT_READABLE:
				$message = sprintf($this->_('File [%s] is not readable'), $args['filename']);
				break;
			case self::DIRECTORY_NOT_READABLE:
				$message = sprintf($this->_('Directory [%s] is not radeable'), $args['directory']);
				break;
			case self::FILE_ALREADY_EXIST:
				$message = sprintf($this->_('File [%s] is already exist'), $args['filename']);
				break;
			case self::DIRECTORY_ALREADY_EXIST:
				$message = sprintf($this->_('Directory [%s] is already exist'), $args['directory']);
				break;
			case self::DIRECTORY_IS_EMPTY:
				$message = sprintf($this->_('Directory [%s] is empty'), $args['directory']);
				break;
			case self::DIRECTORY_IS_NOT_EMPTY:
				$message = sprintf($this->_('Directory [%s] is not empty'), $args['directory']);
				break;
			default:
				$message = $this->_('Unknown NewStep IO error');
		}
		
		parent::__construct($message);
	}
}
?>
