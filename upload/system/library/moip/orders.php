<?php
namespace Moip;
class Orders extends MoipAPI {
	private $route = 'orders/';

	public function get($order_id) {
		$result = $this->client->request($this->route . $order_id);

		return $result;
	}

	public function create($data) {
		$result = $this->client->request($this->route, $data);

		return (!empty($result->id) ? $result->id : false);
	}

	public function payment($order_id, $data) {
		$result = $this->client->request($this->route . $order_id . '/payments/', $data);

		return $result;
	}
}