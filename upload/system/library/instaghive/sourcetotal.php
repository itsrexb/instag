<?php
namespace instagHive;
class SourceTotal extends instagHiveAPI {
	private $route = 'account/source_total/';

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