<?php
namespace instagHive;
class AccountSetting extends instagHiveAPI {
	private $route = 'account/setting/';

	public function update($account_id, $setting_data) {
		$data = array(
			'account_id' => $account_id,
			'data'       => $setting_data
		);

		$result = $this->client->request($this->route . 'update', $data);

		if ($result) {
			return $result;
		} else {
			return false;
		}
	}

	public function update_attribute($account_id, $attribute, $value, $action = '') {
		$data = array(
			'account_id' => $account_id,
			'attribute'  => $attribute,
			'value'      => $value
		);

		if ($action) {
			$data['action'] = $action;
		}

		$result = $this->client->request($this->route . 'update_attribute', $data);

		if ($result) {
			return $result->success;
		} else {
			return false;
		}
	}

	public function clear_list($account_id, $attribute) {
		$data = array(
			'account_id' => $account_id,
			'attribute'  => $attribute
		);

		$result = $this->client->request($this->route . 'clear_list', $data);

		if ($result) {
			return $result->success;
		} else {
			return false;
		}
	}

	public function get($account_id, $attributes = '') {
		$data = array(
			'account_id' => $account_id
		);

		if ($attributes) {
			$data['attributes'] = $attributes;
		}

		$result = $this->client->request($this->route . 'get', $data);

		if ($result && $result->success) {
			return $result->data;
		} else {
			return array();
		}
	}
}