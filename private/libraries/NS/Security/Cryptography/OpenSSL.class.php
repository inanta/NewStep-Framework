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

namespace NS\Security\Cryptography;

use NS\Exception\SecurityException;

/**
 *Class encrype / decrype string using simple XOR
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class OpenSSL {
	private $_key = 'NS';
	const METHOD = 'aes-256-ctr';
	
	function __construct($key = null) {
		if(!empty($key)) 
			$this->_key = $key;
	}

	function encrypt($message) {
		$nonceSize = openssl_cipher_iv_length(self::METHOD);
		$nonce = openssl_random_pseudo_bytes($nonceSize);

		$ciphertext = openssl_encrypt(
			$message,
			self::METHOD,
			$this->_key,
			OPENSSL_RAW_DATA,
			$nonce
		);

		return base64_encode($nonce.$ciphertext);
	}

	function decrypt($message) {
		$message = base64_decode($message, true);
		
		if ($message === false) {
			throw new SecurityException(array('code' => SecurityException::INVALID_ENCRYPTION));
		}

		$nonceSize = openssl_cipher_iv_length(self::METHOD);
		$nonce = mb_substr($message, 0, $nonceSize, '8bit');
		$ciphertext = mb_substr($message, $nonceSize, null, '8bit');

		$plaintext = openssl_decrypt(
			$ciphertext,
			self::METHOD,
			$this->_key,
			OPENSSL_RAW_DATA,
			$nonce
		);

		return $plaintext;
	}
}
?>