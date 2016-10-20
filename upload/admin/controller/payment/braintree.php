<?php
class ControllerPaymentBraintree extends Controller {
	private $error = array();

	public function install() {
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'braintree_customer_id'");

		if (!$results->row) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `braintree_customer_id` varchar(36) NOT NULL");
		}

		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "affiliate` LIKE 'braintree_customer_id'");

		if (!$results->row) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "affiliate` ADD `braintree_customer_id` varchar(36) NOT NULL");
		}
	}

	public function index() {
		$data = $this->load->language('payment/braintree');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('braintree', $this->request->post);

			$this->session->data['success'] = $this->language->get('success_update');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/braintree', 'token=' . $this->session->data['token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['merchant_id'])) {
			$data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$data['error_merchant_id'] = '';
		}

		if (isset($this->error['public_key'])) {
			$data['error_public_key'] = $this->error['public_key'];
		} else {
			$data['error_public_key'] = '';
		}

		if (isset($this->error['private_key'])) {
			$data['error_private_key'] = $this->error['private_key'];
		} else {
			$data['error_private_key'] = '';
		}

		$data['action'] = $this->url->link('payment/braintree', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['braintree_status'])) {
			$data['braintree_status'] = $this->request->post['braintree_status'];
		} else {
			$data['braintree_status'] = $this->config->get('braintree_status');
		}

		if (isset($this->request->post['braintree_merchant_id'])) {
			$data['braintree_merchant_id'] = $this->request->post['braintree_merchant_id'];
		} else {
			$data['braintree_merchant_id'] = $this->config->get('braintree_merchant_id');
		}

		if (isset($this->request->post['braintree_public_key'])) {
			$data['braintree_public_key'] = $this->request->post['braintree_public_key'];
		} else {
			$data['braintree_public_key'] = $this->config->get('braintree_public_key');
		}

		if (isset($this->request->post['braintree_private_key'])) {
			$data['braintree_private_key'] = $this->request->post['braintree_private_key'];
		} else {
			$data['braintree_private_key'] = $this->config->get('braintree_private_key');
		}

		if (isset($this->request->post['braintree_transaction_server'])) {
			$data['braintree_transaction_server'] = $this->request->post['braintree_transaction_server'];
		} else {
			$data['braintree_transaction_server'] = $this->config->get('braintree_transaction_server');
		}

		if (isset($this->request->post['braintree_transaction_method'])) {
			$data['braintree_transaction_method'] = $this->request->post['braintree_transaction_method'];
		} else {
			$data['braintree_transaction_method'] = $this->config->get('braintree_transaction_method');
		}

		if (isset($this->request->post['braintree_order_status_id'])) {
			$data['braintree_order_status_id'] = $this->request->post['braintree_order_status_id'];
		} else {
			$data['braintree_order_status_id'] = $this->config->get('braintree_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['braintree_sort_order'])) {
			$data['braintree_sort_order'] = $this->request->post['braintree_sort_order'];
		} else {
			$data['braintree_sort_order'] = $this->config->get('braintree_sort_order');
		}

		if (isset($this->request->post['braintree_debug'])) {
			$data['braintree_debug'] = $this->request->post['braintree_debug'];
		} else {
			$data['braintree_debug'] = $this->config->get('braintree_debug');
		}

		if (isset($this->request->post['braintree_currencies'])) {
			$data['braintree_currencies'] = $this->request->post['braintree_currencies'];
		} else if ($this->config->get('braintree_currencies')) {
			$data['braintree_currencies'] = $this->config->get('braintree_currencies');
		} else {
			$data['braintree_currencies'] = array();
		}

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/braintree', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/braintree')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['braintree_merchant_id'])) {
			$this->error['merchant_id'] = $this->language->get('error_merchant_id');
		}

		if (empty($this->request->post['braintree_public_key'])) {
			$this->error['public_key'] = $this->language->get('error_public_key');
		}

		if (empty($this->request->post['braintree_private_key'])) {
			$this->error['private_key'] = $this->language->get('error_private_key');
		}

		// make sure at least one currency is enabled
		$currency_enabled = false;

		foreach ($this->request->post['braintree_currencies'] as $key => $currency) {
			if ($currency['status']) {
				$currency_enabled = true;
				break;
			}
		}

		if (!$currency_enabled) {
			$this->error['warning'] = $this->language->get('error_currency');
		}

		return !$this->error;
	}
}