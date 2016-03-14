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

/**
 *Send email in HTML format
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class HTMLMail extends Mail {
	function send() {
		$header =
			'From: ' . $this->From . "\r\n" .
			'Reply-To: ' . ($this->ReplyTo == '' ? $this->From : $this->ReplyTo) . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" .
			'Content-Type: text/html; charset=ISO-8859-1' . "\r\n" .
			($this->CC != '' ? 'Cc: ' . $this->CC . "\r\n" : '') .
			($this->BCC != '' ? 'Bcc: ' . $this->BCC . "\r\n" : '') .
			'X-Mailer: NewStepFramework/' . NS_VERSION . ' - ' . 'PHP/' . phpversion();

		return mail($this->To, $this->Subject, $this->Message, $header);
	}
}
?>
