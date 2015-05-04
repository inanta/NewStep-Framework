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

use NS\Object;
use NS\Exception\Exception;
use NS\Template\Template;
use NS\Net\Http\ClientRequest;
use NS\IO\CacheManager;
use NS\UI\ScriptManager;
use NS\UI\StyleManager;
use NS\Utility\XML;

/**
 *Controller base class for REST
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $Content Content generated from current controller
 */
abstract class RESTController extends Object {
	/**
	 * Response type JSON
	 */
	const REST_TYPE_JSON = 2;

	/**
	 * Response type XML
	 */
	const REST_TYPE_XML = 3;

	/**
	 *
	 * @var integer HTTP response header code 
	 */
	public $ResponseHeaderCode = 200;

	/**
	 *
	 * @var string HTTP response header message
	 */
	public $ResponseHeaderMessage = 'OK';

	/**
	 *
	 * @var ClientRequest Client request get value or validate $_POST, $_GET and $_REQUEST
	 */
	public $Request;

	/**
	 *
	 * @var mixed REST response
	 */
	public $Response;

	/**
	 *
	 * @var integer Response type output
	 */
	public $ResponseType = self::REST_TYPE_JSON;

	public $Path, $ControllerPath, $URL, $Action, $Params = array(), $Content;
	protected $_actionCache = null;
	private $_isConstructorCalled = false;

	function __construct() {
		ob_start();
		$this->createProperties(array('Session' => Session::getInstance()));
		$this->setReadOnlyProperties(array('Session'));

		$this->Request = ClientRequest::getInstance();
		$this->_isConstructorCalled = true;

		Exception::$DisplayFormat =& $this->ResponseType;
	}

	/**
	 *Run controller
	 *
	 */
	function _run() {
		if(method_exists($this, '_main')) {
			$this->_main();
		}

		$this->_dispatch();
		$this->_finalize();
	}

	/**
	 *Create object property
	 *
	 */
	function _createProperty($k, $v = null) {
		$this->createProperty($k, $v);
	}

	/**
	 *Create object properties
	 *
	 */
	function _createProperties($p) {
		$this->createProperties($p);
	}

	/**
	 *Call another method by user request
	 *
	 */
	protected function _dispatch() {
		if(!$this->_isConstructorCalled) throw new \NS\Exception\Exception('Parent constructor is not called');

		call_user_func_array(array($this, $this->Action), $this->Params);
	}

	/**
	 *Finalize controller
	 *
	 */
	protected function _finalize() {
		$echo = ob_get_clean();

		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->ResponseHeaderCode . ' ' . $this->ResponseHeaderMessage);

		if($this->ResponseType == self::REST_TYPE_XML) {
			header('Content-type: application/xml');
			
			if($echo != '') {
				$this->Response['Output'] = $echo;
			}

			$this->Content = XML::fromArray($this->Response, 'Response');
		} else {
			header('Content-type: application/json');

			if($echo != '') {
				$this->Response['output'] = $echo;
			}

			$this->Content = @json_encode($this->Response);
		}

		if(isset($this->_actionCache[$this->Action])) {
			$cm = CacheManager::getInstance();
			if($cm->write(str_replace('\\', '.',get_class($this)) . '.' . $this->Action . '.' . md5(implode($this->Params)), $this->Content)) {
				$cm->write(str_replace('\\', '.',get_class($this)) . '.' . $this->Action . '.' . md5(implode($this->Params)) . '.css', StyleManager::getInstance()->get());
				$cm->write(str_replace('\\', '.',get_class($this)) . '.' . $this->Action . '.' . md5(implode($this->Params)) . '.js', ScriptManager::getInstance()->get());
			}
		}
	}

	/**
	 *Redirect to another controller or action
	 *
	 */
	protected function redirect($url, $message = null, $header = null, $time = 4) {
		$this->redirectURL(NS_BASE_URL . '/' . $url, $message, $header, $time);
	}

	/**
	 *Redirect full URL
	 *
	 */
	protected function redirectURL($url) {
		ns_gettext_init('NS');

		header('Location: ' . $url);

		exit;
	}
}
?>
