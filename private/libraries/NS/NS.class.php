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

namespace NS;

use NS\Core\Config;
use NS\Core\Router;
use NS\Exception\Exception;
use NS\Exception\UIException;

define('NS_VERSION', 10);
define('NS_VERSION_NAME', 'Batik');

define('NS_SITE', 'http://ns.inationsoft.com');
define('INATIONSOFT_SITE', 'http://www.inationsoft.com');

/**
 *Main NewStep framework
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class NS {
	private $_eventCallback = array();

	function __construct(&$System) {
		try {
			register_shutdown_function(array($this, 'shutdown'));

			require(NS_SYSTEM_PATH . '/' . $System['LibrariesFolder'] . '/NS/ClassMapper.class.php');
			ClassMapper::$ClassPath = NS_SYSTEM_PATH . '/' . $System['LibrariesFolder'] . '/';
			spl_autoload_register(array($this, 'autoload'));
	
			require(NS_SYSTEM_PATH . '/' . $System['ConfigFolder'] . '/Event.inc.php');
			require(NS_SYSTEM_PATH . '/' . $System['ConfigFolder'] . '/Constant.inc.php');
			foreach($Constant as $k => $v) define($k, $v);

			$cf = Config::getInstance();
			$cf->bind($System);
			$cf->load('Application');

			error_reporting(E_ALL);
			ini_set('display_errors', $cf->Application->DebugMode);
			date_default_timezone_set($cf->Application->Timezone);
			if($cf->Application->SendInfoHeader) header('X-Powered-By: NewStep Framework - PHP/' . PHP_VERSION);
			$_SERVER['PHP_SELF'] .= (preg_match('/index.php$/', $_SERVER['PHP_SELF'])) ? (!empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '')) : '';

			require(NS_GETTEXT_PATH);
			if(isset($_GET['locale'])) $cf->Application->Locale = $_GET['locale'];
			T_setlocale(LC_ALL, $cf->Application->Locale);

			define('NS_DEBUG_MODE', $cf->Application->DebugMode);
			define('NS_CACHE', $cf->Application->Cache);
			define('NS_BASE_URL', $System['Domain'] . (!$cf->Application->URLRewrite ? $_SERVER['SCRIPT_NAME'] : str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'])));

			$this->triggerEvent($Event['beforeApplicationInit']);
			$router = Router::getInstance();
			$router->initialize($cf);
			$this->triggerEvent($Event['afterApplicationInit']);

			$router->App->_run();

			$this->triggerEvent($Event['beforeApplicationOutput']);

			if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && $cf->Application->HttpCompression && stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && function_exists('gzencode')) {
				header('Content-Encoding: gzip');
				echo gzencode($router->App->Content);
			} else {
				echo $router->App->Content;
			}

			$this->triggerEvent($Event['afterApplicationOutput']);

			if(defined('NS_JS') && !defined('NS_JS_RENDERED')) throw new UIException(array('code' => UIException::JS_NOT_RENDERED));
			if(defined('NS_CSS') && !defined('NS_CSS_RENDERED')) throw new UIException(array('code' => UIException::CSS_NOT_RENDERED));
		} catch (Exception $ex) {
			if(isset($router->File)) {
				foreach($ex->getTrace() as $trace) {
					if(isset($trace['file']) && $trace['file'] == $router->File) {
						$ex->Message = $ex->getMessage();
						$ex->Source = get_class($ex);
						$ex->File = (!defined('NS_DEBUG_MODE') || !NS_DEBUG_MODE ? end(explode('/', $router->File)) : $router->File);
						$ex->Line = $trace['line'];
			
						break;
					}
				}
			}
		
			try {
				$message = date('h:i:s') . "\n" . date('d/m/Y') . sprintf("\n" . '%s ' . "\n" . '%s ' . "\n" . '%s line %s' . "\n", $ex->Message, $ex->Source, $ex->File, $ex->Line);

				$counter = 0;
				foreach($ex->getTrace() as $x) {
					if(isset($x['file'])) {
						$message .= 'Trace ' . $counter++ . ': ' . $x['file'] . ' line ' .  $x['line'] . "\n";
					}
				}
	
				$message .= "\n\n";

				$log = new IO\FileWriter(NS_SYSTEM_PATH . '/asset/log/' . str_replace('\\', '.', get_class($ex)) . '.log', IO\FileWriter::MODE_APPEND);
				$log->write($message);
			} catch(\Exception $e) {
				$e->IsSendHttpHeader = false;
			}

			$ex->showMessage();
		}
	}

	function shutdown() {
		$is_fatal_error = false;
		$is_error = false;

		if (($error = error_get_last())) {
			switch($error['type']) {
				case E_ERROR: 
				case E_PARSE: 
				case E_CORE_ERROR: 
				case E_COMPILE_ERROR: 
				case E_USER_ERROR: 
					$is_fatal_error = true;
					break;
				case E_WARNING:
				case E_NOTICE:
					$is_error = true;
					break;
				
			}
		}
	
		if ($is_fatal_error) {
			if(ob_get_contents()) ob_end_clean();

			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');

			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				echo @json_encode(array('error' => true, 'message' => $error['message'], 'file' => $error['file'], 'line' => $error['line']));
			} else if(file_exists($er_file = NS_SYSTEM_PATH . '/asset/template/error/PHPFatalError.php')) {
				extract(array('Message' => $error['message'], 'File' => $error['file'], 'Line' => $error['line']));
				require($er_file);
			} else echo sprintf('PHP Fatal Error<br />NS Error Message: %s<br/>File: %s line %s', $error['message'], $error['file'], $error['line']);

			try {
				$log = new IO\FileWriter(NS_SYSTEM_PATH . '/asset/log/FatalError.log', IO\FileWriter::MODE_APPEND);
				$log->write(date('h:i:s') . "\n" . date('d/m/Y') . "\n" . sprintf('%s' . "\n" . '%s line %s' . "\n\n\n", $error['message'], $error['file'], $error['line']));
			} catch(\Exception $e) {
				$e->IsSendHttpHeader = false;
			}
		} elseif ($is_error) {
			try {
				$log = new IO\FileWriter(NS_SYSTEM_PATH . '/asset/log/NonFatalError.log', IO\FileWriter::MODE_APPEND);
				$log->write(date('h:i:s') . "\n" . date('d/m/Y') . "\n" . sprintf('%s' . "\n" . '%s line %s' . "\n\n\n", $error['message'], $error['file'], $error['line']));
			} catch(\Exception $e) {
				$e->IsSendHttpHeader = false;
			}
		} else {
			if(ob_get_contents()) ob_end_flush();
		}
	}

	function autoload($classname) {
		if($file = ClassMapper::getClassPath($classname)) require($file);
	}

	function triggerEvent(&$callback) {
		if(is_array($callback)) {
			foreach($callback as $event) {
				if(!isset($this->_eventCallback[$event['class']])) {
					$this->_eventCallback[$event['class']] = new $event['class'];
				}

				call_user_func_array(array($this->_eventCallback[$event['class']], $event['method']), $event['param']);
			}
		}
	}
}
?>