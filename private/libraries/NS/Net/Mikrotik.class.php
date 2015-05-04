<?php
/*
	Copyright (C) 2008 - 2014 Inanta Martsanto
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

namespace NS\Net;

use NS\Exception\NetException;

/**
 * Handle Mikrotik RouterOS API functionality based on RouterOS PHP API class v1.5 by Denis Basta
 *
 * @author Inanta Martsanto <inanta@inationsoft.com>
 */
class Mikrotik {
	private $_socket, $_debug, $_errorNo, $_errorMessage, $_retryAttemps = 5,
		$_isConnected = false, $_retryTimeout = 3, $_defaultPort = 8728,
		$_connectionTimeout = 3;

	/**
	 * 
	 * @param type $text
	 */
	function debug($text) {
		if ($this->_debug)
			echo $text . "\n";
	}

	/**
	 * 
	 * @param type $length
	 * @return string
	 */
	function encodeLength($length) {
		if ($length < 0x80) {
			$length = chr($length);
		} else if ($length < 0x4000) {
			$length |= 0x8000;
			$length = chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
		} else if ($length < 0x200000) {
			$length |= 0xC00000;
			$length = chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
		} else if ($length < 0x10000000) {
			$length |= 0xE0000000;
			$length = chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
		} else if ($length >= 0x10000000)
			$length = chr(0xF0) . chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF);

		return $length;
	}

	/**
	 * 
	 * @param type $ip
	 * @param type $username
	 * @param type $password
	 * @return type
	 * @throws NetException
	 */
	function connect($ip, $username, $password) {
		for ($ATTEMPT = 1; $ATTEMPT <= $this->_retryAttemps; $ATTEMPT++) {
			$this->_isConnected = false;
			$this->debug('Connection attempt #' . $ATTEMPT . ' to ' . $ip . ':' . $this->_defaultPort . '...');
			$this->_socket = @fsockopen($ip, $this->_defaultPort, $this->_errorNo, $this->_errorMessage, $this->_connectionTimeout);
			if ($this->_socket) {
				socket_set_timeout($this->_socket, $this->_connectionTimeout);
				$this->write('/login');
				$RESPONSE = $this->read(false);

				if ($RESPONSE[0] == '!done') {
					$MATCHES = array();

					if (preg_match_all('/[^=]+/i', $RESPONSE[1], $MATCHES)) {
						if ($MATCHES[0][0] == 'ret' && strlen($MATCHES[0][1]) == 32) {
							$this->write('/login', false);
							$this->write('=name=' . $username, false);
							$this->write('=response=00' . md5(chr(0) . $password . pack('H*', $MATCHES[0][1])));
							$RESPONSE = $this->read(false);
							if ($RESPONSE[0] == '!done') {
								$this->_isConnected = true;
								break;
							}
						}
					}
				}

				fclose($this->_socket);
			}

			sleep($this->_retryTimeout);
		}

		/*
		if ($this->_isConnected) {
			$this->_debug('Connected...');
		} else {
			$this->_debug('Error...');
		}
		*/
		
		if(!$this->_isConnected) throw new NetException(array('code' => NetException::UNABLE_TO_LOGIN, 'username' => $username));

		return $this->_isConnected;
	}
	
	
	/**
	 * 
	 */
	function disconnect() {
		fclose($this->_socket);
		$this->_isConnected = false;
		$this->debug('Disconnected...');
	}
	
	
	/**
	 * 
	 * @param type $response
	 * @return array
	 */
	function parseResponse($response) {
		if (is_array($response)) {
			$PARSED      = array();
			$CURRENT     = null;
			$singlevalue = null;

			foreach ($response as $x) {
				if (in_array($x, array(
					'!fatal',
					'!re',
					'!trap'
				))) {
					if ($x == '!re') {
						$CURRENT =& $PARSED[];
					} else
						$CURRENT =& $PARSED[$x][];
				} else if ($x != '!done') {
					$MATCHES = array();

					if (preg_match_all('/[^=]+/i', $x, $MATCHES)) {
						if ($MATCHES[0][0] == 'ret') {
							$singlevalue = $MATCHES[0][1];
						}

						$CURRENT[$MATCHES[0][0]] = (isset($MATCHES[0][1]) ? $MATCHES[0][1] : '');
					}
				}
			}

			if (empty($PARSED) && !is_null($singlevalue)) {
				$PARSED = $singlevalue;
			}

			return $PARSED;
		} else
			return array();
	}

	/**
	 * 
	 * @param type $array
	 * @return type
	 */
	function arrayChangeKeyName(&$array) {
	    if (is_array($array)) {
		foreach ($array as $k => $v) {
		    $tmp = str_replace("-", "_", $k);
		    $tmp = str_replace("/", "_", $tmp);
		    if ($tmp) {
			$array_new[$tmp] = $v;
		    } else {
			$array_new[$k] = $v;
		    }
		}
		return $array_new;
	    } else {
		return $array;
	    }
	}

	/**
	 * 
	 * @param type $parse
	 * @return type
	 */
	function read($parse = true) {
		$RESPONSE = array();
	    	$receiveddone = false;

		while (true) {
			$BYTE   = ord(fread($this->_socket, 1));
			$LENGTH = 0;

			if ($BYTE & 128) {
				if (($BYTE & 192) == 128) {
					$LENGTH = (($BYTE & 63) << 8) + ord(fread($this->_socket, 1));
				} else {
					if (($BYTE & 224) == 192) {
						$LENGTH = (($BYTE & 31) << 8) + ord(fread($this->_socket, 1));
						$LENGTH = ($LENGTH << 8) + ord(fread($this->_socket, 1));
					} else {
						if (($BYTE & 240) == 224) {
							$LENGTH = (($BYTE & 15) << 8) + ord(fread($this->_socket, 1));
							$LENGTH = ($LENGTH << 8) + ord(fread($this->_socket, 1));
							$LENGTH = ($LENGTH << 8) + ord(fread($this->_socket, 1));
						} else {
							$LENGTH = ord(fread($this->_socket, 1));
							$LENGTH = ($LENGTH << 8) + ord(fread($this->_socket, 1));
							$LENGTH = ($LENGTH << 8) + ord(fread($this->_socket, 1));
							$LENGTH = ($LENGTH << 8) + ord(fread($this->_socket, 1));
						}
					}
				}
			} else {
				$LENGTH = $BYTE;
			}

			if ($LENGTH > 0) {
				$_      = "";
				$retlen = 0;

				while ($retlen < $LENGTH) {
					$toread = $LENGTH - $retlen;
					$_ .= fread($this->_socket, $toread);
					$retlen = strlen($_);
				}

				$RESPONSE[] = $_;
				$this->debug('>>> [' . $retlen . '/' . $LENGTH . '] bytes read.');
			}

			if ($_ == "!done")
				$receiveddone = true;
			
			$STATUS = socket_get_status($this->_socket);

			if ($LENGTH > 0)
				$this->debug('>>> [' . $LENGTH . ', ' . $STATUS['unread_bytes'] . ']' . $_);
			
			if ((!$this->_isConnected && !$STATUS['unread_bytes']) || ($this->_isConnected && !$STATUS['unread_bytes'] && $receiveddone))
				break;
		}

		if ($parse)
			$RESPONSE = $this->parseResponse($RESPONSE);

		return $RESPONSE;
	}

	/**
	 * 
	 * @param type $command
	 * @param type $param2
	 * @return boolean
	 */
	function write($command, $param2 = true) {
		if ($command) {
			$data = explode("\n", $command);

			foreach ($data as $com) {
				$com = trim($com);
				fwrite($this->_socket, $this->encodeLength(strlen($com)) . $com);
				$this->debug('<<< [' . strlen($com) . '] ' . $com);
			}

			if (gettype($param2) == 'integer') {
				fwrite($this->_socket, $this->encodeLength(strlen('.tag=' . $param2)) . '.tag=' . $param2 . chr(0));
				$this->debug('<<< [' . strlen('.tag=' . $param2) . '] .tag=' . $param2);
			} else if (gettype($param2) == 'boolean')
				fwrite($this->_socket, ($param2 ? chr(0) : ''));

			return true;
		} else
			return false;
	}

	/**
	 * 
	 * @param type $command
	 * @param type $arr
	 * @return type
	 */
	function command($command, $arr = array()) {
		$count = count($arr);
		$this->write($command, !$arr);
		$i = 0;

		foreach ($arr as $k => $v) {
			switch ($k[0]) {
				case "?":
					$el = "$k=$v";
					break;
				case "~":
					$el = "$k~$v";
					break;
				default:
					$el = "=$k=$v";
					break;
			}

			$last = ($i++ == $count - 1);
			$this->write($el, $last);
		}

		return $this->read();
	}
}
?>