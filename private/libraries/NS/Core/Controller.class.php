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
use NS\Template\Template;
use NS\Net\Http\ClientRequest;
use NS\IO\CacheManager;
use NS\UI\ScriptManager;
use NS\UI\StyleManager;

/**
 *Controller base class
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property string $Content Content generated from current controller
 *@property Template $View Template object that will be used for rendering in view file
 *@property Session $Session Session object that will handle session manipulation
 */
abstract class Controller extends Object {
	/**
	 *
	 * @var ClientRequest Client request get value or validate $_POST, $_GET and $_REQUEST
	 */
	public $Request;

	public $Path, $ControllerPath, $URL, $Action, $Params = array(), $Content;
	protected $_actionCache = null;
	private $_isConstructorCalled = false;

	function __construct() {
		ob_start();
		$this->createProperties(array('View' => Template::getInstance(), 'Session' => Session::getInstance()));
		$this->setReadOnlyProperties(array('View', 'Session'));

		$this->Request = ClientRequest::getInstance();
		$this->_isConstructorCalled = true;
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
		if(isset($this->_actionCache[$this->Action])) {
			if(($this->Content = CacheManager::getInstance()->read(str_replace('\\', '.', get_class($this)) . '.' . $this->Action . '.' . md5(implode($this->Params))))) {
				StyleManager::getInstance()->addSource(NS_CACHE_PATH . '/' . str_replace('\\', '.',get_class($this)) . '.' . $this->Action . '.' . md5(implode($this->Params)) . '.css');
				ScriptManager::getInstance()->addSource(NS_CACHE_PATH . '/' . str_replace('\\', '.',get_class($this)) . '.' . $this->Action . '.' . md5(implode($this->Params)) . '.js');

				echo $this->Content; return;
			}
		}

		call_user_func_array(array($this, $this->Action), $this->Params);
	}

	/**
	 *Finalize controller
	 *
	 */
	protected function _finalize() {
		if($this->View->File != null) {
			$this->View->display(($this->View->Path == '' ? $this->Path . '/views/' : $this->View->Path . '/') . $this->View->File);
		}

		$this->Content = ob_get_clean();

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
	protected function redirectURL($url, $message = null, $header = null, $time = 4) {
		ns_gettext_init('NS');

		if($message == null) header('Location: ' . $url);
		else {
			if(ob_get_contents()) ob_end_clean();
			if($header == null) $header = _('Redirecting...');
			
			if(is_file($ex_path = NS_SYSTEM_PATH . '/asset/template/redirect.php')) {
				extract(array('Header' => $header, 'Message' => $message, 'Time' => $time, 'URL' => $url, 'IfNotReload' => _('If the page does not automatically reload, please click here')));
				include($ex_path);
			} else {
				echo '<html><head><title>' . $header . '</title><meta charset="utf-8"><meta http-equiv="Refresh" content="' . $time . '; url=' . $url . '" /></head><body><h2>' . $message . '</h2><h3><a href="' . $url . '">' . _('If the page does not automatically reload, please click here') . '</a></h3></body></html>';
			}
		}

		exit;
	}

	/**
	 *Initialize cache for selected method
	 *
	 */
	protected function actionCache($actions) {
		$this->_actionCache = array_fill_keys($actions, true);
	}
}
?>
