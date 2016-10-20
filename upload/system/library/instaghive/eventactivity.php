<?php
namespace instagHive;
class EventActivity extends instagHiveAPI {
	private $route = 'account/event_activity/';

	public function get_list($account_id, $code = '', $limit = 0, $last_evaluated_key = '') {
		$data = array(
			'account_id' => $account_id
		);

		if ($code) {
			$data['code'] = $code;
		}

		if ($limit) {
			$data['limit'] = $limit;
		}

		if($last_evaluated_key){
			$data['last_evaluated_key'] = $last_evaluated_key;
		}
		return $this->client->request($this->route . 'get_list', $data);
	}
}