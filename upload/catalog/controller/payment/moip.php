<?php
class ControllerPaymentMoip extends Controller {
	private $error = array();

	public function index() {
		$data = $this->language->load('payment/moip');

		if (isset($this->request->post['account_id'])) {
			$account_id = $this->request->post['account_id'];
		} else if (isset($this->request->get['account_id'])) {
			$account_id = $this->request->get['account_id'];
		} else {
			$account_id = '';
		}

		if ($account_id) {
			$data['confirm'] = $this->url->link('payment/moip/confirm', 'account_id=' . $account_id, true);
			$data['update']  = $this->url->link('payment/moip/update', 'account_id=' . $account_id, true);

			// setup the moip environment
			$this->moip = new Moip($this->registry);

			$data['payment_methods']       = array();
			$data['active_payment_method'] = array();

			if ($this->customer->isLogged()) {
				$this->load->model('payment/moip');
				$moip_customer_id = $this->model_payment_moip->getMoipCustomerId($this->customer->getId());

				// create a customer if this customer doesn't have a moip customer
				if (!$moip_customer_id) {
					$moip_customer_id = $this->model_payment_moip->createMoipCustomer($this->customer->getId(), array(
						'firstname' => $this->customer->getFirstName(),
						'lastname'  => $this->customer->getLastName(),
						'email'     => $this->customer->getEmail(),
						'telephone' => $this->customer->getTelephone()
					));
				}

				if ($moip_customer_id) {
					// get active recurring order to get the moip_payment_method_id
					$payment_method_id = $this->model_payment_moip->getPaymentMethodIdForAccount($this->customer->getId(), $account_id);

					// get all payment methods associated with this customer
					$data['payment_methods'] = $this->model_payment_moip->getMoipPaymentMethods($moip_customer_id);

					// try and find the active payment method in the list of available payment methods
					if ($payment_method_id) {
						foreach ($data['payment_methods'] as $payment_method) {
							if ($payment_method['id'] == $payment_method_id) {
								$data['active_payment_method'] = $payment_method;
								break;
							}
						}
					}

					// if there is no active payment method, use the first payment method
					if ($data['payment_methods'] && !$data['active_payment_method']) {
						$data['active_payment_method'] = reset($data['payment_methods']);
					}
				}
			}

			$data['public_key'] = $this->config->get('moip_public_key');

			return $this->load->view('payment/moip', $data);
		}
	}

	public function confirm() {
		$this->language->load('payment/moip');

		$json = array('success' => false);

		// setup the moip environment
		$this->moip = new Moip($this->registry);

		$this->load->model('checkout/order');
		$this->load->model('payment/moip');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$moip_customer_id = $this->model_payment_moip->getMoipCustomerId($order_info['customer_id']);

		// create payment method id from hash
		if (empty($this->request->post['payment_method_id']) && !empty($this->request->post['cc'])) {
			$this->request->post['payment_method_id'] = $this->model_payment_moip->createMoipPaymentMethod($moip_customer_id, array(
				'method'     => 'CREDIT_CARD',
				'creditCard' => array(
					'number'          => $this->request->post['cc']['number'],
					'expirationMonth' => $this->request->post['cc']['exp_month'],
					'expirationYear'  => $this->request->post['cc']['exp_year'],
					'cvc'             => $this->request->post['cc']['cvv'],
					'holder'          => array(
						'fullname'  => $this->request->post['cardholder']['name'],
						'birthdate' => date('Y-m-d', strtotime($this->request->post['cardholder']['birthdate'])),
						'taxDocument' => array(
							'type'   => 'CPF',
							'number' => $this->request->post['cardholder']['cpf']
						),
						'billingAddress' => array(				// moip is dumb... forcing me to use a dummy address... wtf...
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
				)
			));

			// if a new payment method was added, retrieve a new listing of payment methods
			if ($this->request->post['payment_method_id']) {
				$json['payment_method_id']     = $this->request->post['payment_method_id'];
				$json['payment_methods']       = $this->model_payment_moip->getMoipPaymentMethods($moip_customer_id);
				$json['active_payment_method'] = array();

				foreach ($json['payment_methods'] as $payment_method) {
					if ($payment_method['id'] == $this->request->post['payment_method_id']) {
						$json['active_payment_method'] = $payment_method;
						break;
					}
				}
			}
		}

		if ($this->validateConfirm()) {
			if (!empty($this->request->post['cc']['cvv'])) {
				$moip_payment_method_cvc = $this->request->post['cc']['cvv'];
			} else {
				$moip_payment_method_cvc = $this->model_payment_moip->getMoipPaymentMethodCvc($this->request->post['payment_method_id']);
			}

			$success = false;
			$message = $this->language->get('error_unknown');

			if ($this->currency->format($order_info['total'], '', '', false) > 0) {
				// setup the $order_data variable to be passed to moip
				$order_data = array(
					'ownId'  => $this->session->data['order_id'],
					'amount' => array(
						'currency'  => $order_info['currency_code'],
						'subtotals' => array(
							'addition' => 0,
							'discount' => 0
						)
					),
					'items'    => array(),
					'customer' => array(
						'id' => $moip_customer_id,
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

				$phone = $this->customer->getTelephone();

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

				// go through totals and add any additions/discounts
				$order_totals = $this->model_checkout_order->getOrderTotals($this->session->data['order_id']);

				foreach ($order_totals as $order_total) {
					switch ($order_total['code'])
					{
					case 'total':
						// nothing after total is worth looking at
						break 2;
					case 'sub_total':
						// do nothing
						break;
					default:
						if ($order_total['value'] > 0) {
							$order_data['amount']['subtotals']['addition'] += str_replace('.', '', $this->currency->format($order_total['value'], '', '', false));
						} else if ($order_total['value'] < 0) {
							$order_data['amount']['subtotals']['discount'] += abs(str_replace('.', '', $this->currency->format($order_total['value'], '', '', false)));
						}
					}
				}

				// add products as items
				$order_products = $this->model_checkout_order->getOrderProducts($this->session->data['order_id']);

				foreach ($order_products as $order_product) {
					$order_data['items'][] = array(
						'product'  => $order_product['name'],
						'quantity' => $order_product['quantity'],
						'price'    => str_replace('.', '', $this->currency->format($order_product['price'], '', '', false))
					);
				}

				// attempt the transaction
				$moip_order_id = $this->moip->orders->create($order_data);

				if ($moip_order_id) {
					$result = $this->moip->orders->payment($moip_order_id, array(
						'delayCapture'      => ($this->config->get('moip_transaction_method') == 'capture' ? false : true),
						'fundingInstrument' => array(
							'method' => 'CREDIT_CARD',
							'creditCard' => array(
								'id'  => $this->request->post['payment_method_id'],
								'cvc' => $moip_payment_method_cvc
							)
						)
					));
				} else {
					$result = false;
				}

				if (!empty($result)) {
					if (!empty($result->id) && ($result->status == 'IN_ANALYSIS' || $result->status == 'AUTHORIZED' || $result->status == 'PRE_AUTHORIZED')) {
						$success = true;
						$message = '';

						// add order transaction to table, this is used to view transactions made to an order and ability to void/refund
						$this->load->model('checkout/order_transaction');

						$this->model_checkout_order_transaction->addOrderTransaction($this->session->data['order_id'], array(
							'transaction_id'     => $result->id,
							'authorization_code' => '',
							'payment_method'     => $order_info['payment_method'],
							'payment_code'       => $order_info['payment_code'],
							'amount'             => $order_info['total'],
							'currency_id'        => $order_info['currency_id'],
							'currency_code'      => $order_info['currency_code'],
							'currency_value'     => $order_info['currency_value'],
							'status'             => (($this->config->get('moip_transaction_method') == 'capture') ? 1 : 0),
							'meta_data'          => array(
								'environment' => $this->config->get('moip_environment'),
								'api_token'   => $this->config->get('moip_api_token'),
								'api_key'     => $this->config->get('moip_api_key'),
								'public_key'  => $this->config->get('moip_public_key')
							)
						));
					} else {
						if (isset($result->cancellationDetails)) {
							$message = $result->cancellationDetails->cancelledBy . ': ' . $result->cancellationDetails->description;
						} else if (isset($result->errors)) {
							foreach ($result->errors as $error) {
								$message = $error->code . ': ' . $error->description;
							}
						} else {
							$message = $this->language->get('error_unknown');
						}
					}
				}
			} else {
				// order total is $0.00, can't put through a transaction
				$success = true;
				$message = '';
			}

			if ($success) {
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('moip_order_status_id'), $message, false);

				// add recurring orders for any recurring products
				$this->load->model('checkout/recurring_order');
				$this->model_checkout_recurring_order->addRecurringOrders($order_info, array(
					'environment'            => $this->config->get('moip_environment'),
					'api_token'              => $this->config->get('moip_api_token'),
					'api_key'                => $this->config->get('moip_api_key'),
					'public_key'             => $this->config->get('moip_public_key'),
					'moip_customer_id'       => $moip_customer_id,
					'moip_payment_method_id' => $this->request->post['payment_method_id']
				));

				$json['success'] = true;
			} else {
				$json['error'] = $message;
			}
		} else {
			$json['error'] = implode(' ', $this->error);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateConfirm() {
		if (empty($this->request->post['payment_method_id'])) {
			$this->error['payment_method_id'] = $this->language->get('error_payment_method_id');
		}

		return !$this->redirect && !$this->error;
	}

	public function update() {
		$this->language->load('payment/moip');

		$json = array('success' => false);

		// setup the moip environment
		$this->moip = new Moip($this->registry);

		$this->load->model('payment/moip');
		$moip_customer_id = $this->model_payment_moip->getMoipCustomerId($this->customer->getId());

		// create payment method id from hash
		if (empty($this->request->post['payment_method_id']) && !empty($this->request->post['cc'])) {
			$this->request->post['payment_method_id'] = $this->model_payment_moip->createMoipPaymentMethod($moip_customer_id, array(
				'method'     => 'CREDIT_CARD',
				'creditCard' => array(
					'number'          => $this->request->post['cc']['number'],
					'expirationMonth' => $this->request->post['cc']['exp_month'],
					'expirationYear'  => $this->request->post['cc']['exp_year'],
					'cvc'             => $this->request->post['cc']['cvv'],
					'holder'          => array(
						'fullname'  => $this->request->post['cardholder']['name'],
						'birthdate' => date('Y-m-d', strtotime($this->request->post['cardholder']['birthdate'])),
						'taxDocument' => array(
							'type'   => 'CPF',
							'number' => $this->request->post['cardholder']['cpf']
						)
					)
				)
			));

			// if a new payment method was added, retrieve a new listing of payment methods
			if ($this->request->post['payment_method_id']) {
				$json['payment_method_id']     = $this->request->post['payment_method_id'];
				$json['payment_methods']       = $this->model_payment_moip->getMoipPaymentMethods($moip_customer_id);
				$json['active_payment_method'] = array();

				foreach ($json['payment_methods'] as $payment_method) {
					if ($payment_method['id'] == $this->request->post['payment_method_id']) {
						$json['active_payment_method'] = $payment_method;
						break;
					}
				}
			}
		}

		if ($this->validateUpdate()) {
			$this->load->model('checkout/recurring_order');
			$this->model_checkout_recurring_order->updatePaymentMethodForAccount(
				$this->customer->getId(),
				$this->request->get['account_id'],
				'Moip',
				'moip',
				array(
					'environment'            => $this->config->get('moip_environment'),
					'api_token'              => $this->config->get('moip_api_token'),
					'api_key'                => $this->config->get('moip_api_key'),
					'public_key'             => $this->config->get('moip_public_key'),
					'moip_customer_id'       => $moip_customer_id,
					'moip_payment_method_id' => $this->request->post['payment_method_id']
				)
			);

			$json['success'] = $this->language->get('success_update');
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

		if (empty($this->request->post['payment_method_id'])) {
			$this->error['payment_method_id'] = $this->language->get('error_payment_method_id');
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