<?php
class ControllerApiOrder extends Controller {
	public function add() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Customer
			if (!isset($this->session->data['customer'])) {
				$json['error'] = $this->language->get('error_customer');
			}

			// Payment Method
			if (!$json && !empty($this->request->post['payment_method'])) {
				if (empty($this->session->data['payment_methods'])) {
					$json['error'] = $this->language->get('error_no_payment');
				} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
					$json['error'] = $this->language->get('error_payment_method');
				}

				if (!$json) {
					$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
				}
			}

			if (!isset($this->session->data['payment_method'])) {
				$json['error'] = $this->language->get('error_payment_method');
			}

			// Cart
			if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
				$json['error'] = $this->language->get('error_product');
			}

			// Validate minimum quantity requirements.
			$products = $this->cart->getProducts();

			foreach ($products as $product) {
				$product_total = 0;

				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}

				if ($product['minimum'] > $product_total) {
					$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

					break;
				}
			}

			if (!$json) {
				$json['success'] = $this->language->get('text_success');
				
				$order_data = array();

				// Store Details
				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');
				$order_data['store_url'] = $this->config->get('config_url');

				// Customer Details
				$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
				$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['customer']['firstname'];
				$order_data['lastname'] = $this->session->data['customer']['lastname'];
				$order_data['email'] = $this->session->data['customer']['email'];
				$order_data['telephone'] = $this->session->data['customer']['telephone'];
				$order_data['custom_field'] = $this->session->data['customer']['custom_field'];

				if (isset($this->session->data['payment_method']['title'])) {
					$order_data['payment_method'] = $this->session->data['payment_method']['title'];
				} else {
					$order_data['payment_method'] = '';
				}

				if (isset($this->session->data['payment_method']['code'])) {
					$order_data['payment_code'] = $this->session->data['payment_method']['code'];
				} else {
					$order_data['payment_code'] = '';
				}

				// Products
				$order_data['products'] = array();

				foreach ($this->cart->getProducts() as $product) {
					$order_data['products'][] = array(
						'product_id'               => $product['product_id'],
						'name'                     => $product['name'],
						'model'                    => $product['model'],
						'account_id'               => $product['account_id'],
						'account_type'             => $product['account_type'],
						'account_username'         => $product['account_username'],
						'time_extension'           => $product['time_extension'],
						'time_extension_frequency' => $product['time_extension_frequency'],
						'quantity'                 => $product['quantity'],
						'price'                    => $product['price'],
						'total'                    => $product['total'],
						'discount'                 => $product['discount'],
						'coupon_id'                => $product['coupon_id'],
						'tax'                      => $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'                   => $product['reward'],
						'recurring'                => $product['recurring']
					);
				}

				// Gift Voucher
				$order_data['vouchers'] = array();

				if (!empty($this->session->data['vouchers'])) {
					foreach ($this->session->data['vouchers'] as $voucher) {
						$order_data['vouchers'][] = array(
							'description'      => $voucher['description'],
							'code'             => token(10),
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'],
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']
						);
					}
				}

				// Order Totals
				$this->load->model('extension/extension');

				$order_data['totals'] = array();
				$total = 0;
				$taxes = $this->cart->getTaxes();

				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
					}
				}

				$sort_order = array();

				foreach ($order_data['totals'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $order_data['totals']);

				if (isset($this->request->post['comment'])) {
					$order_data['comment'] = $this->request->post['comment'];
				} else {
					$order_data['comment'] = '';
				}

				$order_data['total'] = $total;

				if (isset($this->request->post['affiliate_id'])) {
					$subtotal = $this->cart->getSubTotal();

					// Affiliate
					$this->load->model('affiliate/affiliate');

					$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->post['affiliate_id']);

					if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					// Marketing
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}

				$order_data['language_id'] = $this->config->get('config_language_id');
				$order_data['currency_id'] = $this->currency->getId();
				$order_data['currency_code'] = $this->currency->getCode();
				$order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
				$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$order_data['accept_language'] = '';
				}

				$this->load->model('checkout/order');

				$json['order_id'] = $this->model_checkout_order->addOrder($order_data);

				// Set the order history
				if (isset($this->request->post['order_status_id'])) {
					$order_status_id = $this->request->post['order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}

				$this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);
			}
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				// Customer
				if (!isset($this->session->data['customer'])) {
					$json['error'] = $this->language->get('error_customer');
				}

				// Payment Method
				if (!$json && !empty($this->request->post['payment_method'])) {
					if (empty($this->session->data['payment_methods'])) {
						$json['error'] = $this->language->get('error_no_payment');
					} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
						$json['error'] = $this->language->get('error_payment_method');
					}

					if (!$json) {
						$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
					}
				}

				if (!isset($this->session->data['payment_method'])) {
					$json['error'] = $this->language->get('error_payment_method');
				}

				// Cart
				if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
					$json['error'] = $this->language->get('error_product');
				}

				// Validate minimum quantity requirements.
				$products = $this->cart->getProducts();

				foreach ($products as $product) {
					$product_total = 0;

					foreach ($products as $product_2) {
						if ($product_2['product_id'] == $product['product_id']) {
							$product_total += $product_2['quantity'];
						}
					}

					if ($product['minimum'] > $product_total) {
						$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

						break;
					}
				}

				if (!$json) {
					$json['success'] = $this->language->get('text_success');
					
					$order_data = array();

					// Store Details
					$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
					$order_data['store_id'] = $this->config->get('config_store_id');
					$order_data['store_name'] = $this->config->get('config_name');
					$order_data['store_url'] = $this->config->get('config_url');

					// Customer Details
					$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
					$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
					$order_data['firstname'] = $this->session->data['customer']['firstname'];
					$order_data['lastname'] = $this->session->data['customer']['lastname'];
					$order_data['email'] = $this->session->data['customer']['email'];
					$order_data['telephone'] = $this->session->data['customer']['telephone'];
					$order_data['custom_field'] = $this->session->data['customer']['custom_field'];

					if (isset($this->session->data['payment_method']['title'])) {
						$order_data['payment_method'] = $this->session->data['payment_method']['title'];
					} else {
						$order_data['payment_method'] = '';
					}

					if (isset($this->session->data['payment_method']['code'])) {
						$order_data['payment_code'] = $this->session->data['payment_method']['code'];
					} else {
						$order_data['payment_code'] = '';
					}

					// Products
					$order_data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
						$order_data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'download'   => $product['download'],
							'quantity'   => $product['quantity'],
							'price'      => $product['price'],
							'total'      => $product['total'],
							'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
							'reward'     => $product['reward']
						);
					}

					// Gift Voucher
					$order_data['vouchers'] = array();

					if (!empty($this->session->data['vouchers'])) {
						foreach ($this->session->data['vouchers'] as $voucher) {
							$order_data['vouchers'][] = array(
								'description'      => $voucher['description'],
								'code'             => token(10),
								'to_name'          => $voucher['to_name'],
								'to_email'         => $voucher['to_email'],
								'from_name'        => $voucher['from_name'],
								'from_email'       => $voucher['from_email'],
								'voucher_theme_id' => $voucher['voucher_theme_id'],
								'message'          => $voucher['message'],
								'amount'           => $voucher['amount']
							);
						}
					}

					// Order Totals
					$this->load->model('extension/extension');

					$order_data['totals'] = array();
					$total = 0;
					$taxes = $this->cart->getTaxes();

					$sort_order = array();

					$results = $this->model_extension_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get($result['code'] . '_status')) {
							$this->load->model('total/' . $result['code']);

							$this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
						}
					}

					$sort_order = array();

					foreach ($order_data['totals'] as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $order_data['totals']);

					if (isset($this->request->post['comment'])) {
						$order_data['comment'] = $this->request->post['comment'];
					} else {
						$order_data['comment'] = '';
					}

					$order_data['total'] = $total;

					if (isset($this->request->post['affiliate_id'])) {
						$subtotal = $this->cart->getSubTotal();

						// Affiliate
						$this->load->model('affiliate/affiliate');

						$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->post['affiliate_id']);

						if ($affiliate_info) {
							$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
							$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
						} else {
							$order_data['affiliate_id'] = 0;
							$order_data['commission'] = 0;
						}
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					$this->model_checkout_order->editOrder($order_id, $order_data);

					// Set the order history
					if (isset($this->request->post['order_status_id'])) {
						$order_status_id = $this->request->post['order_status_id'];
					} else {
						$order_status_id = $this->config->get('config_order_status_id');
					}

					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
				}
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->deleteOrder($order_id);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	//ORDER RESEND
	public function send_receipt(){
		$this->load->model('checkout/order');
		$this->load->language('api/order');
		$json = array();
		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {		
			if (isset($this->request->post['selected'])) {
				$orders = $this->request->post['selected'];
			} elseif (isset($this->request->get['order_id'])) {
				$orders[] = $this->request->get['order_id'];
			}
			foreach ($orders as $order_id) {
				$this->model_checkout_order->resendOrderEmail($order_id);
				$json['order_id'] = $order_id;
			}
			$json['error'] = 0;
			$json['success'] = $this->language->get('text_success');
		}
		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	//ORDER RESEND
	public function history() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'order_status_id',
				'notify',
				'override',
				'comment'
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->addOrderHistory($order_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify'], $this->request->post['override']);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
			$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			$this->response->addHeader('Access-Control-Max-Age: 1000');
			$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
