<?php
namespace Moip;
class Payments extends MoipAPI {
	private $route = 'payments/';

	public function capture($payment_id) {
		$result = $this->client->request($this->route . $payment_id . '/capture', $data, 'POST');

		return $result;
	}

	public function refund($payment_id, $amount = 0) {
		$data = array();

		if ($amount) {
			$data['amount'] = $amount;
		}

		$result = $this->client->request($this->route . $payment_id . '/refunds', $data, 'POST');

		return $result;
	}
}