<?php
/*
	Copyright (C) 2008 - 2012 Inanta Martsanto
	Inanta Martsanto (inanta@8daysproject.com)

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

use NS\BaseObject;
use NS\Exception\IOException;

class FileInfo extends BaseObject
{
	function __construct($path)
	{
		if (!is_file($path))
			throw new IOException(
				[
					'code' => IOException::FILE_NOT_FOUND,
					'filename' => $path
				]
			);

		$this->createProperties(
			[
				'BaseName' => '',
				'FullName' => $path,
				'Extension' => '',
				'Size' => sprintf("%u", filesize($path))
			]
		);

		$explode = explode('/', $path);
		$this->BaseName = $explode[count(explode('/', $path)) - 1];
		$explode = explode('.', $path);
		$this->Extension = '.' . $explode[count(explode('.', $path)) - 1];
	}
}