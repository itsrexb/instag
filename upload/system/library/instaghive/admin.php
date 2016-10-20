<?php
namespace instagHive;
class Admin extends instagHiveAPI {
	private $route = 'admin/admin/';

	public function get_settings() {
		$result = $this->client->request($this->route . 'get_settings');

		if ($result && $result->success) {
			return $result->data;
		} else {
			return array();
		}
	}
}