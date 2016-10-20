<?php
class ModelPaymentBraintree extends Model {
	public function getMethod() {
		$braintree_currencies = $this->config->get('braintree_currencies');

		if (isset($braintree_currencies[$this->currency->getCode()]) && $braintree_currencies[$this->currency->getCode()]['status']) {
			$this->language->load('payment/braintree');

			$method_data = array(
				'code'       => 'braintree',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('braintree_sort_order')
			);
		} else {
			$method_data = array();
		}

		return $method_data;
	}

	public function addAssets($force = false) {
		$braintree_currencies = $this->config->get('braintree_currencies');

		if ($force || (isset($braintree_currencies[$this->currency->getCode()]) && $braintree_currencies[$this->currency->getCode()]['status'])) {
			$this->document->addScript('//js.braintreegateway.com/v2/braintree.js', 'footer');
		}
	}

	public function getBraintreeCustomerId($customer_id) {
		$braintree_customer_id = '';

		$query = $this->db->query("SELECT managed_billing, affiliate_id, braintree_customer_id FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");

		if ($query->row) {
			if ($query->row['affiliate_id']) {
				$affiliate_query = $this->db->query("SELECT braintree_customer_id FROM `" . DB_PREFIX . "affiliate` WHERE affiliate_id = '" . (int)$query->row['affiliate_id'] . "'");

				if ($affiliate_query->row) {
					$braintree_customer_id = $affiliate_query->row['braintree_customer_id'];
				}
			}

			// if there is no special braintree_customer_id, use the one attached to the customer
			if (!$braintree_customer_id) {
				$braintree_customer_id = $query->row['braintree_customer_id'];
			}
		}

		return $braintree_customer_id;
	}

	public function updateBraintreeCustomerId($customer_id, $braintree_customer_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET braintree_customer_id = '" . $this->db->escape($braintree_customer_id) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getPaymentMethodTokenForAccount($customer_id, $account_id) {
		$query = $this->db->query("SELECT metadata FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "' AND payment_code = 'braintree' AND active = '1' ORDER BY recurring_order_id ASC LIMIT 1");

		if ($query->row) {
			$metadata = json_decode($query->row['metadata'], true);

			return $metadata['braintree_payment_method_token'];
		}

		return '';
	}

	public function createBraintreeCustomer($data, $customer_id = 0) {
		$response_data = array('success' => false, 'message' => '');

		$customer_data = array(
			'firstName' => $data['firstname'],
			'lastName'  => $data['lastname'],
			'phone'     => $data['telephone'],
			'email'     => $data['email']
		);

		if (!empty($data['payment_method_nonce'])) {
			$customer_data['paymentMethodNonce'] = $data['payment_method_nonce'];
		}

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- Braintree\Customer::create REQUEST ---');
			$this->log->write($customer_data);
		}

		$result = Braintree\Customer::create($customer_data);

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- Braintree\Customer::create RESPONSE ---');
			$this->log->write($result);
		}

		if (!empty($result)) {
			if ($result->success) {
				$response_data['success'] = true;

				// get the braintree customer id and update it locally
				if (!empty($result->customer->id)) {
					$response_data['customer_id'] = $result->customer->id;

					if ($customer_id) {
						$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET braintree_customer_id = '" . $this->db->escape($result->customer->id) . "' WHERE customer_id = '" . (int)$customer_id . "'");
					}
				}

				// get the braintree payment method token
				if (!empty($result->customer->paymentMethods[0]->token)) {
					$response_data['payment_method_token'] = $result->customer->paymentMethods[0]->token;
				}
			} else {
				$response_data['message'] = $result->message;
			}
		}

		return $response_data;
	}

	public function createBraintreePaymentMethod($braintree_customer_id, $payment_method_nonce) {
		$response_data = array('success' => false, 'message' => '');

		$payment_method_data = array(
			'customerId'         => $braintree_customer_id,
			'paymentMethodNonce' => $payment_method_nonce
		);

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- Braintree\PaymentMethod::create REQUEST ---');
			$this->log->write($payment_method_data);
		}

		$result = Braintree\PaymentMethod::create($payment_method_data);

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- Braintree\PaymentMethod::create RESPONSE ---');
			$this->log->write($result);
		}

		if (!empty($result)) {
			if ($result->success) {
				$response_data['success'] = true;

				// get the braintree payment method token
				if (!empty($result->paymentMethod->token)) {
					$response_data['payment_method_token'] = $result->paymentMethod->token;
				}
			} else {
				$response_data['message'] = $result->message;
			}
		}

		return $response_data;
	}

	public function getBraintreePaymentMethods($braintree_customer_id) {
		$payment_methods = array();

		$braintree_customer = Braintree\Customer::find($braintree_customer_id);

		if (!empty($braintree_customer)) {
			foreach ($braintree_customer->paymentMethods as $result) {
				$payment_method = array();

				$payment_method['token']   = $result->token;
				$payment_method['default'] = $result->default;
				$payment_method['image']   = $result->imageUrl;

				if ($result instanceof Braintree\CreditCard) {
					$payment_method['type']        = strtolower($result->cardType);
					$payment_method['label']       = str_replace(' ', '-', $result->cardType);
					$payment_method['description'] = sprintf($this->language->get('text_description_cc'), $result->last4);
				} else if ($result instanceof Braintree\PayPalAccount) {
					$payment_method['type']        = 'paypal';
					$payment_method['label']       = 'PayPal';
					$payment_method['description'] = sprintf($this->language->get('text_description_pp'), $result->email);
				}

				if ($payment_method) {
					$payment_methods[] = $payment_method;
				}
			}
		}

		return $payment_methods;
	}

	public function recurringOrder($order_id, $recurring_order) {
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		// if order is free, don't try and make a payment
		if ($this->currency->format($order_info['total'], '', '', false) <= 0) {
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('braintree_order_status_id'), '', false);

			return true;
		}

		// check if braintree is already loaded before loading again
		if (!class_exists('Braintree\\Configuration')) {
			$this->load->vendor('braintree_php/lib/Braintree');
		}

		// setup the braintree environment
		Braintree\Configuration::reset();
		Braintree\Configuration::environment($recurring_order['metadata']['transaction_server']);
		Braintree\Configuration::merchantId($recurring_order['metadata']['merchant_id']);
		Braintree\Configuration::publicKey($recurring_order['metadata']['public_key']);
		Braintree\Configuration::privateKey($recurring_order['metadata']['private_key']);

		// setup the $braintree_data variable to be passed to braintree
		$braintree_data = array(
			'channel'            => 'BigPayout_SP',
			'amount'             => $this->currency->format($order_info['total'], '', '', false),
			'customerId'         => $recurring_order['metadata']['braintree_customer_id'],
			'orderId'            => $order_id,
			'paymentMethodToken' => $recurring_order['metadata']['braintree_payment_method_token'],
			'options' => array(
				'submitForSettlement' => ($this->config->get('braintree_transaction_method') == 'capture' ? true : false)
			)
		);

		// use the correct merchant account for the currency
		$braintree_currency_settings = $this->config->get('braintree_currencies')[$order_info['currency_code']];

		if ($braintree_currency_settings['merchant_account']) {
			$braintree_data['merchantAccountId'] = $braintree_currency_settings['merchant_account'];
		}

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- BRAINTREE REQUEST (recurringOrder/sale) ---');
			$this->log->write($braintree_data);
		}

		// attempt the transaction
		$result = Braintree\Transaction::sale($braintree_data);

		if ($this->config->get('braintree_debug')) {
			$this->log->write('--- BRAINTREE RESPONSE (recurringOrder/sale) ---');
			$this->log->write($result);
		}

		$transaction_info = false;

		if (!empty($result)) {
			if ($result->success) {
				$transaction_info = $result->transaction;
			} else {
				$message = 'Error Response: ' . $result->message;
			}
		} else {
			$message = 'Error Response: Empty gateway response';

			// empty gateway response, check to see if the transaction actually went through before considering this a failed transaction
			$transactions = Braintree\Transaction::search(array(
				Braintree\TransactionSearch::customerId()->is($recurring_order['metadata']['braintree_customer_id']),
				Braintree\TransactionSearch::orderId()->is($order_id)
			));

			if (!empty($transactions) && !empty($transactions->_ids)) {
				$transaction_id = reset($transactions->_ids);

				if ($transaction_id) {
					try {
						$transaction_info = Braintree\Transaction::find($transaction_id);
					} catch (Braintree\Exception\NotFound $e) {
						// not found
					}
				}
			}
		}

		if ($transaction_info) {
			$message  = 'Authorization Code: ' . $transaction_info->processorAuthorizationCode . "\n";
			$message .= 'AVS Response: ' . $transaction_info->avsErrorResponseCode . "\n";
			$message .= 'AVS Postal Code Response: ' . $transaction_info->avsPostalCodeResponseCode . "\n";
			$message .= 'AVS Street Address Response: ' . $transaction_info->avsStreetAddressResponseCode . "\n";
			$message .= 'Transaction ID: ' . $transaction_info->id . "\n";
			$message .= 'Card Code Response: ' . $transaction_info->cvvResponseCode . "\n";

			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('braintree_order_status_id'), $message, false);

			// add order transaction to table, this is used to view transactions made to an order and ability to void/refund
			$this->load->model('localisation/currency');

			$currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));

			$this->load->model('checkout/order_transaction');
			$this->model_checkout_order_transaction->addOrderTransaction($order_id, array(
				'transaction_id'     => $transaction_info->id,
				'authorization_code' => $transaction_info->processorAuthorizationCode,
				'payment_method'     => $order_info['payment_method'],
				'payment_code'       => $order_info['payment_code'],
				'amount'             => $order_info['total'],
				'currency_id'        => $order_info['currency_id'],
				'currency_code'      => $order_info['currency_code'],
				'currency_value'     => $order_info['currency_value'],
				'status'             => (($this->config->get('braintree_transaction_method') == 'capture') ? 1 : 0),
				'meta_data'          => array(
					'transaction_server' => $recurring_order['metadata']['transaction_server'],
					'merchant_id'        => $recurring_order['metadata']['merchant_id'],
					'public_key'         => $recurring_order['metadata']['public_key'],
					'private_key'        => $recurring_order['metadata']['private_key']
				)
			));

			return true;
		} else {
			$this->model_checkout_order->addOrderHistory($order_id, 0, $message, false);
		}

		return false;
	}
}