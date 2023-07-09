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
 *Base class for singleton object creation
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
abstract class SingletonObject extends BaseObject
{
	private static $_instances = array();

	/**
	 *Create or retrieve object instance, must be overidden in child class
	 *
	 */
	static function getInstance()
	{
		throw new OverrideMethodException(__FUNCTION__);
	}

	/**
	 *Create new object instance
	 *
	 *@param string $class Class name, use __CLASS__
	 *@param array $args Class arguments that will be passed to constructor
	 *@param mixed $id Identifier for newly created instance
	 */
	protected static function createInstance($class = null, $args = array(), $id = 0)
	{
		if ($class == null)
			throw new MissingArgumentException(__FUNCTION__, 'class');

		if (!isset(self::$_instances[$class][$id]))
			self::$_instances[$class][$id] = new $class($args);

		return self::$_instances[$class][$id];
	}
}
?>