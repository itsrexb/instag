<?php
namespace instagHive;
class Activity extends instagHiveAPI {
	private $route = 'account/activity/';

	public function get_list($account_id, $activity = '', $limit = 0, $last_evaluated_key = '') {
		$data = array(
			'account_id' => $account_id
		);

		if ($activity) {
			$data['activity'] = $activity;
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