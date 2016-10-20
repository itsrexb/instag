<?php
require_once('mailchimp/api.php');
class MailChimp {
	const URL_PRODUCTION = '.api.mailchimp.com/3.0/';

	private $url;
	private $authorization;

	protected $debug = false;
	protected $apis  = array();

	public function __construct($registry, $api_key = '') {
		if (!$api_key) {
			$api_key = $registry->get('config')->get('mailchimp_api_key');
		}

		list(, $dc) = explode('-', $api_key);

		$this->url = 'https://' . $dc . self::URL_PRODUCTION;

		$this->authorization = base64_encode('user:' . $api_key);

		$this->debug = $registry->get('config')->get('mailchimp_debug');
	}

	public function request($route, $data = array(), $method = '') {
		$url = $this->url . $route;

		$curl = curl_init();

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
		} else if ($method && $method != 'GET') {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		}

		if ($data) {
			if ($method == 'GET') {
				$url .= '?' . http_build_query($data);

				$json_data = '';
			} else {
				$json_data = json_encode($data);

				curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
			}
		} else {
			$json_data = '';
		}

		if ($this->debug) {
			// set the request headers to be included in the debug output on the response
			curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
		}

		curl_setopt($curl, CURLOPT_URL, $url);

		$response = curl_exec($curl);

		$http_status_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($this->debug) {
			trigger_error('--- MAILCHIMP REQUEST ---', E_USER_NOTICE);

			if ($json_data) {
				trigger_error(curl_getinfo($curl, CURLINFO_HEADER_OUT) . $json_data, E_USER_NOTICE);
			} else {
				trigger_error(curl_getinfo($curl, CURLINFO_HEADER_OUT), E_USER_NOTICE);
			}

			//trigger_error('--- MAILCHIMP CURL INFORMATION ---', E_USER_NOTICE);
			//trigger_error(print_r(curl_getinfo($curl), true), E_USER_NOTICE);

			trigger_error('--- MAILCHIMP RESPONSE ---', E_USER_NOTICE);
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
		$key = strtolower($key);
		if (isset($this->apis[$key])) {
			return $this->apis[$key];
		} else if (file_exists(dirname(__FILE__) . '/mailchimp/' . strtolower($key) . '.php')) {
			$class = '\Mailchimp\\' . ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $key));
			$this->apis[$key] = new $class($this);
			
			return $this->apis[$key];
		} else {
			return false;
		}
	}
}