<?php
namespace instagHive;
class Event extends instagHiveAPI {
	private $route = 'account/event/';

	public function insert($account_id, $code, $message = '') {
		$data = array(
			'account_id' => $account_id,
			'code'       => $code
		);

		if ($message) {
			$data['message'] = $message;
		}

		$result = $this->client->request($this->route . 'insert', $data);

		return $result->success;
	}

	public function get_list($account_id, $limit = 0) {
		$data = array(
			'account_id' => $account_id
		);

		if ($limit) {
			$data['limit'] = $limit;
		}

		$result = $this->client->request($this->route . 'get_list', $data);

		if ($result && $result->success) {
			return (array)$result->data;
		}

		return array();
	}
}