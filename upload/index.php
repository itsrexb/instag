<?php
// Version
define('VERSION', '2.0.0.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);

// Store
if ($_SERVER['HTTPS']) {
	$store_query = $db->query("SELECT * FROM `" . DB_PREFIX . "store` WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
} else {
	$store_query = $db->query("SELECT * FROM `" . DB_PREFIX . "store` WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
}

if ($store_query->num_rows) {
	$config->set('config_store_id', $store_query->row['store_id']);
} else {
	$config->set('config_store_id', 0);
	$config->set('config_url', HTTP_SERVER);
	$config->set('config_ssl', HTTPS_SERVER);
}

// Settings
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $result) {
	if (!$result['serialized']) {
		$config->set($result['key'], $result['value']);
	} else {
		$config->set($result['key'], json_decode($result['value'], true));
	}
}

// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $url);

// Log
$log = new Log($registry);
$registry->set('log', $log);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
$session = new Session();

if (isset($request->post['token'])) {
	$token = $request->post['token'];
} else if (isset($request->get['token'])) {
	$token = $request->get['token'];
} else {
	$token = '';
}

if ($token && isset($request->get['route']) && substr($request->get['route'], 0, 4) == 'api/') {
	$db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE TIMESTAMPADD(HOUR, 1, date_modified) < NOW()");

	$query = $db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "api` `a` LEFT JOIN `" . DB_PREFIX . "api_session` `as` ON (a.api_id = as.api_id) WHERE a.status = '1' AND as.token = '" . $db->escape($token) . "'");

	if ($query->num_rows) {
		$session->start($query->row['session_id'], $query->row['session_name']);

		$registry->set('session', $session);

		// keep the session alive
		$db->query("UPDATE `" . DB_PREFIX . "api_session` SET date_modified = NOW() WHERE api_session_id = '" . (int)$query->row['api_session_id'] . "'");
	}
} else {
	$session->start();
}

$registry->set('session', $session);

// Language Detection
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'");

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

if (isset($session->data['language'])) {
	$code = $session->data['language'];
} else {
	$code = '';
}

if (isset($request->cookie['language']) && (!$code || !array_key_exists($code, $languages))) {
	$code = $request->cookie['language'];
}

if (!empty($request->server['HTTP_ACCEPT_LANGUAGE']) && (!$code || !array_key_exists($code, $languages))) {
	$detect = '';

	$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);

	foreach ($browser_languages as $browser_language) {
		$browser_language = explode(';', $browser_language)[0];

		foreach ($languages as $key => $value) {
			if ($value['status']) {
				$locale = explode(',', $value['locale']);

				if (in_array($browser_language, $locale)) {
					$detect = $key;
					break 2;
				}
			}
		}
	}

	if ($detect) {
		$code = $detect;
	}
}

if (!$code || !array_key_exists($code, $languages)) {
	$code = $config->get('config_language');
}

if (!isset($session->data['language']) || $session->data['language'] != $code) {
	$session->data['language'] = $code;
}

if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
	setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
}

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language
$language = new Language($languages[$code]['directory']);
$language->load($languages[$code]['directory']);
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// User
$registry->set('user', new Cart\User($registry));

// Customer
$customer = new Cart\Customer($registry);
$registry->set('customer', $customer);

// Customer Group
if ($customer->isLogged()) {
	$config->set('config_customer_group_id', $customer->getGroupId());
} elseif (isset($session->data['customer']) && isset($session->data['customer']['customer_group_id'])) {
	// For API calls
	$config->set('config_customer_group_id', $session->data['customer']['customer_group_id']);
}

// instag Hive API
$registry->set('instaghive', new instagHive($registry));

// Tracking Code (30 days)
if (isset($request->get['tracking'])) {
	setcookie('tracking', $request->get['tracking'], time() + 3600 * 24 * 30, '/');
	$request->cookie['tracking'] = $request->get['tracking'];

	$db->query("UPDATE `" . DB_PREFIX . "marketing` SET clicks = (clicks + 1) WHERE code = '" . $db->escape($request->get['tracking']) . "'");
}

// External Affiliate ID (30 days)
if (isset($request->get['aff_id'])) {
	setcookie('ext_aff_id', $request->get['aff_id'], time() + 3600 * 24 * 30, '/');
	$request->cookie['ext_aff_id'] = $request->get['aff_id'];
}

// MailChimp Campaign ID (5 days)
if (isset($request->get['mc_cid'])) {
	setcookie('mc_cid', $request->get['mc_cid'], time() + 3600 * 24 * 5, '/');
	$request->cookie['mc_cid'] = $request->get['mc_cid'];
}

// Affiliate
$registry->set('affiliate', new Cart\Affiliate($registry));

// Currency
$registry->set('currency', new Cart\Currency($registry, $customer->getCurrencyCode()));

// Tax
$registry->set('tax', new Cart\Tax($registry));

// Cart
$registry->set('cart', new Cart\Cart($registry));

// Encryption
$registry->set('encryption', new Encryption($config->get('config_encryption')));

// Front Controller
$controller = new Front($registry);

// Maintenance Mode
$controller->addPreAction(new Action('common/maintenance'));

// SEO URL's
$controller->addPreAction(new Action('common/seo_url'));

// Router
if (isset($request->get['route'])) {
	$route = new Action($request->get['route']);
} else {
	$route = new Action('common/home');
}

// Dispatch
$controller->dispatch($route, new Action('error/not_found'));

// Output
$response->output();