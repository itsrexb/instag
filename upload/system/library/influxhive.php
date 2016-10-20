<?php
require_once('instaghive/api.php');
class instagHive {
	const URL_PROD = 'http://hive.instagsocial.com/api/v1/';
	const URL_TEST = 'http://devhive.instagsocial.com/api/v1/';

	private $key;
	private $token;
	private $url;

	protected $apis = array();

	public function __construct($registry, $auto_login = true) {
		$this->customer = $registry->get('customer');
		$this->session  = $registry->get('session');

		$this->key = $registry->get('config')->get('config_instaghive_key');

		if ($registry->get('config')->get('config_instaghive_environment') == 'development') {
			$this->url = self::URL_TEST;
		} else {
			$this->url = self::URL_PROD;
		}

		if (isset($this->session->data['instaghive_token'])) {
			$this->token = $this->session->data['instaghive_token'];
		} else if ($auto_login && $this->customer && $this->customer->isLogged()) {
			$this->login();
		}
	}

	public function login($customer_id = 0) {
		if (!$customer_id && $this->customer) {
			$customer_id = $this->customer->getId();
		}

		$result = $this->getResponse($this->url . 'customer/login', array(
			'key'         => $this->key,
			'customer_id' => $customer_id
		));

		if ($result->success) {
			$this->setToken($result->token);

			return true;
		}

		return false;
	}

	public function logout() {
		$this->setToken('');

		if (isset($this->session->data['instaghive_token'])) {
			$result = $this->getResponse($this->url . 'customer/logout', array(
				'token' => $this->token
			));
		}
	}

	public function setToken($token = '') {
		$this->token = $token;

		if ($token) {
			$this->session->data['instaghive_token'] = $token;
		} else if (isset($this->session->data['instaghive_token'])) {
			unset($this->session->data['instaghive_token']);
		}
	}

	public function request($route, $data = array(), $force_token = false) {
		if ($this->token && !$force_token) {
			$data['token'] = $this->token;
		} else if ($this->key) {
			$data['key'] = $this->key;
		}

		$result = $this->getResponse($this->url . $route, $data);

		if (isset($result->errors['token'])) {
			// the request used an invalid token, try logging in and getting a new token and trying the request again
			if ($this->login()) {
				$data['token'] = $this->token;

				$result = $this->getResponse($this->url . $route, $data);
			}
		}

		return $result;
	}

	private function getResponse($url, $data = array()) {
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

		$response = curl_exec($curl);

		curl_close($curl);

		if (!$response) {
			$response = json_encode(array('success' => false));
		}

		$result = @json_decode($response);

		if (isset($result->errors) && is_object($result->errors)) {
			$result->errors = (array)$result->errors;
		}

		return $result;
	}

	public function __get($key) {
		if (isset($this->apis[$key])) {
			return $this->apis[$key];
		} else if (file_exists(dirname(__FILE__) . '/instaghive/' . $key . '.php')) {
			$class = '\instagHive\\' . ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $key));

			$this->apis[$key] = new $class($this);

			return $this->apis[$key];
		} else {
			return false;
		}
	}
}