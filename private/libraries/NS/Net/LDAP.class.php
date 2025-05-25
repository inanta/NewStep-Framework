<?php
namespace NS\Net;

use NS\BaseObject;
use NS\Exception\LibraryException;
use NS\Exception\NetException;

/**
 *Handle LDAP protocol functionality
 *
 *@author Inanta Martsanto <inanta@8daysproject.com>
 */
class LDAP extends BaseObject
{
	private $_connection, $_isConnected = false;

	function bind($username, $password)
	{
		if (ldap_bind($this->_connection, $username, $password) === true)
			return true;

		throw new NetException(
			[
				'code' => NetException::UNABLE_TO_LOGIN,
				'username' => $username
			]
		);
	}

	function close()
	{
		if (ldap_close($this->_connection)) {
			$this->_isConnected = false;

			return true;
		}

		return false;
	}

	function unbind()
	{
		return $this->close();
	}

	function __construct($host)
	{
		if (!function_exists('ldap_connect'))
			throw new LibraryException(
				[
					'code' => NS_EX_LIB_NOT_INSTALLED,
					'class' => __CLASS__,
					'library' => 'LDAP'
				]
			);

		if (!$this->_connection = ldap_connect($host))
			throw new NetException(
				[
					'code' => NetException::NOT_CONNECTED,
					'server' => $host
				]
			);

		$this->_isConnected = true;
	}

	function __destruct()
	{
		if ($this->_isConnected)
			$this->close();
	}
}