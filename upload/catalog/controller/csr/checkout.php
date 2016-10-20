<?php
class ControllerCsrCheckout extends Controller {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->instaghive->logout();

		$this->user = new Cart\User($registry);

		if ($this->user->isLogged() && (!empty($this->request->post['customer_id']) || !empty($this->request->get['customer_id']))) {
			$customer_id = (isset($this->request->post['customer_id']) ? $this->request->post['customer_id'] : $this->request->get['customer_id']);

			$this->load->model('customer/customer');

			$customer_info = $this->model_customer_customer->getCustomer($customer_id);

			if ($customer_info) {
				$this->customer->login($customer_info['email'], '', true);
				$this->instaghive->login();

				$this->currency->set($this->customer->getCurrencyCode());
			}
		}
	}

	public function success() {
		if (isset($this->session->data['order_id'])) {
			$this->language->load('csr/checkout');

			$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->session->data['order_id']);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getExtensions('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing['code'] . '_status')) {
					$this->{$marketing['code']} = new $marketing['code']($this->registry);

					$this->load->model('extension/marketing/' . $marketing['code']);
					$this->{'model_extension_marketing_' . $marketing['code']}->updateCustomer($this->customer->getId());
					$this->{'model_extension_marketing_' . $marketing['code']}->addOrder($this->session->data['order_id']);
				}
			}
		}

		$this->response->redirect($this->url->link('csr/checkout', '', true));
	}

	public function index() {
		if (!$this->user->isLogged()) {
			$this->response->redirect($this->url->link('csr/login', '', true));
		}

		if ($this->config->get('config_url') != $this->config->get('config_ssl') && !$this->request->server['HTTPS']) {
			$this->response->redirect($this->url->link('csr/checkout', '', true));
		}

		// Reset everything
		$this->cart->clear();
		$this->customer->logout();

		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['comment']);
		unset($this->session->data['order_id']);
		unset($this->session->data['coupon']);
		unset($this->session->data['reward']);
		unset($this->session->data['voucher']);
		unset($this->session->data['vouchers']);
		unset($this->session->data['totals']);

		$data = $this->load->language('csr/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['action_new_customer']  = $this->url->link('csr/checkout/customer_insert', '', true);
		$data['action_new_instagram'] = $this->url->link('csr/checkout/instagram_insert', '', true);
		$data['action_reset']         = $this->url->link('csr/checkout', '', true);
		$data['action_success']       = $this->url->link('csr/checkout/success', '', true);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$data['categories'] = array();

		$category_data = $this->model_catalog_category->getCategories();

		foreach ($category_data as $category) {
			$products = array();

			$product_data = $this->model_catalog_product->getProducts(array('filter_category_id' => $category['category_id']));

			foreach ($product_data as $product) {
				$products[] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'price'      => $this->currency->format($product['price'], '', '', false)
				);
			}

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'],
				'products'    => $products
			);
		}

		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		$data['country_id'] = $this->config->get('config_country_id');
		$data['countries']  = $this->model_localisation_country->getCountries();
		$data['zones']      = $this->model_localisation_zone->getZonesByCountryId($this->config->get('config_country_id'));

		$this->session->data['csr'] = array(
			'customer_group_id' => $this->config->get('config_customer_group_id'),
			'firstname'         => '',
			'lastname'          => '',
			'email'             => '',
			'telephone'         => ''
		);

		$this->document->addStyle('//cdn.datatables.net/1.10.10/css/dataTables.bootstrap.min.css');

		$this->document->addScript('//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js', 'footer');
		$this->document->addScript('//cdn.datatables.net/1.10.10/js/dataTables.bootstrap.min.js', 'footer');

		// go through all enabled payment methods and add their assets if they're available
		$this->load->model('extension/extension');
		$results = $this->model_extension_extension->getExtensions('payment');

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('payment/' . $result['code']);
				$this->{'model_payment_' . $result['code']}->addAssets(true);
			}
		}

		$data['header'] = $this->load->controller('csr/header');
		$data['footer'] = $this->load->controller('csr/footer');

		$this->response->setOutput($this->load->view('csr/checkout', $data));
	}

	public function customer() {
		$json = array();

		if (!$this->user->isLogged()) {
			$json['redirect'] = $this->url->link('csr/login', '', true);
		}

		if ($this->customer->isLogged()) {
			$this->load->language('csr/checkout');

			$this->load->model('account/account');
			$this->load->model('checkout/recurring_order');

			$json['instagram_accounts'] = array();

			$instagram_account_data = $this->model_account_account->getAccounts('instagram');

			foreach ($instagram_account_data as $instagram_account) {
				if (isset($instagram_account->ExpiresDateTime)) {
					$expires = new DateTime($instagram_account->ExpiresDateTime);
					$today   = new DateTime();

					if ($expires > $today) {
						$interval = $today->diff($expires);

						$days = $interval->format('%a');

						if ($days > 1) {
							$time = $interval->format($this->language->get('date_days_remaining'));
						} else if ($days) {
							$time = $interval->format($this->language->get('date_day_remaining'));
						} else {
							$time = $interval->format($this->language->get('date_hours_remaining'));
						}
					} else {
						$time = $this->language->get('text_expired');
					}
				} else {
					$time = $this->language->get('text_unlimited');
				}

				if (isset($instagram_account->MetaData['info']['profile_picture'])) {
					$image = $instagram_account->MetaData['info']['profile_picture'];
				} else {
					$image = '';
				}

				$recurring_order = $this->model_checkout_recurring_order->getActiveRecurringOrder($this->customer->getId(), $instagram_account->Id);

				if ($recurring_order) {
					$recurring_product_id = $recurring_order['product_id'];
				} else {
					$recurring_product_id = 0;
				}

				$json['instagram_accounts'][] = array(
					'account_id'           => $instagram_account->Id,
					'username'             => $instagram_account->Username,
					'image'                => $image,
					'time'                 => $time,
					'price'                => $this->currency->format($this->tax->calculate(0, 0, $this->config->get('config_tax')), '', '', false),
					'recurring_product_id' => $recurring_product_id
				);
			}

			$json['totals'] = array();

			foreach ($this->getTotals() as $total) {
				if ($total['code'] == 'total') {
					$json['total'] = sprintf($this->language->get('text_cart_total'), $this->currency->format($total['value']));
				}

				$json['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'])
				);
			}

			$json['firstname'] = $this->customer->getFirstName();
			$json['lastname']  = $this->customer->getLastName();
			$json['email']     = $this->customer->getEmail();
			$json['telephone'] = $this->customer->getTelephone();

			$this->session->data['payment_method'] = $this->getPaymentMethod();

			if ($this->session->data['payment_method']) {
				$this->config->set('config_template', 'default');

				$payment_html = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);

				$json['payment_html'] = $payment_html;
			} else {
				$json['payment_html'] = '';
			}

			$this->cart->clear();
			$this->customer->logout();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function cart() {
		$json = array();

		if (!$this->user->isLogged()) {
			$json['redirect'] = $this->url->link('csr/login', '', true);
		}

		if (!$json) {
			$this->load->language('csr/checkout');

			$json = array(
				'account_totals' => array(),
				'totals'         => array(),
				'total'          => 0
			);

			$this->addProductsToCart();
			$this->addCoupon();
			$this->addVoucher();

			$this->setCustomerInformation();
			$this->setContactInformation();

			foreach ($this->request->post['accounts'] as $account) {
				$total = 0;

				foreach ($this->cart->getProducts() as $cart_product) {
					if ($account['account_id'] == $cart_product['account_id']) {
						$total = $this->tax->calculate($cart_product['total'], $cart_product['tax_class_id'], $this->config->get('config_tax'));
						break;
					}
				}

				$json['account_totals'][] = array(
					'account_id' => $account['account_id'],
					'total'      => $this->currency->format($total)
				);
			}

			$json['totals'] = array();

			foreach ($this->getTotals() as $total) {
				if ($total['code'] == 'total') {
					$json['total'] = sprintf($this->language->get('text_cart_total'), $this->currency->format($total['value']));
				}

				$json['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'])
				);
			}
		}

		if ($this->customer->isLogged()) {
			$this->cart->clear();
			$this->customer->logout();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validate() {
		$json = array('success' => false);

		if (!$this->user->isLogged()) {
			$json['redirect'] = $this->url->link('csr/login', '', true);
		}

		if (!isset($json['redirect']) && $this->customer->isLogged()) {
			$this->language->load('csr/checkout');

			$this->addProductsToCart();
			$this->addCoupon();
			$this->addVoucher();

			$this->setCustomerInformation();
			$this->setContactInformation();

			$this->validateCart();
			$this->validateContact();

			if (!$this->error) {
				$this->load->model('csr/customer');

				// update customer information
				$this->model_csr_customer->editCustomer(array(
					'firstname' => $this->session->data['csr']['firstname'],
					'lastname'  => $this->session->data['csr']['lastname'],
					'email'     => $this->session->data['csr']['email'],
					'telephone' => $this->session->data['csr']['telephone']
				));

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

				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id']       = $this->config->get('config_store_id');
				$order_data['store_name']     = $this->config->get('config_name');

				if ($order_data['store_id']) {
					$order_data['store_url'] = $this->config->get('config_url');
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}

				$customer_info = $this->model_customer_customer->getCustomer($this->customer->getId());

				$order_data['customer_id']       = $this->customer->getId();
				$order_data['customer_group_id'] = $this->session->data['csr']['customer_group_id'];
				$order_data['firstname']         = $this->session->data['csr']['firstname'];
				$order_data['lastname']          = $this->session->data['csr']['lastname'];
				$order_data['email']             = $this->session->data['csr']['email'];
				$order_data['telephone']         = $this->session->data['csr']['telephone'];
				$order_data['custom_field']      = json_decode($customer_info['custom_field'], true);

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

				$order_data['user_id'] = $this->user->getId();

				$this->load->model('checkout/order');

				$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);

				$json['success'] = true;
			} else {
				$json['error'] = $this->error;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function addProductsToCart() {
		$this->load->model('catalog/product');
		$this->load->model('checkout/recurring_order');

		$this->cart->clear();

		if (isset($this->request->post['accounts']) && is_array($this->request->post['accounts'])) {
			foreach ($this->request->post['accounts'] as $account) {
				if (!empty($account['product_id'])) {
					$recurring_order = $this->model_checkout_recurring_order->getActiveRecurringOrder($this->customer->getId(), $account['account_id']);

					if ($recurring_order && $recurring_order['product_id'] == $account['product_id']) {
						continue;
					}

					$recurring_profiles = $this->model_catalog_product->getProfiles($account['product_id']);

					if ($recurring_profiles) {
						$recurring_id = $recurring_profiles[0]['recurring_id'];
					} else {
						$recurring_id = 0;
					}

					if ($this->currency->getCode() != $this->config->get('config_currency')) {
						$account['price'] = $this->currency->convert($account['price'], $this->currency->getCode(), $this->config->get('config_currency'));
					}

					$this->cart->add($account['product_id'], $account['account_id'], 1, $recurring_id, $account['price']);
				}
			}
		}
	}

	protected function addCoupon() {
		unset($this->session->data['coupon']);

		if (!empty($this->request->post['coupon']) && $this->validateCoupon($this->request->post['coupon'])) {
			$this->session->data['coupon'] = $this->request->post['coupon'];
		}
	}

	protected function addVoucher() {
		unset($this->session->data['voucher']);

		if (!empty($this->request->post['coupon']) && $this->validateVoucher($this->request->post['coupon'])) {
			$this->session->data['voucher'] = $this->request->post['coupon'];
		}
	}

	protected function setCustomerInformation() {
		if ($this->customer->isLogged()) {
			$this->session->data['csr']['customer_group_id'] = $this->customer->getGroupId();
		} else {
			$this->session->data['csr']['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['firstname'])) {
			$this->session->data['csr']['firstname'] = $this->request->post['firstname'];
		} else {
			$this->session->data['csr']['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$this->session->data['csr']['lastname'] = $this->request->post['lastname'];
		} else {
			$this->session->data['csr']['lastname'] = '';
		}
	}

	protected function setContactInformation() {
		if (isset($this->request->post['email'])) {
			$this->session->data['csr']['email'] = $this->request->post['email'];
		} else {
			$this->session->data['csr']['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$this->session->data['csr']['telephone'] = $this->request->post['telephone'];
		} else {
			$this->session->data['csr']['telephone'] = '';
		}
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

	protected function getTotals() {
		$total_data = array();
		$total      = 0;
		$taxes      = $this->cart->getTaxes();

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

				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}

		return $total_data;
	}

	protected function validateCart() {
		if (!$this->cart->hasProducts()) {
			$this->error['empty'] = $this->language->get('error_empty');
		}

		return !$this->error;
	}

	protected function validateCoupon($coupon) {
		$this->load->model('checkout/coupon');

		$coupon_info = $this->model_checkout_coupon->getCoupon($coupon);

		if (!$coupon_info) {
			$this->error['coupon'] = $this->language->get('error_coupon');
		}

		return !$this->error;
	}

	protected function validateVoucher($voucher) {
		$this->load->model('checkout/voucher');

		$voucher_info = $this->model_checkout_voucher->getVoucher($voucher);

		if (!$voucher_info) {
			$this->error['voucher'] = $this->language->get('error_voucher');
		}

		return !$this->error;
	}

	protected function validateContact() {
		if ((utf8_strlen($this->session->data['csr']['email']) > 96) || !filter_var($this->session->data['csr']['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		return !$this->error;
	}

	public function autocomplete_customer() {
		$json = array();

		if ($this->user->isLogged() && isset($this->request->get['filter_name'])) {
			$this->load->model('csr/customer');

			$data = array(
				'filter_name'  => $this->request->get['filter_name'],
				'filter_email' => $this->request->get['filter_name'],
				'start' => 0,
				'limit' => 20
			);

			$results = $this->model_csr_customer->getCustomers($data);

			foreach ($results as $result) {
				$json[] = array(
					'customer_id' => $result['customer_id'],
					'firstname'   => $result['firstname'],
					'lastname'    => $result['lastname'],
					'email'       => $result['email']
				);
			}

			$sort_order = array();

			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['firstname'] . ' ' . $value['lastname'];
			}

			array_multisort($sort_order, SORT_ASC, $json);
		}

		if ($this->customer->isLogged()) {
			$this->cart->clear();
			$this->customer->logout();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	///////////
	// CUSTOMER
	//

	public function customer_insert() {
		$this->load->language('csr/checkout');

		$json = array('success' => false);

		$this->load->model('customer/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateCustomerInsert()) {
			$customer_id = $this->model_customer_customer->addCustomer($this->request->post);

			// Clear any previous login attempts for unregistered customers.
			$this->model_customer_customer->deleteLoginAttempts($this->request->post['email']);

			// Add to activity log
			$this->load->model('customer/activity');

			$activity_data = array(
				'customer_id' => $customer_id,
				'name'        => $this->request->post['firstname'] . ' ' . $this->request->post['lastname']
			);

			$this->model_customer_activity->addActivity('register', $activity_data);

			$json['success']      = true;
			$json['customer_id']  = $customer_id;
		} else {
			$json['errors'] = $this->error;
		}

		if ($this->customer->isLogged()) {
			$this->cart->clear();
			$this->customer->logout();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateCustomerInsert() {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (utf8_strlen($this->request->post['email']) > 96 || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->model_customer_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		// Customer Group
		if (isset($this->request->post['customer_group_id'])) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		// Custom field validation
		$this->load->model('customer/custom_field');

		$custom_fields = $this->model_customer_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			}
		}

		if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}


	////////////
	// INSTAGRAM
	//

	public function instagram_insert() {
		$this->load->language('csr/checkout');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateInstagramInsert()) {
			$this->load->model('account/account');

			$result = $this->model_account_account->addAccount('instagram', $this->request->post['username'], $this->request->post['password']);

			if ($result && $result->success) {
				$account_info = $this->model_account_account->getAccount($result->data);

				if ($account_info) {
					if (isset($account_info->ExpiresDateTime)) {
						$expires = new DateTime($account_info->ExpiresDateTime);
						$today   = new DateTime();

						if ($expires > $today) {
							$interval = $today->diff($expires);

							$days = $interval->format('%a');

							if ($days > 1) {
								$time = $interval->format($this->language->get('date_days_remaining'));
							} else if ($days) {
								$time = $interval->format($this->language->get('date_day_remaining'));
							} else {
								$time = $interval->format($this->language->get('date_hours_remaining'));
							}
						} else {
							$time = $this->language->get('text_expired');
						}
					} else {
						$time = $this->language->get('text_unlimited');
					}

					$json['data'] = array(
						'account_id' => $account_info->Id,
						'username'   => $account_info->Username,
						'image'      => (isset($account_info->MetaData['info']['profile_picture']) ? $account_info->MetaData['info']['profile_picture'] : ''),
						'time'       => $time,
						'price'      => $this->currency->format($this->tax->calculate(0, 0, $this->config->get('config_tax')), '', '', false)
					);
				} else {
					$json['data'] = array();
				}
			} else if (isset($result->errors)) {
				$json['errors'] = $result->errors;
			} else {
				$json['errors']['warning'] = $this->language->get('error_unknown');
			}
		} else {
			$json['errors'] = $this->error;
		}

		if ($this->customer->isLogged()) {
			$this->cart->clear();
			$this->customer->logout();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validateInstagramInsert() {
		if (empty($this->request->post['username'])) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (empty($this->request->post['password'])) {
			$this->error['password'] = $this->language->get('error_password');
		}

		return !$this->error;
	}
}