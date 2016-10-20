<?php
// Version
define('VERSION', '2.0.0.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: ../install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0'");

foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], json_decode($setting['value'], true));
	}
}

// convert dates in database to configured timezone
$db->setTimeZone($config->get('config_timezone'));

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Url
$url = new Url(HTTP_SERVER, $config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER);
$registry->set('url', $url);

// Log
$log = new Log($registry);
$registry->set('log', $log);

// Error Handler
set_error_handler(array($log, 'error_handler'));

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
$session = new Session();
$session->start();
$registry->set('session', $session);

// User
$registry->set('user', new Cart\User($registry));

// Customer
$customer = new Cart\Customer($registry);
$registry->set('customer', $customer);

// Affiliate
$affiliate = new Cart\Affiliate($registry);
$registry->set('affiliate', $affiliate);

// instag Hive
$registry->set('instaghive', new instagHive($registry, false));

// Language
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language`");

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

// Language
$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// Currency
$registry->set('currency', new Cart\Currency($registry, $config->get('config_currency')));

// Front Controller
$controller = new Front($registry);

// Compile Sass
$controller->addPreAction(new Action('common/sass'));

// Login
$controller->addPreAction(new Action('common/login/check'));

// Permission
$controller->addPreAction(new Action('error/permission/check'));

// Router
if (isset($request->get['route'])) {
	$route = new Action($request->get['route']);
} else {
	$route = new Action('common/dashboard');
}

// Dispatch
$controller->dispatch($route, new Action('error/not_found'));

// Output
$response->output();