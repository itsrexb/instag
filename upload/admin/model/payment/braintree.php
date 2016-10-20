<?php
class ModelPaymentBraintree extends Model {
	public function capture($order_transaction_info, $amount) {
		include_once(DIR_SYSTEM . 'vendor/braintree_php/lib/Braintree');

		// setup the braintree environment
		Braintree\Configuration::environment($order_transaction_info['meta_data']['transaction_server']);
		Braintree\Configuration::merchantId($order_transaction_info['meta_data']['merchant_id']);
		Braintree\Configuration::publicKey($order_transaction_info['meta_data']['public_key']);
		Braintree\Configuration::privateKey($order_transaction_info['meta_data']['private_key']);

		// attempt to capture the authorized transaction
		$result = Braintree\Transaction::submitForSettlement($order_transaction_info['transaction_id'], $amount);

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- BRAINTREE RESPONSE (submitForSettlement) ---');
			$this->log->write($result);
		}

		if (!empty($result)) {
			if ($result->success) {
				$this->load->model('sale/order_transaction');

				$this->model_sale_order_transaction->captureOrderTransaction($order_transaction_info['order_transaction_id'], $amount, '', $result->transaction->processorAuthorizationCode);

				return true;
			} else {
				return $result->message;
			}
		} else {
			$this->log->write('--- BRAINTREE ERROR (submitForSettlement) --- Empty response');

			$this->language->load('payment/braintree');

			return $this->language->get('error_unknown');
		}
	}

	public function refund($order_transaction_info, $amount) {
		$this->load->model('sale/order_transaction');

		$this->load->vendor('braintree_php/lib/Braintree');

		// setup the braintree environment
		Braintree\Configuration::environment($order_transaction_info['meta_data']['transaction_server']);
		Braintree\Configuration::merchantId($order_transaction_info['meta_data']['merchant_id']);
		Braintree\Configuration::publicKey($order_transaction_info['meta_data']['public_key']);
		Braintree\Configuration::privateKey($order_transaction_info['meta_data']['private_key']);

		// if the full amount is trying to be refunded, check to see if we can attempt to void the order and do so if possible
		if ($amount == $order_transaction_info['amount']) {
			// attempt the transaction
			$result = Braintree\Transaction::find($order_transaction_info['transaction_id']);

			if ($this->config->get('braintree_debug')) {
				$this->log->write('--- BRAINTREE RESPONSE (find) ---');
				$this->log->write($result);
			}

			if (!empty($result)) {
				if ($result->status == 'authorized' || $result->status == 'submitted_for_settlement') {
					// attempt to void the transaction
					$result = Braintree\Transaction::void($order_transaction_info['transaction_id']);

					if ($this->config->get('braintree_debug')) {
						$this->log->write('--- BRAINTREE RESPONSE (void) ---');
						$this->log->write($result);
					}

					if (!empty($result)) {
						if ($result->success) {
							$this->model_sale_order_transaction->voidOrderTransaction($order_transaction_info['order_transaction_id']);
						} else {
							return $result->message;
						}
					} else {
						$this->log->write('--- BRAINTREE ERROR (void) --- Empty Response');

						$this->language->load('payment/braintree');

						return $this->language->get('error_unknown');
					}

					return 'voided';
				}
			} else {
				$this->log->write('--- BRAINTREE ERROR (find) --- Empty Response');

				$this->language->load('payment/braintree');

				return $this->language->get('error_unknown');
			}
		}

		// if we've gotten this far, that means we're not able to void the transaction so let's refund it
		$result = Braintree\Transaction::refund($order_transaction_info['transaction_id'], $amount);

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- BRAINTREE RESPONSE (refund) ---');
			$this->log->write($result);
		}

		if (!empty($result)) {
			if ($result->success) {
				$this->load->model('sale/order');
				$order_info = $this->model_sale_order->getOrder($order_transaction_info['order_id']);

				$this->model_sale_order_transaction->addOrderTransaction($order_transaction_info['order_id'], array(
					'transaction_id'           => $result->transaction->id,
					'reference_transaction_id' => $order_transaction_info['transaction_id'],
					'authorization_code'       => $result->transaction->processorAuthorizationCode,
					'payment_method'           => 'Braintree',
					'payment_code'             => 'braintree',
					'amount'                   => -($amount / $order_transaction_info['currency_value']),
					'currency_id'              => $order_transaction_info['currency_id'],
					'currency_code'            => $order_transaction_info['currency_code'],
					'currency_value'           => $order_transaction_info['currency_value'],
					'status'                   => 1,
					'meta_data'                => array(
						'transaction_server' => $order_transaction_info['meta_data']['transaction_server'],
						'merchant_id'        => $order_transaction_info['meta_data']['merchant_id'],
						'public_key'         => $order_transaction_info['meta_data']['public_key'],
						'private_key'        => $order_transaction_info['meta_data']['private_key']
					)
				));

				return 'refunded';
			} else {
				return $result->message;
			}
		} else {
			$this->log->write('--- BRAINTREE ERROR (refund) --- Empty Response');

			$this->language->load('payment/braintree');

			return $this->language->get('error_unknown');
		}
	}
}