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

namespace NS\Net\Mail;

use NS\Object;
use NS\Template\Engine\PHPTemplate;

/**
 *Send email in plain text format
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $To Recipients email address
 *@property string $Subject Mail subject
 *@property string $Message Mail message
 *@property string $From Sender email address
 *@property string $ReplyTo Reply email address
 *@property string $CC CC email address
 *@property string $BCC BCC email address
 */
class Mail extends Object {
	function __construct() {
		$this->createProperties(array(
			'To' => '',
			 'Subject' => '',
			 'Message' => '',
			 'From' => '',
			 'ReplyTo' => '',
			 'CC' => '',
			 'BCC' => ''
		));
	}

	/**
	*Add BCC recipient email address and name
	*
	*@param string $email Email address
	*@param string $name Recipient name
	*/
	function addBCC($email, $name = null) {
		if($name != null) {
			$email = '"' . $name . '" <' . $email . '>';
		}

		if($this->BCC != '') $this->BCC .= ', ' . $email;
		else $this->BCC = $email;
	}

	/**
	*Add many BCC recipients email address and name
	*
	*@param array $emails Associative array containing email address as key and recipient name as value
	*/
	function addBCCs($emails) {
		foreach($emails as $email => $name) { $this->addBCC($email, $name); }
	}

	/**
	*Add CC recipient email address and name
	*
	*@param string $email Email address
	*@param string $name Recipient name
	*/
	function addCC($email, $name = null) {
		if($name != null) {
			$email = '"' . $name . '" <' . $email . '>';
		}

		if($this->CC != '') $this->CC .= ', ' . $email;
		else $this->CC = $email;
	}

	/**
	*Add many CC recipients email address and name
	*
	*@param array $emails Associative array containing email address as key and recipient name as value
	*/
	function addCCs($emails) {
		foreach($emails as $email => $name) { $this->addCC($email, $name); }
	}

	/**
	*Add recipient email address and name
	*
	*@param string $email Email address
	*@param string $name Recipient name
	*/
	function addRecipient($email, $name = null) {
		if($name != null) {
			$email = '"' . $name . '" <' . $email . '>';
		}

		if($this->To != '') $this->To .= ', ' . $email;
		else $this->To = $email;
	}

	/**
	*Add many recipients email address and name
	*
	*@param array $emails Associative array containing email address as key and recipient name as value
	*/
	function addRecipients($emails) {
		foreach($emails as $email => $name) { $this->addRecipient($email, $name); }
	}

	/**
	*Parse email message from template
	*
	*@param string $file Template file name and path
	*@param mixed $data Variable that will be used in template file
	*/
	function parseMessage($file, $data) {
		$template = new PHPTemplate();
		$template->assign($data);

		$this->Message = $template->fetch($file);
	}

	/**
	*Send email
	*
	*@return boolean Return true if email is sent successfully
	*/
	function send() {
		$header =
			'From: ' . $this->From . "\r\n" .
			'Reply-To: ' . ($this->ReplyTo == '' ? $this->From : $this->ReplyTo) . "\r\n" .
			($this->CC != '' ? 'Cc: ' . $this->CC . "\r\n" : '') .
			($this->BCC != '' ? 'Bcc: ' . $this->BCC . "\r\n" : '') .
			'X-Mailer: NewStepFramework/' . NS_VERSION . ' - ' . 'PHP/' . phpversion();

		return mail($this->To, $this->Subject, $this->Message, $header);
	}
}
?>