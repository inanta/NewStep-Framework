<?php
namespace NS\Net\Http;

use NS\BaseObject;
use NS\Exception\LibraryException;
use NS\Exception\NetException;

/**
 *Handle remote request (cURL) wrapper
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class RemoteRequest extends BaseObject
{
	const REQUEST_POST = 1;
	const REQUEST_GET = 2;

	private $_ch;

	function __construct()
	{
		if (!function_exists('curl_init'))
			throw new LibraryException(array('code' => NS_EX_LIB_NOT_INSTALLED, 'class' => __CLASS__, 'library' => 'cURL'));

		$this->_ch = curl_init();

		curl_setopt($this->_ch, CURLOPT_HEADER, 0);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, 1);
	}

	function get($url)
	{
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_HTTPGET, 1);

		return curl_exec($this->_ch);
	}

	function post($url, $fields = [], $headers = [])
	{
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_POST, 1);

		if (!empty($fields)) {
			curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($this->_ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
		}

		if (!empty($headers)) {
			curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $headers);
		}

		return curl_exec($this->_ch);
	}

	function postJSON($url, $data)
	{
		$headers = [
			'Content-Type: application/json'
		];

		$data = json_encode($data);

		return $this->post($url, $data, $headers);
	}

	function __destruct()
	{
		curl_close($this->_ch);
	}
}
?>