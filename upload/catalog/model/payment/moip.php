<?php
class ModelPaymentMoip extends Model {
	public function getMethod() {
		if (in_array($this->currency->getCode(), $this->config->get('moip_currencies'))) {
			$this->language->load('payment/moip');

			$method_data = array(
				'code'       => 'moip',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('moip_sort_order')
			);
		} else {
			$method_data = array();
		}

		return $method_data;
	}

	public function addAssets($force = false) {
		if ($force || in_array($this->currency->getCode(), $this->config->get('moip_currencies'))) {
			$this->document->addScript('//assets.moip.com.br/v2/moip.min.js', 'footer');
		}
	}

	public function getMoipCustomerId($customer_id) {
		$moip_customer_id = '';

		$query = $this->db->query("SELECT managed_billing, affiliate_id, moip_customer_id FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");

		if ($query->row) {
			if ($query->row['affiliate_id']) {
				$affiliate_query = $this->db->query("SELECT moip_customer_id FROM `" . DB_PREFIX . "affiliate` WHERE affiliate_id = '" . (int)$query->row['affiliate_id'] . "'");

				if ($affiliate_query->row) {
					$moip_customer_id = $affiliate_query->row['moip_customer_id'];
				}
			}

			// if there is no special moip_customer_id, use the one attached to the customer
			if (!$moip_customer_id) {
				$moip_customer_id = $query->row['moip_customer_id'];
			}
		}

		return $moip_customer_id;
	}

	public function updateMoipCustomerId($customer_id, $moip_customer_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET moip_customer_id = '" . $this->db->escape($moip_customer_id) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getPaymentMethodIdForAccount($customer_id, $account_id) {
		$query = $this->db->query("SELECT metadata FROM `" . DB_PREFIX . "recurring_order` WHERE customer_id = '" . (int)$customer_id . "' AND account_id = '" . $this->db->escape($account_id) . "' AND payment_code = 'moip' AND active = '1' ORDER BY recurring_order_id ASC LIMIT 1");

		if ($query->row) {
			$metadata = json_decode($query->row['metadata'], true);

			return $metadata['moip_payment_method_id'];
		}

		return '';
	}

	public function createMoipCustomer($customer_id, $data) {
		$customer_data = array(
			'ownId'    => $customer_id,
			'fullname' => $data['firstname'] . ' ' . $data['lastname'],
			'email'    => $data['email'],
			'shippingAddress' => array(				// moip is dumb... forcing me to use a dummy address... wtf...
				'street'       => 'Avenida Faria Lima',
				'streetNumber' => 2927,
				'complement'   => 8,
				'district'     => 'Itaim',
				'city'         => 'Sao Paulo',
				'state'        => 'SP',
				'country'      => 'BRA',
				'zipCode'      => '01234000'
			)
		);

		// attempt to get brazil phone number data from a customers phone number
		/*
			COUNTRY CODE: 55
			AREA CODE:    2 digits (there is an area code the same as the country code)
			NUMBER:       8-9 digits

			2  2  8 - 9
			-- -- ---------
			55 75 777788889
		*/

		$phone = $data['telephone'];

		if ($phone) {
			$phone = preg_replace('/\D/', '', $phone);

			if (strlen($phone) > 11) {
				// remove country code from beginning of phone
				if (substr($phone, 0, 2) == '55') {
					$phone = substr($phone, 2);
				}
			}

			// remove 0's from beginning of phone
			$phone = ltrim($phone, '0');

			if (strlen($phone) == 11 || strlen($phone) == 10) {
				$customer_data['phone'] = array(
					'countryCode' => 55,
					'areaCode'    => (int)substr($phone, 0, 2),
					'number'      => (int)substr($phone, 2)
				);
			}
		}

		$result = $this->moip->customers->create($customer_data);

		if (!empty($result->id)) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET moip_customer_id = '" . $this->db->escape($result->id) . "' WHERE customer_id = '" . (int)$customer_id . "'");

			return $result->id;
		}

		return false;
	}

	public function createMoipPaymentMethod($moip_customer_id, $data) {
		$payment_method_id = $this->moip->customers->add_funding_instrument($moip_customer_id, $data);

		if ($payment_method_id) {
			$payment_method_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "moip_payment_method` WHERE payment_method_id = '" . $this->db->escape($payment_method_id) . "'");

			if ($payment_method_query->row) {
				$this->db->query("UPDATE `" . DB_PREFIX . "moip_payment_method` SET cvc = '" . (int)$data['creditCard']['cvc'] . "' WHERE payment_method_id = '" . $this->db->escape($payment_method_id) . "'");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "moip_payment_method` SET payment_method_id = '" . $this->db->escape($payment_method_id) . "', cvc = '" . (int)$data['creditCard']['cvc'] . "', date_added = NOW()");
			}

			return $payment_method_id;
		}

		return false;
	}

	public function getMoipPaymentMethods($moip_customer_id) {
		$payment_methods = array();

		$moip_customer = $this->moip->customers->get($moip_customer_id);

		if (!empty($moip_customer) && !empty($moip_customer->fundingInstruments)) {
			foreach ($moip_customer->fundingInstruments as $result) {
				$payment_method = array();

				if ($result->method == 'CREDIT_CARD') {
					$payment_method['id']          = $result->creditCard->id;
					$payment_method['type']        = strtolower($result->creditCard->brand);
					$payment_method['label']       = str_replace(' ', '-', $result->creditCard->brand);
					$payment_method['description'] = sprintf($this->language->get('text_description_cc'), $result->creditCard->last4);
				}

				if ($payment_method) {
					$payment_methods[] = $payment_method;
				}
			}
		}

		return $payment_methods;
	}

	public function getMoipPaymentMethodCvc($payment_method_id) {
		$query = $this->db->query("SELECT cvc FROM `" . DB_PREFIX . "moip_payment_method` WHERE payment_method_id = '" . $this->db->escape($payment_method_id) . "'");

		if ($query->row) {
			return $query->row['cvc'];
		}

		return '';
	}

	public function recurringOrder($order_id, $recurring_order) {
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		// if order is free, don't try and make a payment
		if ($this->currency->format($order_info['total'], '', '', false) <= 0) {
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('moip_order_status_id'), '', false);

			return true;
		}

		// setup the moip environment
		$moip = new Moip($this->registry);

		// setup the $order_data variable to be passed to moip
		$order_data = array(
			'ownId'  => $order_id,
			'amount' => array(
				'currency' => $order_info['currency_code']
			),
			'items'    => array(),
			'customer' => array(
				'id' => $recurring_order['metadata']['moip_customer_id'],
				'shippingAddress' => array(				// moip is dumb... forcing me to use a dummy address... wtf...
					'street'       => 'Avenida Faria Lima',
					'streetNumber' => 2927,
					'complement'   => 8,
					'district'     => 'Itaim',
					'city'         => 'Sao Paulo',
					'state'        => 'SP',
					'country'      => 'BRA',
					'zipCode'      => '01234000'
				)
			)
		);

		// attempt to get brazil phone number data from a customers phone number
		/*
			COUNTRY CODE: 55
			AREA CODE:    2 digits (there is an area code the same as the country code)
			NUMBER:       8-9 digits

			2  2  8 - 9
			-- -- ---------
			55 75 777788889
		*/

		$phone = $order_info['telephone'];

		if ($phone) {
			$phone = preg_replace('/\D/', '', $phone);

			if (strlen($phone) > 11) {
				// remove country code from beginning of phone
				if (substr($phone, 0, 2) == '55') {
					$phone = substr($phone, 2);
				}
			}

			// remove 0's from beginning of phone
			$phone = ltrim($phone, '0');

			if (strlen($phone) == 11 || strlen($phone) == 10) {
				$order_data['customer']['phone'] = array(
					'countryCode' => 55,
					'areaCode'    => (int)substr($phone, 0, 2),
					'number'      => (int)substr($phone, 2)
				);
			}
		}

		// add products as items
		$order_products = $this->model_checkout_order->getOrderProducts($order_id);

		foreach ($order_products as $order_product) {
			$order_data['items'][] = array(
				'product'  => $order_product['name'],
				'quantity' => $order_product['quantity'],
				'price'    => str_replace('.', '', $this->currency->format($order_product['price'], '', '', false))
			);
		}

		// attempt the transaction
		$moip_order_id = $moip->orders->create($order_data);

		if ($moip_order_id) {
			// get cvc for payment method
			$payment_method_query = $this->db->query("SELECT cvc FROM `" . DB_PREFIX . "moip_payment_method` WHERE payment_method_id = '" . $this->db->escape($recurring_order['metadata']['moip_payment_method_id']) . "'");

			if ($payment_method_query->row) {
				$result = $moip->orders->payment($moip_order_id, array(
					'delayCapture'      => ($this->config->get('moip_transaction_method') == 'capture' ? false : true),
					'fundingInstrument' => array(
						'method' => 'CREDIT_CARD',
						'creditCard' => array(
							'id'  => $recurring_order['metadata']['moip_payment_method_id'],
							'cvc' => $payment_method_query->row['cvc']
						)
					)
				));
			} else {
				$result = false;
			}
		} else {
			$result = false;
		}

		$transaction_info = false;

		if (!empty($result)) {
			if (!empty($result->id) && ($result->status == 'IN_ANALYSIS' || $result->status == 'AUTHORIZED' || $result->status == 'PRE_AUTHORIZED')) {
				$transaction_info = $result;
			} else {
				$message = 'Error Response:' . "\n";

				if (isset($result->cancellationDetails)) {
					$message .= $result->cancellationDetails->cancelledBy . ': ' . $result->cancellationDetails->description;
				} else if (isset($result->errors)) {
					foreach ($result->errors as $error) {
						$message .= $error->code . ': ' . $error->description;
					}
				} else {
					$message .= 'There was an error while processing your request.';
				}
			}
		} else {
			$message = 'Error Response: Empty gateway response';
		}

		if ($transaction_info) {
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('moip_order_status_id'), '', false);

			// add order transaction to table, this is used to view transactions made to an order and ability to void/refund
			$this->load->model('localisation/currency');

			$currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));

			$this->load->model('checkout/order_transaction');
			$this->model_checkout_order_transaction->addOrderTransaction($order_id, array(
				'transaction_id'     => $transaction_info->id,
				'authorization_code' => '',
				'payment_method'     => $order_info['payment_method'],
				'payment_code'       => $order_info['payment_code'],
				'amount'             => $order_info['total'],
				'currency_id'        => $order_info['currency_id'],
				'currency_code'      => $order_info['currency_code'],
				'currency_value'     => $order_info['currency_value'],
				'status'             => (($this->config->get('moip_transaction_method') == 'capture') ? 1 : 0),
				'meta_data'          => array(
					'environment' => $recurring_order['metadata']['environment'],
					'api_token'   => $recurring_order['metadata']['api_token'],
					'api_key'     => $recurring_order['metadata']['api_key'],
					'public_key'  => $recurring_order['metadata']['public_key']
				)
			));

			return true;
		} else {
			$this->model_checkout_order->addOrderHistory($order_id, 0, $message, false);
		}

		return false;
	}
}