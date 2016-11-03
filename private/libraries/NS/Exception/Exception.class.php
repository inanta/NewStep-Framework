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

namespace NS\Exception;

use NS\Utility\XML;

/**
 *Exception class
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Exception extends \Exception {
	public $ErrorCode = 0, $Message, $Source, $File, $Line, 
		$DefaultMessageTemplate, $IsSendHttpHeader = true;
	protected $_errorShowed = false, $_httpHeader;

	/**
	 * Display as command line
	 */
	const DISPLAY_FORMAT_CLI = 0;

	/**
	 * Display as HTML format
	 */
	const DISPLAY_FORMAT_HTML = 1;

	/**
	 * Display as REST JSON response format
	 */
	const DISPLAY_FORMAT_REST_JSON = 2;

	/**
	 * Display as REST XML response format
	 */
	const DISPLAY_FORMAT_REST_XML = 3;

	/**
	 *
	 * @var integer Error display format
	 */
	static $DisplayFormat = self::DISPLAY_FORMAT_HTML;

	/**
	 *Exception constructor
	 *
	 *@param string $message Error message
	 */
	function __construct($message = 'NewStep Framework unknown error') {
		if(isset($this->_httpHeader) && !array_key_exists('code', $this->_httpHeader) || !isset($this->_httpHeader)) {
			$this->_httpHeader['code'] = 500;
			$this->_httpHeader['message'] = 'Internal Server Error';
		}

		if(!isset($this->DefaultMessageTemplate)) $this->DefaultMessageTemplate = NS_SYSTEM_PATH . '/asset/template/error/NS.Exception.Exception.php';

		if(!NS_DEBUG_MODE) $message = preg_replace('/\[[^\]]*\]/', '[HIDDEN]', $message);
		parent::__construct($message);

		$this->Message = $this->getMessage();
		$this->Source = get_class($this);
		$this->File = (!NS_DEBUG_MODE ? end(explode('/', $this->getFile())) : $this->getFile());
		$this->Line = $this->getLine();
	}

	/**
	 *Translate error message if translation is available
	 *
	 *@param string $msg Message to be translated
	 */
	function _($msg) {
		if(function_exists('ns_gettext_init')) {
			ns_gettext_init(str_replace('\\', '.', get_class($this)));

			return _($msg);
		}

		return $msg;
	}

	/**
	 *Show error message
	 */
	function showMessage() {
		if(isset($this->_httpHeader['code']) && $this->IsSendHttpHeader) header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->_httpHeader['code'] . ' ' . $this->_httpHeader['message']);

		if(function_exists('ns_gettext_init')) {
			ns_gettext_init('NS');
		}

		$error = array(
			'ErrorHeader' => _('The page cannot be displayed due to internal error'), 'NSErrorMessageCaption' => _('NS Error Message'), 'ExceptionCaption' => _('Exception'), 'FileCaption' => _('File'), 'LastOutputCaption' => _('Last Output From Buffer'), 'TraceCaption' => _('Trace'),
			'LastOutput' => htmlentities(ob_get_contents()), 'Message' => $this->Message, 'Source' => $this->Source, 'File' => $this->File, 'Line' => $this->Line, 'Trace' => (NS_DEBUG_MODE ? $this->getTrace() : array()),
			'ErrorCode' => $this->ErrorCode
		);

		if(ob_get_contents()) ob_end_clean();

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			self::$DisplayFormat = self::DISPLAY_FORMAT_REST_JSON;
		}

		switch(self::$DisplayFormat) {
			case self::DISPLAY_FORMAT_REST_JSON:
				$this->_showMessageJSON($error);
				break;
			case self::DISPLAY_FORMAT_REST_XML:
			    $this->_showMessageXML($error);
				break;
			case self::DISPLAY_FORMAT_CLI:
			case self::DISPLAY_FORMAT_HTML:
			default:
				$this->_showMessageHTML($error);
		}

		$this->_errorShowed = true;

		die(0);
	}

	private function _showMessageHTML($error) {
		extract($error);

		if(is_file($ex_path = NS_SYSTEM_PATH . '/asset/template/error/' . str_replace('\\', '.', get_class($this)) . '.php')) include($ex_path);
		else if(is_file($ex_path = $this->DefaultMessageTemplate)) include($ex_path);
		else {
			echo sprintf('NS Error Message: %s<br/>Exception: %s<br/>File: %s line %s', $this->Message, $this->Source, $this->File, $this->Line);
			if(NS_DEBUG_MODE) {
				$counter = 0;
				foreach($Trace as $x) {
					if(isset($x['file'])) {
						echo '<br />Trace ', $counter++, ': ', $x['file'], ' line ',  $x['line'];
					}
				}
			}
		}
	}

	private function _showMessageJSON($error) {
		$error = array(
			'code' => $error['ErrorCode'],
			'message' => $error['Message'],
			'exception' => $error['Source'],
			'file' => $error['File'],
			'line' => $error['Line'],
			'last_output' => $error['LastOutput'],
			'trace' => $error['Trace']
		);

		if(!NS_DEBUG_MODE) {
			unset($error['exception'], $error['file'], $error['line'], $error['last_output'], $error['trace']);
		}

		header('Content-type: application/json');
		echo @json_encode($error);
	}

	private function _showMessageXML($error) {
		$error = array(
			'Code' => $error['ErrorCode'],
			'Message' => $error['Message'],
			'Exception' => $error['Source'],
			'File' => $error['File'],
			'Line' => $error['Line'],
			'LastOutput' => $error['LastOutput'],
			'Trace' => $error['Trace']
		);

		if(!NS_DEBUG_MODE) {
			unset($error['Exception'], $error['File'], $error['Line'], $error['Last_output'], $error['Trace']);
		}

		header('Content-type: application/xml');
		echo XML::fromArray($error, 'Error');
	}
}
?>