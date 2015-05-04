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

/* Error Reporting */
ini_set('display_errors', 0);
/* Error Reporting */

/* System Variable */
$System['SystemFolder'] = 'private';
$System['PublicFolder'] = 'public';
$System['ApplicationFolder'] = 'application';
$System['AssetFolder'] = 'asset';
$System['ConfigFolder'] = 'config';
$System['LibrariesFolder'] = 'libraries';
$System['LocaleFolder'] = 'locale';
/* System Variable */

define('NS_ROOT_PATH', __DIR__);
define('NS_SYSTEM_PATH', NS_ROOT_PATH . '/' . $System['SystemFolder']);
define('NS_PUBLIC_PATH', NS_ROOT_PATH . '/' . $System['PublicFolder']);

$System['Domain'] = 'http' . (empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
$System['Port'] = ($_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']);

define('NS_ROOT_URL', $System['Domain'] . substr($_SERVER['SCRIPT_NAME'], 0, -10) . $System['Port']);
define('NS_PUBLIC_URL', $System['Domain'] . substr($_SERVER['SCRIPT_NAME'], 0, -10) . '/' . $System['PublicFolder'] . $System['Port']);
define('NS_CURRENT_URL', $System['Domain'] . $_SERVER['REQUEST_URI'] . $System['Port']);

require(NS_SYSTEM_PATH . '/' . $System['LibrariesFolder'] . '/NS/NS.class.php');
new NS\NS($System);    