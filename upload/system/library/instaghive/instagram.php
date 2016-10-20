<?php
namespace instagHive;
class Instagram extends instagHiveAPI {
	private $route = 'instagram/';

	public function insert($username, $password) {
		$data = array(
			'username' => $username,
			'password' => $password
		);

		return $this->client->request($this->route . 'insert', $data);
	}

	public function reconnect($account_id, $username, $password) {
		$data = array(
			'account_id' => $account_id,
			'username'   => $username,
			'password'   => $password
		);

		return $this->client->request($this->route . 'reconnect', $data);
	}

	public function already_existed($network_id) {
		$data = array(
			'network_id' => $network_id,
			'type'       => 'instagram'
		);

		$result = $this->client->request($this->route . 'already_existed', $data);

		return (($result && $result->success) ? $result->existed : false);
	}

	public function import_whitelist($account_id, $limit = 0) {
		$data = array(
			'account_id' => $account_id
		);

		if ($limit) {
			$data['quantity'] = $limit;
		}

		$result = $this->client->request($this->route . 'import_whitelist', $data);

		if ($result && $result->success) {
			return $result->data;
		}

		return array();
	}

	public function get($account_id, $get_data, $network_id = 'self') {
		$data = array(
			'account_id' => $account_id,
			'network_id' => $network_id,
			'get_data'   => $get_data
		);

		return $this->client->request($this->route . 'get', $data);
	}

	public function info($account_id, $network_id = 'self') {
		$data = array(
			'account_id' => $account_id,
			'network_id' => $network_id
		);

		return $this->client->request($this->route . 'info', $data);
	}

	public function recent_media($account_id, $network_id = 'self') {
		$data = array(
			'account_id' => $account_id,
			'network_id' => $network_id
		);

		return $this->client->request($this->route . 'recent_media', $data);
	}

	public function search_users($account_id, $username) {
		$data = array(
			'account_id' => $account_id,
			'username'   => $username
		);

		$result = $this->client->request($this->route . 'search_users', $data);

		if ($result && $result->success) {
			return $result->data;
		}

		return array();
	}

	public function search_tags($account_id, $tag) {
		$data = array(
			'account_id' => $account_id,
			'tag'        => $tag
		);

		$result = $this->client->request($this->route . 'search_tags', $data);

		if ($result && $result->success) {
			return $result->data;
		}

		return array();
	}

	public function search_locations($account_id, $query) {
		$data = array(
			'account_id' => $account_id,
			'query'      => $query
		);

		$result = $this->client->request($this->route . 'search_locations', $data);

		if ($result && $result->success) {
			return $result->data;
		}

		return array();
	}
}