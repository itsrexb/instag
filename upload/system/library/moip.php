<?php
require_once('moip/api.php');
class Moip {
	const URL_PRODUCTION = 'https://api.moip.com.br/v2/';
	const URL_SANDBOX    = 'https://sandbox.moip.com.br/v2/';

	private $url;
	private $authorization;

	protected $debug = false;
	protected $apis  = array();

	public function __construct($registry, $environment = '', $api_token = '', $api_key = '') {
		if (!$environment) {
			$environment = $registry->get('config')->get('moip_environment');
		}

		if ($environment == 'sandbox') {
			$this->url = self::URL_SANDBOX;
		} else {
			$this->url = self::URL_PRODUCTION;
		}

		if (!$api_token) {
			$api_token = $registry->get('config')->get('moip_api_token');
		}

		if (!$api_key) {
			$api_key = $registry->get('config')->get('moip_api_key');
		}

		$this->authorization = base64_encode($api_token . ':' . $api_key);

		$this->debug = $registry->get('config')->get('moip_debug');
	}

	public function request($route, $data = array(), $method = '') {
		$curl = curl_init($this->url . $route);

		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Basic ' . $this->authorization
		));

		if ($method == 'POST' || ($data && !$method)) {
			curl_setopt($curl, CURLOPT_POST, 1);
		} else if ($method) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		}

		if ($data) {
			$json_data = json_encode($data);

			curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
		}

		if ($this->debug) {
			// set the request headers to be included in the debug output on the response
			curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
		}

		$response = curl_exec($curl);

		$http_status_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($this->debug) {
			trigger_error('--- MOIP REQUEST ---', E_USER_NOTICE);

			if ($data) {
				trigger_error(curl_getinfo($curl, CURLINFO_HEADER_OUT) . $json_data, E_USER_NOTICE);
			} else {
				trigger_error(curl_getinfo($curl, CURLINFO_HEADER_OUT), E_USER_NOTICE);
			}

			//trigger_error('--- MOIP CURL INFORMATION ---', E_USER_NOTICE);
			//trigger_error(print_r(curl_getinfo($curl), true), E_USER_NOTICE);

			trigger_error('--- MOIP RESPONSE ---', E_USER_NOTICE);
			trigger_error($http_status_code . ' - ' . $response, E_USER_NOTICE);
		}

		curl_close($curl);

		if ($response) {
			$result = @json_decode($response);
		} else {
			$result = false;
		}

		return $result;
	}

	public function __get($key) {
		if (isset($this->apis[$key])) {
			return $this->apis[$key];
		} else if (file_exists(dirname(__FILE__) . '/moip/' . $key . '.php')) {
			$class = '\Moip\\' . ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $key));

			$this->apis[$key] = new $class($this);

			return $this->apis[$key];
		} else {
			return false;
		}
	}
}