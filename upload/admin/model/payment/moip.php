<?php
class ModelPaymentMoip extends Model {
	public function capture($order_transaction_info, $amount) {
		// setup the moip environment
		$moip = new Moip(
			$this->registry,
			$order_transaction_info['meta_data']['environment'],
			$order_transaction_info['meta_data']['api_token'],
			$order_transaction_info['meta_data']['api_key']
		);

		// attempt to capture the authorized transaction
		$result = $moip->payments->capture($order_transaction_info['transaction_id']);

		// TODO: all of this once i figure out the response
		if (!empty($result)) {
			if ($result->status == 'AUTHORIZED') {
				$this->load->model('sale/order_transaction');

				$this->model_sale_order_transaction->captureOrderTransaction($order_transaction_info['order_transaction_id'], $amount);

				return true;
			} else {
				return $result->message;
			}
		} else {
			$this->log->write('--- MOIP ERROR (capture) --- Empty response');

			$this->language->load('payment/moip');

			return $this->language->get('error_unknown');
		}
	}

	public function refund($order_transaction_info, $amount) {
		$this->load->model('sale/order_transaction');

		// setup the moip environment
		$moip = new Moip(
			$this->registry,
			$order_transaction_info['meta_data']['environment'],
			$order_transaction_info['meta_data']['api_token'],
			$order_transaction_info['meta_data']['api_key']
		);

		$result = $moip->payments->refund($order_transaction_info['transaction_id'], $amount);

		// TODO: all of this once i figure out the response
		if (!empty($result)) {
			if (!empty($result->id)) {
				$this->load->model('sale/order');
				$order_info = $this->model_sale_order->getOrder($order_transaction_info['order_id']);

				$this->load->model('localisation/currency');
				$currency_info = $this->model_localisation_currency->getCurrency($order_info['currency_id']);

				$this->model_sale_order_transaction->addOrderTransaction($order_transaction_info['order_id'], array(
					'transaction_id'           => $result->id,
					'reference_transaction_id' => $order_transaction_info['transaction_id'],
					'authorization_code'       => '',
					'payment_method'           => 'Moip',
					'payment_code'             => 'moip',
					'amount'                   => -($amount / $currency_info['value']),
					'currency_id'              => $currency_info['currency_id'],
					'currency_code'            => $currency_info['code'],
					'currency_value'           => $currency_info['value'],
					'status'                   => 1,
					'meta_data'                => array(
						'environment' => $order_transaction_info['meta_data']['environment'],
						'api_token'   => $order_transaction_info['meta_data']['api_token'],
						'api_key'     => $order_transaction_info['meta_data']['api_key'],
						'public_key'  => $order_transaction_info['meta_data']['public_key']
					)
				));

				return 'refunded';
			} else {
				return $result->message;
			}
		} else {
			$this->log->write('--- MOIP ERROR (refund) --- Empty Response');

			$this->language->load('payment/moip');

			return $this->language->get('error_unknown');
		}
	}
}