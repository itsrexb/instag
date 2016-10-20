<?php
class ControllerAccountBilling extends Controller {
	private $error    = array();
	private $redirect = false;

	public function __construct($registry) {
		parent::__construct($registry);

		if (isset($this->request->post['account_id'])) {
			$this->account_id = $this->request->post['account_id'];
		} else if (isset($this->request->get['account_id'])) {
			$this->account_id = $this->request->get['account_id'];
		} else {
			$this->account_id = '';
		}
	}

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('customer/login'));
		}

		$this->cart->clear();

		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['comment']);
		unset($this->session->data['order_id']);
		unset($this->session->data['coupon']);
		unset($this->session->data['reward']);
		unset($this->session->data['voucher']);
		unset($this->session->data['vouchers']);
		unset($this->session->data['totals']);

		$data = $this->load->language('account/billing');

		$data['href_customer_order'] = $this->url->link('customer/order', '', true);

		$data['account_id'] = $this->account_id;

		$this->load->model('account/recurring_order');
		$this->load->model('catalog/product');

		$recurring_order = $this->model_account_recurring_order->getLastRecurringOrder($this->account_id);

		if ($recurring_order && $recurring_order['active']) {
			$this->load->model('account/account');
			$account_info = $this->model_account_account->getAccountFromCache($this->account_id);

			// if account is expired, do not consider them on a plan
			if (strtotime($account_info['date_expires']) < time()) {
				$data['recurring_product_id'] = 0;
			} else {
				$data['recurring_product_id'] = $recurring_order['product_id'];
			}

			$product = $this->model_catalog_product->getProduct($data['recurring_product_id']);

			if ($product) {
				$product_name_arr = explode('-', $product['name']);

				if (count($product_name_arr) > 1) {
					$data['current_billing'] = trim(array_pop($product_name_arr));
					$data['current_speed']   = trim(implode('-', $product_name_arr));
				} else {
					$data['current_billing'] = $product['name'];
					$data['current_speed']   = $product['name'];
				}
			} else {
				$data['current_billing'] = $this->language->get('text_no_plan');
				$data['current_speed']   = $this->language->get('text_no_plan');
			}
		} else {
			$data['recurring_product_id'] = 0;
			$data['current_billing']      = $this->language->get('text_no_plan');
			$data['current_speed']        = $this->language->get('text_no_plan');
		}

		$this->load->model('catalog/category');

		$data['categories'] = array();

		$category_data = $this->model_catalog_category->getCategories();

		foreach ($category_data as $category) {
			$products = array();

			$product_data = $this->model_catalog_product->getProducts(array('filter_category_id' => $category['category_id']));

			foreach ($product_data as $product) {
				$products[] = array(
					'product_id'  => $product['product_id'],
					'label'       => ltrim(str_replace($category['name'], '', $product['name']), '- '),
					'name'        => str_replace($category['name'] . ' - ', '', $product['name']),
					'price'       => $this->currency->format($product['price']),
					'description' => html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')
				);
			}

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'],
				'description' => html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8'),
				'products'    => $products
			);
		}

		$customer_discount = $this->customer->getDiscount();

		if ($customer_discount > 0) {
			$data['text_customer_discount'] = sprintf($this->language->get('text_customer_discount'), str_replace('.00', '', number_format($customer_discount, 2)));
		} else {
			$data['text_customer_discount'] = '';
		}

		$this->session->data['payment_method'] = $this->getPaymentMethod();

		if ($this->session->data['payment_method']) {
			$data['payment_method'] = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);
		} else {
			$data['payment_method'] = '';
		}

		$data['modules'] = array();

		$files = glob(DIR_APPLICATION . '/controller/total/*.php');

		if ($files) {
			foreach ($files as $file) {
				$result = $this->load->controller('total/' . basename($file, '.php'));

				if ($result) {
					$data['modules'][] = $result;
				}
			}
		}

		$data['upcoming_orders'] = array();

		$recurring_orders = $this->model_account_recurring_order->getActiveRecurringOrders($this->account_id);

		// get recurring orders, group them by next recurring date
		foreach ($recurring_orders as $recurring_order) {
			$key = $recurring_order['store_id'] . '.' . $recurring_order['date_next_recurring'] . '.' . $recurring_order['metadata'];

			if (isset($data['upcoming_orders'][$key])) {
				$product = array(
					'product_id' => $recurring_order['product_id'],
					'name'       => $recurring_order['product_name'],
					'quantity'   => $recurring_order['product_quantity'],
					'price'      => ($recurring_order['trial_duration_remaining'] ? $recurring_order['trial_price'] : $recurring_order['recurring_price']),
					'coupon_id'  => $recurring_order['coupon_id']
				);

				$data['upcoming_orders'][$key]['products'][] = $product;
			} else {
				$product = array(
					'product_id' => $recurring_order['product_id'],
					'account_id' => $recurring_order['account_id'],
					'name'       => $recurring_order['product_name'],
					'quantity'   => $recurring_order['product_quantity'],
					'price'      => ($recurring_order['trial_duration'] ? $recurring_order['trial_price'] : $recurring_order['recurring_price']),
					'coupon_id'  => $recurring_order['coupon_id']
				);

				$date_next_recurring = '';

				for ($i = (int)$this->config->get('config_recurring_order_days_before'); $i >= 0; $i--) {
					if (strtotime('-' . $i . ' days', strtotime($recurring_order['date_next_recurring'])) >= time()) {
						$date_next_recurring = date($this->language->get('date_format_short'), strtotime('-' . $i . ' days', strtotime($recurring_order['date_next_recurring'])));
						break;
					}
				}

				if (!$date_next_recurring) {
					$date_next_recurring = date($this->language->get('date_format_short'), strtotime('tomorrow'));
				}

				$data['upcoming_orders'][$key] = array(
					'total'         => 0,
					'products'      => array($product),
					'date'          => $date_next_recurring,
					'currency_code' => $recurring_order['currency_code']
				);
			}
		}

		// calculate the total for each upcoming order
		foreach ($data['upcoming_orders'] as $key => $upcoming_order) {
			$this->session->data['recurring_coupons'] = array();

			// set currency according to upcoming order
			$this->currency->set($upcoming_order['currency_code']);

			foreach ($upcoming_order['products'] as $product) {
				$cart_id = $this->cart->add($product['product_id'], $product['account_id'], $product['quantity'], 0, $product['price']);

				if ($product['coupon_id']) {
					$this->session->data['recurring_coupons'][] = array(
						'coupon_id' => $product['coupon_id'],
						'cart_id'   => $cart_id
					);
				}
			}

			$order_total_data = $this->getTotals();

			$data['upcoming_orders'][$key]['total'] = $this->currency->format($order_total_data['total']);
		}	

		$this->cart->clear();

		if (isset($this->session->data['recurring_coupons'])) {
			unset($this->session->data['recurring_coupons']);
		}

		// set currency back to the customer currency
		$this->currency->set($this->customer->getCurrencyCode());

		// add profile link
		$data['link_profile'] = $this->url->link('customer/profile', '', true);

		$this->response->setOutput($this->load->view('account/billing', $data));
	}

	public function cart() {
		$json = array();

		if ($this->validateAccount()) {
			$this->load->language('account/billing');

			$json = array(
				'account_totals' => array(),
				'totals'         => array(),
				'total'          => 0
			);

			$this->addCoupon();
			$this->addProductToCart();

			$json['order_totals'] = array();

			if ($this->cart->hasProducts()) {
				$order_total_data = $this->getTotals();

				foreach ($order_total_data['totals'] as $total) {
					$json['order_totals'][] = array(
						'code'  => $total['code'],
						'title' => $total['title'],
						'help'  => isset($total['help']) ? $total['help'] : '',
						'text'  => $this->currency->format($total['value'])
					);
				}
			}

			$payment_method = $this->getPaymentMethod();

			if ($payment_method) {
				if (!isset($this->session->data['payment_method']) || $payment_method['code'] != $this->session->data['payment_method']['code']) {
					$this->session->data['payment_method'] = $payment_method;

					$json['payment_method'] = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);
				}
			} else {
				$json['payment_method'] = '';
			}
		}

		if ($this->error) {
			$json['errors'] = $this->error;
		}

		if ($this->redirect) {
			$json['redirect'] = $this->redirect;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validate() {
		$json = array('success' => false);

		if ($this->validateAccount()) {
			$this->language->load('account/billing');

			$this->addCoupon();
			$this->addProductToCart();

			if ($this->cart->hasProducts()) {
				// customer is trying to purchase a new plan
				$this->session->data['payment_method'] = $this->getPaymentMethod();

				$order_data = array();

				$order_data['totals'] = array();
				$total                = 0;
				$taxes                = $this->cart->getTaxes();

				$this->load->model('extension/extension');

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

				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id']       = $this->config->get('config_store_id');
				$order_data['store_name']     = $this->config->get('config_name');
				$order_data['store_url']      = $this->config->get('config_url');

				$this->load->model('customer/customer');
				$customer_info = $this->model_customer_customer->getCustomer($this->customer->getId());

				$order_data['customer_id']        = $this->customer->getId();
				$order_data['customer_group_id']  = $this->customer->getGroupId();
				$order_data['firstname']          = $this->customer->getFirstName();
				$order_data['lastname']           = $this->customer->getLastName();
				$order_data['email']              = $this->customer->getEmail();
				$order_data['telephone']          = $this->customer->getTelephone();
				$order_data['custom_field']       = json_decode($customer_info['custom_field'], true);

				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($this->customer->getCountryId());

				$order_data['payment_country']    = ($country_info ? $country_info['name'] : '');
				$order_data['payment_country_id'] = $this->customer->getCountryId();

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

				$order_data['comment']        = '';
				$order_data['total']          = $total;
				$order_data['language_id']    = $this->config->get('config_language_id');
				$order_data['currency_id']    = $this->currency->getId();
				$order_data['currency_code']  = $this->currency->getCode();
				$order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
				$order_data['ip']             = $this->request->server['REMOTE_ADDR'];

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
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

				$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

				$json['success'] = true;
				$json['action']  = 'confirm';
			} else {
				// customer is updating the payment method associated with their account
				$json['success'] = true;
				$json['action']  = 'update';
			}
		}

		if ($this->error) {
			$json['errors'] = $this->error;
		}

		if ($this->redirect) {
			$json['redirect'] = $this->redirect;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function addCoupon() {
		if (empty($this->request->post['coupon'])) {
			unset($this->session->data['coupon']);
		} else {
			$this->load->model('total/coupon');

			$coupon_info = $this->model_total_coupon->getCoupon($this->request->post['coupon']);

			if ($coupon_info) {
				$this->session->data['coupon'] = $this->request->post['coupon'];
			} else if (isset($this->session->data['coupon'])) {
				unset($this->session->data['coupon']);
			}
		}
	}

	protected function addProductToCart() {
		$this->load->model('account/recurring_order');
		$this->load->model('catalog/product');

		$this->cart->clear();

		if (isset($this->request->post['product_id'])) {
			$recurring_order = $this->model_account_recurring_order->getActiveRecurringOrder($this->account_id);

			if ($recurring_order && $recurring_order['product_id'] == $this->request->post['product_id']) {
				$this->load->model('account/account');
				$account_info = $this->model_account_account->getAccountFromCache($this->account_id);

				// if account is expired, add the product to the cart still
				if (strtotime($account_info['date_expires']) > time()) {
					return;
				}
			}

			$recurring_profiles = $this->model_catalog_product->getProfiles($this->request->post['product_id']);

			if ($recurring_profiles) {
				$recurring_id = $recurring_profiles[0]['recurring_id'];
			} else {
				$recurring_id = 0;
			}

			$this->cart->add($this->request->post['product_id'], $this->account_id, 1, $recurring_id);
		}
	}

	protected function getTotals() {
		$total_data = array(
			'totals' => array(),
			'total'  => 0,
			'taxes'  => $this->cart->getTaxes()
		);

		$sort_order = array();

		$this->load->model('extension/extension');

		$results = $this->model_extension_extension->getExtensions('total');

		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);

				$this->{'model_total_' . $result['code']}->getTotal($total_data['totals'], $total_data['total'], $total_data['taxes']);
			}
		}

		return $total_data;
	}

	protected function getPaymentMethod() {
		$method_data = array();

		$this->load->model('extension/extension');
		$results = $this->model_extension_extension->getExtensions('payment');

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('payment/' . $result['code']);

				$method = $this->{'model_payment_' . $result['code']}->getMethod();

				if ($method) {
					$method_data[$result['code']] = $method;
				}
			}
		}

		$sort_order = array();

		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $method_data);

		$this->session->data['payment_methods'] = $method_data;

		return ($method_data ? reset($method_data) : array());
	}

	protected function validateAccount() {
		if (!$this->customer->isLogged()) {
			$this->redirect = $this->url->link('customer/login');
		}

		if (!$this->account_id) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		return !$this->redirect && !$this->error;
	}

	public function success() {
		$json = array('success' => false);

		if ($this->validateSuccess()) {
			$this->load->language('account/billing');

			$this->load->model('checkout/order');

			$order_info     = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			$order_products = $this->model_checkout_order->getOrderProducts($this->session->data['order_id']);

			$this->cart->clear();

			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);

			// get new upcoming orders
			$json['upcoming_orders'] = array();

			$this->load->model('account/recurring_order');

			$recurring_orders = $this->model_account_recurring_order->getActiveRecurringOrders($this->account_id);

			// get recurring orders, group them by next recurring date
			foreach ($recurring_orders as $recurring_order) {
				if (isset($json['upcoming_orders'][$recurring_order['date_next_recurring']])) {
					$product = array(
						'product_id' => $recurring_order['product_id'],
						'name'       => $recurring_order['product_name'],
						'quantity'   => $recurring_order['product_quantity'],
						'price'      => ($recurring_order['trial_duration_remaining'] ? $recurring_order['trial_price'] : $recurring_order['recurring_price']),
						'coupon_id'  => $recurring_order['coupon_id']
					);

					$json['upcoming_orders'][$recurring_order['date_next_recurring']]['products'][] = $product;
				} else {
					$product = array(
						'product_id' => $recurring_order['product_id'],
						'account_id' => $recurring_order['account_id'],
						'name'       => $recurring_order['product_name'],
						'quantity'   => $recurring_order['product_quantity'],
						'price'      => ($recurring_order['trial_duration'] ? $recurring_order['trial_price'] : $recurring_order['recurring_price']),
						'coupon_id'  => $recurring_order['coupon_id']
					);

					$date_next_recurring = '';

					for ($i = (int)$this->config->get('config_recurring_order_days_before'); $i >= 0; $i--) {
						if (strtotime('-' . $i . ' days', strtotime($recurring_order['date_next_recurring'])) >= time()) {
							$date_next_recurring = date($this->language->get('date_format_short'), strtotime('-' . $i . ' days', strtotime($recurring_order['date_next_recurring'])));
							break;
						}
					}

					if (!$date_next_recurring) {
						$date_next_recurring = date($this->language->get('date_format_short'), strtotime('tomorrow'));
					}

					$json['upcoming_orders'][$recurring_order['date_next_recurring']] = array(
						'total'    => 0,
						'products' => array($product),
						'date'     => $date_next_recurring
					);
				}
			}

			// calculate the total for each upcoming order
			foreach ($json['upcoming_orders'] as $key => $upcoming_order) {
				foreach ($upcoming_order['products'] as $product) {
					$cart_id = $this->cart->add($product['product_id'], $product['account_id'], $product['quantity'], 0, $product['price']);

					if ($product['coupon_id']) {
						$this->session->data['recurring_coupons'][] = array(
							'coupon_id' => $product['coupon_id'],
							'cart_id'   => $cart_id
						);
					}
				}

				$order_total_data = $this->getTotals();

				$json['upcoming_orders'][$key]['total'] = $this->currency->format($order_total_data['total']);
			}

			// convert upcoming orders into an indexed array
			$json['upcoming_orders'] = array_values($json['upcoming_orders']);

			// need to do this again because i added products back into the cart to calculate upcoming orders
			$this->cart->clear();

			// get capabilities
			$this->load->model('account/capability');

			$json['capabilities'] = $this->model_account_capability->getAccountCapabilities($this->request->get['account_id']);

			// get conversion tracking pixels
			$json['conversions'] = array();

			if ($this->config->get('conversion_checkout_status')) {
				$json['conversions'][] = $this->load->controller('conversion/checkout', array($order_info, $order_products));
			}

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing['code'] . '_status')) {
					$this->{$marketing['code']} = new $marketing['code']($this->registry);

					$this->load->model('extension/marketing/' . $marketing['code']);
					$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($this->customer->getId());
					$this->{'model_extension_marketing_' . $marketing['code']}->addOrder($order_info['order_id'], $order_info, $order_products);
				}
			}

			// Language for right sidebar account-status and button
			$json['button_start']   = $this->language->get('button_start');
			$json['account_status'] = $this->language->get('status_stopped');

			$this->load->language('account/tooltip');
			$json['tooltip_stopped'] = $this->language->get('instagram_tooltip_stopped');

			$json['success'] = $this->language->get('success_order');
		}

		if ($this->error) {
			$json['errors'] = $this->error;
		}

		if ($this->redirect) {
			$json['redirect'] = $this->redirect;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateSuccess() {
		if (!$this->customer->isLogged()) {
			$this->redirect = $this->url->link('customer/login');
		}

		if (!$this->account_id) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		return !$this->redirect && !$this->error;
	}

	public function invoice() {
		$json = array();

		if ($this->validateInvoice()) {
			$this->load->model('checkout/order');

			$data = $this->model_checkout_order->getInvoiceData($this->request->get['order_id']);

			$json['html'] = $this->load->view('account/billing_invoice', $data);
		} else {
			if ($this->error) {
				$json['errors'] = $this->error;
			}

			if ($this->redirect) {
				$json['redirect'] = $this->redirect;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateInvoice() {
		if (!$this->customer->isLogged()) {
			$this->redirect = $this->url->link('customer/login');
		}

		if (!isset($this->request->get['order_id'])) {
			$this->error['order_id'] = $this->language->get('error_order_id');
		}

		return !$this->redirect && !$this->error;
	}
}