<?php
/*
	Copyright (C) 2008 - 2016 Inanta Martsanto
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

namespace NS\IO;

use NS\Iterable;
use NS\Exception\IOException;

/**
 *File functionality
 *
 *@author Inanta Martsanto <inanta@inationsoft.com
 */
class File extends Iterable {
	static function copy($source, $dest, $permissions = 0755) {
		if(is_file($source)) {
			return copy($source, $dest);
		}

		return false;
	}

	static function delete($path) {
		if(is_file($source)) {
			return unlink($path);
		}

		return false;
	}
}
?>