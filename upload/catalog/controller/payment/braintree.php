<?php
class ControllerPaymentBraintree extends Controller {
	private $error = array();

	public function index() {
		$data = $this->language->load('payment/braintree');

		if (isset($this->request->post['account_id'])) {
			$account_id = $this->request->post['account_id'];
		} else if (isset($this->request->get['account_id'])) {
			$account_id = $this->request->get['account_id'];
		} else {
			$account_id = '';
		}

		if ($account_id) {
			$data['confirm'] = $this->url->link('payment/braintree/confirm', 'account_id=' . $account_id, true);
			$data['update']  = $this->url->link('payment/braintree/update', 'account_id=' . $account_id, true);

			$this->load->vendor('braintree_php/lib/Braintree');

			// setup the braintree environment
			Braintree\Configuration::environment($this->config->get('braintree_transaction_server'));
			Braintree\Configuration::merchantId($this->config->get('braintree_merchant_id'));
			Braintree\Configuration::publicKey($this->config->get('braintree_public_key'));
			Braintree\Configuration::privateKey($this->config->get('braintree_private_key'));

			// generate client token
			$generate_data = array();

			$data['payment_methods']       = array();
			$data['active_payment_method'] = array();

			if ($this->customer->isLogged()) {
				$this->load->model('payment/braintree');

				$braintree_customer_id = $this->model_payment_braintree->getBraintreeCustomerId($this->customer->getId());

				if ($braintree_customer_id) {
					$generate_data['customerId'] = $braintree_customer_id;

					// get active recurring order to get the braintree_payment_method_token
					$payment_method_token = $this->model_payment_braintree->getPaymentMethodTokenForAccount($this->customer->getId(), $account_id);

					// get all payment methods associated with this customer
					$data['payment_methods'] = $this->model_payment_braintree->getBraintreePaymentMethods($braintree_customer_id);

					foreach ($data['payment_methods'] as $payment_method) {
						if (($payment_method_token && $payment_method['token'] == $payment_method_token) || (!$payment_method_token && $payment_method['default'])) {
							$data['active_payment_method'] = $payment_method;
							break;
						}
					}

					// if there is no active payment method, use the first payment method
					if ($data['payment_methods'] && !$data['active_payment_method']) {
						$data['active_payment_method'] = reset($data['payment_methods']);
					}
				}
			}

			try {
				$data['client_token'] = Braintree\ClientToken::generate($generate_data);

				return $this->load->view('payment/braintree', $data);
			} catch (Braintree\Exception\DownForMaintenance $e) {
				return false;
			}
		}
	}

	public function confirm() {
		$this->language->load('payment/braintree');

		$json = array('success' => false);

		$this->load->vendor('braintree_php/lib/Braintree');

		// setup the braintree environment
		Braintree\Configuration::environment($this->config->get('braintree_transaction_server'));
		Braintree\Configuration::merchantId($this->config->get('braintree_merchant_id'));
		Braintree\Configuration::publicKey($this->config->get('braintree_public_key'));
		Braintree\Configuration::privateKey($this->config->get('braintree_private_key'));

		$braintree_currency_settings = $this->config->get('braintree_currencies')[$this->currency->getCode()];

		// get nonce from payment method token
		if (empty($this->request->post['nonce']) && !empty($this->request->post['token'])) {
			$result = Braintree\PaymentMethodNonce::create($this->request->post['token']);

			if (!empty($result)) {
				$this->request->post['nonce'] = $result->paymentMethodNonce->nonce;
			}
		}

		if ($this->validateConfirm()) {
			$this->load->model('checkout/order');
			$this->load->model('payment/braintree');

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			$braintree_customer_id = $this->model_payment_braintree->getBraintreeCustomerId($order_info['customer_id']);

			$success = false;
			$message = $this->language->get('error_unknown');

			if ($this->currency->format($order_info['total'], '', '', false) > 0) {
				// setup the $data variable to be passed to braintree
				$data = array(
					'channel'            => 'BigPayout_SP',
					'amount'             => $this->currency->format($order_info['total'], '', '', false),
					'orderId'            => $this->session->data['order_id'],
					'paymentMethodNonce' => $this->request->post['nonce'],
					'options' => array(
						'storeInVault'        => true,
						'submitForSettlement' => ($this->config->get('braintree_transaction_method') == 'capture' ? true : false)
					)
				);

				// use the correct merchant account for the currency
				if ($braintree_currency_settings['merchant_account']) {
					$data['merchantAccountId'] = $braintree_currency_settings['merchant_account'];
				}

				// does this customer exist in braintree?
				if ($braintree_customer_id) {
					$data['customerId'] = $braintree_customer_id;
				} else {
					$data['customer'] = array(
						'firstName' => $order_info['firstname'],
						'lastName'  => $order_info['lastname'],
						'phone'     => $order_info['telephone'],
						'email'     => $order_info['email']
					);
				}

				if ($this->config->get('braintree_debug')) {
					$this->log->write('--- BRAINTREE SALE TRANSACTION REQUEST (confirm/sale) ---');
					$this->log->write($data);
				}

				// attempt the transaction
				$result = Braintree\Transaction::sale($data);

				if ($this->config->get('braintree_debug')) {
					$this->log->write('--- BRAINTREE SALE TRANSACTION RESPONSE (confirm/sale) ---');
					$this->log->write($result);
				}

				if (!empty($result)) {
					// get the braintree customer id and update it locally if needed
					if (!$braintree_customer_id && !empty($result->transaction->customerDetails->id)) {
						$braintree_customer_id = $result->transaction->customerDetails->id;

						if ($order_info['customer_id']) {
							$this->model_payment_braintree->updateBraintreeCustomerId($order_info['customer_id'], $result->transaction->customerDetails->id);
						}
					}

					if ($result->success) {
						$success = true;

						$message  = 'Authorization Code: ' . $result->transaction->processorAuthorizationCode . "\n";
						$message .= 'AVS Response: ' . $result->transaction->avsErrorResponseCode . "\n";
						$message .= 'AVS Postal Code Response: ' . $result->transaction->avsPostalCodeResponseCode . "\n";
						$message .= 'AVS Street Address Response: ' . $result->transaction->avsStreetAddressResponseCode . "\n";
						$message .= 'Transaction ID: ' . $result->transaction->id . "\n";
						$message .= 'Card Code Response: ' . $result->transaction->cvvResponseCode . "\n";

						// get the braintree payment method token
						if (!empty($result->transaction->creditCardDetails->token)) {
							$braintree_payment_method_token = $result->transaction->creditCardDetails->token;
						} else if (!empty($result->transaction->paypalDetails->token)) {
							$braintree_payment_method_token = $result->transaction->paypalDetails->token;
						} else {
							$braintree_payment_method_token = '';
						}

						// add order transaction to table, this is used to view transactions made to an order and ability to void/refund
						$this->load->model('checkout/order_transaction');

						$this->model_checkout_order_transaction->addOrderTransaction($this->session->data['order_id'], array(
							'transaction_id'     => $result->transaction->id,
							'authorization_code' => $result->transaction->processorAuthorizationCode,
							'payment_method'     => $order_info['payment_method'],
							'payment_code'       => $order_info['payment_code'],
							'amount'             => $order_info['total'],
							'currency_id'        => $order_info['currency_id'],
							'currency_code'      => $order_info['currency_code'],
							'currency_value'     => $order_info['currency_value'],
							'status'             => (($this->config->get('braintree_transaction_method') == 'capture') ? 1 : 0),
							'meta_data'          => array(
								'transaction_server' => $this->config->get('braintree_transaction_server'),
								'merchant_id'        => $this->config->get('braintree_merchant_id'),
								'public_key'         => $this->config->get('braintree_public_key'),
								'private_key'        => $this->config->get('braintree_private_key')
							)
						));
					} else {
						$message = $result->message;
					}
				}
			} else {
				// order total is $0.00, can't put through a transaction
				if ($braintree_customer_id) {
					// customer already exists, just need to get payment method token
					$result = $this->model_payment_braintree->createBraintreePaymentMethod($braintree_customer_id, $this->request->post['nonce']);
				} else {
					// no customer exists, create customer and payment method
					$result = $this->model_payment_braintree->createBraintreeCustomer(array(
						'firstname'            => $order_info['firstname'],
						'lastname'             => $order_info['lastname'],
						'telephone'            => $order_info['telephone'],
						'email'                => $order_info['email'],
						'payment_method_nonce' => $this->request->post['nonce']
					), $this->customer->getId());
				}

				if ($result['success']) {
					$success = true;

					$message = '';

					// get the braintree customer id
					if (!empty($result['customer_id'])) {
						$braintree_customer_id = $result['customer_id'];
					}

					// get the braintree payment method token
					if ($result['success'] && !empty($result['payment_method_token'])) {
						$braintree_payment_method_token = $result['payment_method_token'];
					} else {
						$braintree_payment_method_token = '';
					}
				} else {
					$message = $result['message'];
				}
			}

			if ($success) {
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('braintree_order_status_id'), $message, false);

				// add recurring orders for any recurring products
				if ($braintree_customer_id && $braintree_payment_method_token) {
					$this->load->model('checkout/recurring_order');

					$this->model_checkout_recurring_order->addRecurringOrders($order_info, array(
						'transaction_server'             => $this->config->get('braintree_transaction_server'),
						'merchant_id'                    => $this->config->get('braintree_merchant_id'),
						'public_key'                     => $this->config->get('braintree_public_key'),
						'private_key'                    => $this->config->get('braintree_private_key'),
						'braintree_customer_id'          => $braintree_customer_id,
						'braintree_payment_method_token' => $braintree_payment_method_token
					));
				}

				$json['success'] = true;
			} else {
				$json['error'] = $message;
			}
		} else {
			$json['error'] = implode(' ', $this->error);
		}

		// get updated list of payment methods
		if (!empty($braintree_customer_id) && !empty($braintree_payment_method_token)) {
			$json['payment_method_token']  = $braintree_payment_method_token;
			$json['payment_methods']       = $this->model_payment_braintree->getBraintreePaymentMethods($braintree_customer_id);
			$json['active_payment_method'] = array();

			foreach ($json['payment_methods'] as $payment_method) {
				if ($payment_method['token'] == $braintree_payment_method_token) {
					$json['active_payment_method'] = $payment_method;
					break;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateConfirm() {
		if (empty($this->request->post['nonce'])) {
			$this->error['nonce'] = $this->language->get('error_nonce');
		}

		return !$this->redirect && !$this->error;
	}

	public function update() {
		$this->language->load('payment/braintree');

		$json = array('success' => false);

		$this->load->vendor('braintree_php/lib/Braintree');

		// setup the braintree environment
		Braintree\Configuration::environment($this->config->get('braintree_transaction_server'));
		Braintree\Configuration::merchantId($this->config->get('braintree_merchant_id'));
		Braintree\Configuration::publicKey($this->config->get('braintree_public_key'));
		Braintree\Configuration::privateKey($this->config->get('braintree_private_key'));

		$this->load->model('payment/braintree');
		$braintree_customer_id = $this->model_payment_braintree->getBraintreeCustomerId($this->customer->getId());

		// get nonce from payment method token
		if (empty($this->request->post['token']) && !empty($this->request->post['nonce'])) {
			if ($braintree_customer_id) {
				// customer already exists, just need to get payment method token
				$result = $this->model_payment_braintree->createBraintreePaymentMethod($braintree_customer_id, $this->request->post['nonce']);
			} else {
				// no customer exists, create customer and payment method
				$result = $this->model_payment_braintree->createBraintreeCustomer(array(
					'firstname'            => $this->customer->getFirstName(),
					'lastname'             => $this->customer->getLastName(),
					'telephone'            => $this->customer->getTelephone(),
					'email'                => $this->customer->getEmail(),
					'payment_method_nonce' => $this->request->post['nonce']
				), $this->customer->getId());

				if ($result['success'] && !empty($result['customer_id'])) {
					$braintree_customer_id = $result['customer_id'];
				}
			}

			if ($result['success'] && !empty($result['payment_method_token'])) {
				$this->request->post['token'] = $result['payment_method_token'];
			} else {
				$this->error['merchant'] = $result['message'];
			}
		}

		if (!$this->error && $this->validateUpdate()) {
			$this->load->model('checkout/recurring_order');
			$this->model_checkout_recurring_order->updatePaymentMethodForAccount(
				$this->customer->getId(),
				$this->request->get['account_id'],
				'Braintree',
				'braintree',
				array(
					'transaction_server'             => $this->config->get('braintree_transaction_server'),
					'merchant_id'                    => $this->config->get('braintree_merchant_id'),
					'public_key'                     => $this->config->get('braintree_public_key'),
					'private_key'                    => $this->config->get('braintree_private_key'),
					'braintree_customer_id'          => $braintree_customer_id,
					'braintree_payment_method_token' => $this->request->post['token']
				)
			);

			$json['success'] = $this->language->get('success_update');

			// get updated list of payment methods
			$json['payment_method_token']  = $this->request->post['token'];
			$json['payment_methods']       = $this->model_payment_braintree->getBraintreePaymentMethods($braintree_customer_id);
			$json['active_payment_method'] = array();

			foreach ($json['payment_methods'] as $payment_method) {
				if ($payment_method['token'] == $this->request->post['token']) {
					$json['active_payment_method'] = $payment_method;
					break;
				}
			}
		} else {
			$json['error'] = implode(' ', $this->error);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateUpdate() {
		if (empty($this->request->get['account_id'])) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		if (empty($this->request->post['token'])) {
			$this->error['token'] = $this->language->get('error_token');
		}

		return !$this->redirect && !$this->error;
	}

	public function recurringCancel() {
		if (!empty($this->request->get['recurring_id'])) {
			$this->language->load('customer/recurring');

			$this->load->model('customer/recurring');

			$order_recurring_info = $this->model_customer_recurring->getProfile($this->request->get['recurring_id']);

			if ($order_recurring_info) {
				$this->model_customer_recurring->updateStatus($this->request->get['recurring_id'], 4);

				$this->session->data['success'] = $this->language->get('success_cancelled');
			} else {
				$this->log->write($this->language->get('error_not_found'));
			}
		}

		$this->redirect($this->url->link('customer/recurring/info', 'recurring_id=' . $this->request->get['recurring_id'], true));
	}
}