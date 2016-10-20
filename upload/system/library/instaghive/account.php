<?php
namespace instagHive;
class Account extends instagHiveAPI {
	private $route = 'account/account/';

	public function start($account_id) {
		$data = array(
			'account_id' => $account_id
		);

		$result = $this->client->request($this->route . 'start', $data);

		return $result->success;
	}

	public function stop($account_id) {
		$data = array(
			'account_id' => $account_id
		);

		$result = $this->client->request($this->route . 'stop', $data);

		return $result->success;
	}

	public function flow($account_id, $flow) {
		$data = array(
			'account_id' => $account_id,
			'flow'       => $flow
		);

		$result = $this->client->request($this->route . 'flow', $data);

		return $result->success;
	}

	public function delete($account_id) {
		$data = array(
			'account_id' => $account_id
		);

		$result = $this->client->request($this->route . 'delete', $data);

		return $result->success;
	}

	public function edit_expiration($account_id, $date) {
		$data = array(
			'account_id' => $account_id,
			'date'       => $date
		);

		$result = $this->client->request($this->route . 'update_expiration', $data);

		return $result->success;
	}

	public function update_expiration($account_id, $extension, $frequency) {
		$data = array(
			'account_id' => $account_id,
			'extension'  => $extension,
			'frequency'  => $frequency
		);

		$result = $this->client->request($this->route . 'update_expiration', $data);

		return $result->success;
	}

	public function update_username($account_id, $username) {
		$data = array(
			'account_id' => $account_id,
			'username'   => $username
		);

		$result = $this->client->request($this->route . 'update_username', $data);

		return $result->success;
	}

	public function get($account_id, $get_settings = false) {
		$data = array(
			'account_id' => $account_id
		);

		if ($get_settings) {
			$data['get_settings'] = $get_settings;
		}

		$result = $this->client->request($this->route . 'get', $data);

		if ($result && $result->success) {
			return $result->data;
		}

		return array();
	}

	public function get_list($type = '') {
		$data = array();

		if ($type) {
			$data['type'] = $type;
		}

		$result = $this->client->request($this->route . 'get_list', $data);

		if ($result && $result->success) {
			return (array)$result->data;
		}

		return array();
	}
}