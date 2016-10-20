<?php
class ControllerPaymentMoip extends Controller {
	private $error = array();

	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "moip_payment_method` (
				`payment_method_id` varchar(16) NOT NULL,
				`cvc` varchar(4) NOT NULL,
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`payment_method_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'moip_customer_id'");

		if (!$results->row) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD `moip_customer_id` varchar(16) NOT NULL");
		}

		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "affiliate` LIKE 'moip_customer_id'");

		if (!$results->row) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "affiliate` ADD `moip_customer_id` varchar(16) NOT NULL");
		}
	}

	public function index() {
		$data = $this->load->language('payment/moip');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('moip', $this->request->post);

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
			'href' => $this->url->link('payment/moip', 'token=' . $this->session->data['token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['api_token'])) {
			$data['error_api_token'] = $this->error['api_token'];
		} else {
			$data['error_api_token'] = '';
		}

		if (isset($this->error['api_key'])) {
			$data['error_api_key'] = $this->error['api_key'];
		} else {
			$data['error_api_key'] = '';
		}

		if (isset($this->error['public_key'])) {
			$data['error_public_key'] = $this->error['public_key'];
		} else {
			$data['error_public_key'] = '';
		}

		if (isset($this->error['currencies'])) {
			$data['error_currencies'] = $this->error['currencies'];
		} else {
			$data['error_currencies'] = '';
		}

		$data['action'] = $this->url->link('payment/moip', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['moip_status'])) {
			$data['moip_status'] = $this->request->post['moip_status'];
		} else {
			$data['moip_status'] = $this->config->get('moip_status');
		}

		if (isset($this->request->post['moip_api_token'])) {
			$data['moip_api_token'] = $this->request->post['moip_api_token'];
		} else {
			$data['moip_api_token'] = $this->config->get('moip_api_token');
		}

		if (isset($this->request->post['moip_api_key'])) {
			$data['moip_api_key'] = $this->request->post['moip_api_key'];
		} else {
			$data['moip_api_key'] = $this->config->get('moip_api_key');
		}

		if (isset($this->request->post['moip_public_key'])) {
			$data['moip_public_key'] = $this->request->post['moip_public_key'];
		} else {
			$data['moip_public_key'] = $this->config->get('moip_public_key');
		}

		if (isset($this->request->post['moip_environment'])) {
			$data['moip_environment'] = $this->request->post['moip_environment'];
		} else {
			$data['moip_environment'] = $this->config->get('moip_environment');
		}

		if (isset($this->request->post['moip_transaction_method'])) {
			$data['moip_transaction_method'] = $this->request->post['moip_transaction_method'];
		} else {
			$data['moip_transaction_method'] = $this->config->get('moip_transaction_method');
		}

		if (isset($this->request->post['moip_order_status_id'])) {
			$data['moip_order_status_id'] = $this->request->post['moip_order_status_id'];
		} else {
			$data['moip_order_status_id'] = $this->config->get('moip_order_status_id');
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['moip_sort_order'])) {
			$data['moip_sort_order'] = $this->request->post['moip_sort_order'];
		} else {
			$data['moip_sort_order'] = $this->config->get('moip_sort_order');
		}

		if (isset($this->request->post['moip_debug'])) {
			$data['moip_debug'] = $this->request->post['moip_debug'];
		} else {
			$data['moip_debug'] = $this->config->get('moip_debug');
		}

		if (isset($this->request->post['moip_currencies'])) {
			$data['moip_currencies'] = $this->request->post['moip_currencies'];
		} else if ($this->config->get('moip_currencies')) {
			$data['moip_currencies'] = $this->config->get('moip_currencies');
		} else {
			$data['moip_currencies'] = array();
		}

		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/moip', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/moip')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['moip_api_token'])) {
			$this->error['api_token'] = $this->language->get('error_api_token');
		}

		if (empty($this->request->post['moip_api_key'])) {
			$this->error['api_key'] = $this->language->get('error_api_key');
		}

		if (empty($this->request->post['moip_public_key'])) {
			$this->error['public_key'] = $this->language->get('error_public_key');
		}

		if (empty($this->request->post['moip_currencies'])) {
			$this->error['currencies'] = $this->language->get('error_currency');
		}

		return !$this->error;
	}
}