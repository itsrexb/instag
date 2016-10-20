<?php
namespace instagHive;
class ProfileHistory extends instagHiveAPI {
	private $route = 'account/profile_history/';

	public function get_list($account_id, $filters = array()) {
		$data = array(
			'account_id' => $account_id
		);

		if ($filters) {
			$data['filters'] = $filters;
		}

		$result = $this->client->request($this->route . 'get_list', $data);

		if ($result && $result->success) {
			return (array)$result->data;
		}

		return array();
	}
}