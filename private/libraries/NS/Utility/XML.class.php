<?php
/*
	Copyright (C) 2008 - 2013 Inanta Martsanto
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

namespace NS\Utility;

/**
 * XML processing
 *
 * @author Inanta Martsanto <inanta@inationsoft.com>
 */
class XML {
	static function fromArray($data, $parent_tag = 'xml') {
		$xml = new \SimpleXMLElement('<' . $parent_tag . '/>');
		self::_fromArray($data, $xml);

		return $xml->asXML();
	}

	private static function _fromArray($data, $xml = null) {
		foreach($data as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)) {
					$subnode = $xml->addChild($key);
					XML::_fromArray($value, $subnode);
				} else {
					$subnode = $xml->addChild('item' . $key);
					XML::_fromArray($value, $subnode);
				}
			} else {
				if(is_numeric($key)) {
					$key = 'item' . $key;
				}

				$xml->addChild($key, htmlspecialchars($value));
			}
		}
	}
}
?>