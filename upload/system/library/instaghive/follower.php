<?php
namespace instagHive;
class Follower extends instagHiveAPI {
	private $route = 'account/event_activity/';

	public function get_list($account_id, $date_start = "", $date_end = "", $code = 'follow', $limit = 0, $organic = true) {
		$data = array(
			'account_id' => $account_id
		);
		if ($date_start) {
			$data['date_start'] = $date_start;
		}
		if ($date_end) {
			$data['date_end'] = $date_end;
		}
		if ($code) {
			$data['code'] = $code;
		}

		if ($limit) {
			$data['limit'] = $limit;
		}

		if($organic){
			$data['organic'] = $organic;
		}
		$result = $this->client->request($this->route . 'get_list', $data);
		
		if ($result) {
			return $result->data;
		}

		return array();
	}
}