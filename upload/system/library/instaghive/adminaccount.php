<?php
namespace instagHive;
class AdminAccount extends instagHiveAPI {
	private $route = 'admin/account/';

	public function reactivate($customer_id, $account_id) {
		$data = array(
			'customer_id' => $customer_id,
			'account_id'  => $account_id
		);

		$result = $this->client->request($this->route . 'reactivate', $data, true);

		return ($result ? $result->success : false);
	}

	public function migrate($customer_id, $account_id, $new_customer_id) {
		$data = array(
			'customer_id'     => $customer_id,
			'account_id'      => $account_id,
			'new_customer_id' => $new_customer_id
		);

		$result = $this->client->request($this->route . 'migrate', $data, true);

		return ($result ? $result->success : false);
	}
}