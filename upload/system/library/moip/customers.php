<?php
namespace Moip;
class Customers extends MoipAPI {
	private $route = 'customers/';

	public function get($customer_id) {
		$result = $this->client->request($this->route . $customer_id);

		return $result;
	}

	public function create($data) {
		$result = $this->client->request($this->route, $data);

		return $result;
	}

	public function add_funding_instrument($customer_id, $data) {
		$result = $this->client->request($this->route . $customer_id . '/fundinginstruments', $data);

		if (!empty($result->method)) {
			if ($result->method == 'CREDIT_CARD') {
				return $result->creditCard->id;
			}
		}

		return false;
	}
}