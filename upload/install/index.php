<?php
// Error Reporting
error_reporting(E_ALL);

// HTTPS
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else if (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// HTTP
$protocol = ($_SERVER['HTTPS']) ? "https://" : "http://";
define('HTTP_SERVER', $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/.\\') . '/');
define('HTTP_instag', $protocol . $_SERVER['HTTP_HOST'] . rtrim(rtrim(dirname($_SERVER['SCRIPT_NAME']), 'install'), '/.\\') . '/');

// DIR
define('DIR_APPLICATION', str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_SYSTEM', str_replace('\\', '/', realpath(dirname(__FILE__) . '/../')) . '/system/');
define('DIR_instag', str_replace('\\', '/', realpath(DIR_APPLICATION . '../')) . '/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Url
$url = new Url(HTTP_SERVER);
$registry->set('url', $url);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=UTF-8');
$registry->set('response', $response);

// Language
$language = new Language('english');
$language->load('english');
$registry->set('language', $language);

// Document
$document = new Document();
$registry->set('document', $document);

// Session
$session = new Session();
$registry->set('session', $session);

// Upgrade
$upgrade = false;

if (file_exists('../config.php')) {
	if (filesize('../config.php') > 0) {
		$upgrade = true;

		$lines = file(DIR_instag . 'config.php');

		foreach ($lines as $line) {
			if (strpos(strtoupper($line), 'DB_') !== false) {
				eval($line);
			}
		}
	}
}

// Front Controller
$controller = new Front($registry);

// Router
if (isset($request->get['route'])) {
	$action = new Action($request->get['route']);
} elseif ($upgrade) {
	$action = new Action('upgrade');
} else {
	$action = new Action('step_1');
}

// Dispatch
$controller->dispatch($action, new Action('not_found'));

// Output
$response->output();