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

namespace NS\Core;

use NS\SingletonObject;
use NS\Database\Database;
use NS\Core\Config;

class DatabaseSessionHandler extends SingletonObject {
	private $_db;

	function __construct() {
		$this->createProperty('Lifetime', Config::getInstance()->Application->SessionLifetime == 0 ? 86400 : Config::getInstance()->Application->SessionLifetime);
		$this->setReadOnlyProperty('Lifetime');

		session_set_save_handler (
			array(&$this, 'open'),
			array(&$this, 'close'),
			array(&$this, 'read'),
			array(&$this, 'write'),
			array(&$this, 'destroy'),
			array(&$this, 'gc')
		);

		$this->_db = Database::getInstance(Config::getInstance()->Application->SessionBasedDatabaseConenction);
		register_shutdown_function('session_write_close');
	}
 
	function regenerateID() {
		$oldSessionID = session_id();
		session_regenerate_id();
		$this->destroy($oldSessionID);
	}
 
	function open($save_path, $name) { return true; } 
	function close() { return true; }
 
	function read($sid) {
		$result = $this->_db->query("SELECT data FROM " . $this->_db->prefix('sessions') . " WHERE sid = '". $sid . "' AND user_agent = '" . $_SERVER['HTTP_USER_AGENT'] . "' AND expire > '" . time() . "'");
 
		if (is_resource($result) && $this->_db->numRows($result) > 0)
		{
			$fields = $this->_db->fetchAssoc($result);
			return $fields["data"];
		}
 
		return '';
	}
 
	function write($sid, $data) {		
		$result = $this->_db->query("SELECT * FROM " . $this->_db->prefix('sessions') . " WHERE sid = '" . $sid . "'");
 
		if ($this->_db->numRows($result) > 0) {
			$result = $this->_db->query("UPDATE " . $this->_db->prefix('sessions') . " SET data = '" . $this->_db->escape($data) . "', expire = '" . (time() + $this->Lifetime) . "' WHERE sid = '" . $sid . "'");
 
			if ($this->_db->affectedRows()) return true;
		} else {
			$result = $this->_db->query("INSERT INTO " . $this->_db->prefix('sessions') . " (sid, user_agent, data, expire) VALUES ('" . $sid . "','" . $_SERVER['HTTP_USER_AGENT'] . "' , '" . $this->_db->escape($data) . "', '" . (time() + $this->Lifetime) . "')");
 
			if ($this->_db->affectedRows()) return true;
		}
 
		return false;
	}
 
	function destroy($sid) {
		$result = $this->_db->query("DELETE FROM " . $this->_db->prefix('sessions') . " WHERE sid = '" . $sid . "'");
 
		if ($this->_db->affectedRows()) return true;
		return false;
	}
 
	function gc($max_lifetime) {
		$result = $this->_db->query("DELETE FROM " . $this->_db->prefix('sessions') . " WHERE expire < '" . (time() - $this->Lifetime) . "'");
	}

	/**
	*Create or retrieve object instance
	*
	*@return DatabaseSessionHandler
	*/
	static function getInstance() { return self::createInstance(__CLASS__); }
}
?>