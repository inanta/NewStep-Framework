<?php
namespace NS\Net\Http;

use NS\Object;
use NS\Exception\LibraryException;
use NS\Exception\NetException;

/**
 *Handle remote request (cURL) wrapper
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class RemoteRequest extends Object {
	const REQUEST_POST = 1;
	const REQUEST_GET = 2;

	private $_ch;

	function __construct() {
		if(!function_exists('curl_init')) throw new LibraryException(array('code' => NS_EX_LIB_NOT_INSTALLED, 'class' => __CLASS__, 'library' => 'cURL'));

		$this->_ch = curl_init();

		curl_setopt($this->_ch, CURLOPT_HEADER, 0);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
	}

	function get($url) {
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_HTTPGET, 1);

		return curl_exec($this->_ch);
	}

	function post($url, $fields = array()) {
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_POST, 1);

		if(!empty($fields)) {
			curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $fields);
		}

		return curl_exec($this->_ch);
	}

	function __destruct() {
		curl_close($this->_ch);
	}
}
?>
