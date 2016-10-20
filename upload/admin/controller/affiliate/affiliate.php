<?php
class ControllerAffiliateAffiliate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('affiliate/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		$this->getList();
	}

	public function add() {
		$this->load->language('affiliate/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_affiliate_affiliate->addAffiliate($this->request->post);

			$this->session->data['success'] = $this->language->get('success_add');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate', $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('affiliate/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_affiliate_affiliate->editAffiliate($this->request->get['affiliate_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('success_edit');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate', $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('affiliate/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $affiliate_id) {
				$this->model_affiliate_affiliate->deleteAffiliate($affiliate_id);
			}

			$this->session->data['success'] = sprintf($this->language->get('success_delete'), count($this->request->post['selected']));

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate', $url, true));
		}

		$this->getList();
	}

	public function approve() {
		$this->load->language('affiliate/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		if (isset($this->request->get['affiliate_id']) && $this->validateApprove()) {
			$this->model_affiliate_affiliate->approve($this->request->get['affiliate_id']);

			$this->session->data['success'] = $this->language->get('success_approve');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate', $url, true));
		}

		$this->getList();
	}

	public function unlock() {
		$this->load->language('affiliate/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate');

		if (isset($this->request->get['email']) && $this->validateUnlock()) {
			$this->model_affiliate_affiliate->deleteLoginAttempts($this->request->get['email']);

			$this->session->data['success'] = $this->language->get('success_unlock');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate', $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['filter_affiliate_group_id'])) {
			$filter_affiliate_group_id = trim($this->request->get['filter_affiliate_group_id']);
		} else {
			$filter_affiliate_group_id = null;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = trim($this->request->get['filter_name']);
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = trim($this->request->get['filter_email']);
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = trim($this->request->get['filter_date_added']);
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = trim($this->request->get['sort']);
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = trim($this->request->get['order']);
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = trim($this->request->get['page']);
		} else {
			$page = 1;
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('affiliate/affiliate', $url, true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		$data['add']    = $this->url->link('affiliate/affiliate/add', $url, true);
		$data['delete'] = $this->url->link('affiliate/affiliate/delete', $url, true);

		$data['token'] = $this->session->data['token'];

		$this->load->model('affiliate/affiliate_group');
		$data['affiliate_groups'] = $this->model_affiliate_affiliate_group->getAffiliateGroups();

		$data['affiliates'] = array();

		$filter_data = array(
			'filter_affiliate_group_id' => $filter_affiliate_group_id,
			'filter_name'               => $filter_name,
			'filter_email'              => $filter_email,
			'filter_date_added'         => $filter_date_added,
			'sort'                      => $sort,
			'order'                     => $order,
			'start'                     => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                     => $this->config->get('config_limit_admin')
		);

		$affiliate_total = $this->model_affiliate_affiliate->getTotalAffiliates($filter_data);

		$results = $this->model_affiliate_affiliate->getAffiliates($filter_data);

		foreach ($results as $result) {
			$login_info = $this->model_affiliate_affiliate->getTotalLoginAttempts($result['email']);

			if ($login_info && $login_info['total'] >= $this->config->get('config_login_attempts')) {
				$unlock = $this->url->link('affiliate/affiliate/unlock', $url . '&email=' . $result['email'], true);
			} else {
				$unlock = '';
			}

			$data['affiliates'][] = array(
				'affiliate_id'           => $result['affiliate_id'],
				'name'                   => $result['name'],
				'email'                  => $result['email'],
				'total_customers'        => $result['total_customers'],
				'total_active_customers' => $result['total_active_customers'],
				'affiliate_group'        => $result['affiliate_group'],
				'balance'                => $this->currency->format($result['balance'], $this->config->get('config_currency')),
				'date_added'             => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'unlock'                 => $unlock,
				'edit'                   => $this->url->link('affiliate/affiliate/edit', $url . '&affiliate_id=' . $result['affiliate_id'], true),
				'href_customers'         => $this->url->link('customer/customer', 'token=' . $this->session->data['token'] . '&filter_affiliate_id=' . $result['affiliate_id'], true)
			);
		}

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_name']                   = $this->url->link('affiliate/affiliate', $url . '&sort=name', true);
		$data['sort_email']                  = $this->url->link('affiliate/affiliate', $url . '&sort=a.email', true);
		$data['sort_affiliate_group']        = $this->url->link('affiliate/affiliate', $url . '&sort=affiliate_group', true);
		$data['sort_total_customers']        = $this->url->link('affiliate/affiliate', $url . '&sort=total_customers', true);
		$data['sort_total_active_customers'] = $this->url->link('affiliate/affiliate', $url . '&sort=total_active_customers', true);
		$data['sort_date_added']             = $this->url->link('affiliate/affiliate', $url . '&sort=a.date_added', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $affiliate_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('affiliate/affiliate', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($affiliate_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($affiliate_total - $this->config->get('config_limit_admin'))) ? $affiliate_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $affiliate_total, ceil($affiliate_total / $this->config->get('config_limit_admin')));

		$data['filter_name']               = $filter_name;
		$data['filter_email']              = $filter_email;
		$data['filter_affiliate_group_id'] = $filter_affiliate_group_id;
		$data['filter_date_added']         = $filter_date_added;

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/affiliate_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['affiliate_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['affiliate_group'])) {
			$data['error_affiliate_group'] = $this->error['affiliate_group'];
		} else {
			$data['error_affiliate_group'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['check'])) {
			$data['error_check'] = $this->error['check'];
		} else {
			$data['error_check'] = '';
		}

		if (isset($this->error['paypal'])) {
			$data['error_paypal'] = $this->error['paypal'];
		} else {
			$data['error_paypal'] = '';
		}

		if (isset($this->error['bank_account_name'])) {
			$data['error_bank_account_name'] = $this->error['bank_account_name'];
		} else {
			$data['error_bank_account_name'] = '';
		}

		if (isset($this->error['bank_account_number'])) {
			$data['error_bank_account_number'] = $this->error['bank_account_number'];
		} else {
			$data['error_bank_account_number'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['address_1'])) {
			$data['error_address_1'] = $this->error['address_1'];
		} else {
			$data['error_address_1'] = '';
		}

		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}

		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}

		if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = '';
		}

		if (isset($this->error['zone'])) {
			$data['error_zone'] = $this->error['zone'];
		} else {
			$data['error_zone'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('affiliate/affiliate', $url, true)
		);

		if (!isset($this->request->get['affiliate_id'])) {
			$data['action'] = $this->url->link('affiliate/affiliate/add', $url, true);
		} else {
			$data['action'] = $this->url->link('affiliate/affiliate/edit', $url . '&affiliate_id=' . $this->request->get['affiliate_id'], true);
		}

		$data['cancel'] = $this->url->link('affiliate/affiliate', $url, true);

		if (isset($this->request->get['affiliate_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->get['affiliate_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['affiliate_id'])) {
			$data['affiliate_id'] = $this->request->get['affiliate_id'];
		} else {
			$data['affiliate_id'] = 0;
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} else if (!empty($affiliate_info)) {
			$data['path'] = $affiliate_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} else if (!empty($affiliate_info)) {
			$data['parent_id'] = $affiliate_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else if (!empty($affiliate_info)) {
			$data['firstname'] = $affiliate_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else if (!empty($affiliate_info)) {
			$data['lastname'] = $affiliate_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else if (!empty($affiliate_info)) {
			$data['email'] = $affiliate_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else if (!empty($affiliate_info)) {
			$data['telephone'] = $affiliate_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} else if (!empty($affiliate_info)) {
			$data['company'] = $affiliate_info['company'];
		} else {
			$data['company'] = '';
		}

		if (isset($this->request->post['website'])) {
			$data['website'] = $this->request->post['website'];
		} else if (!empty($affiliate_info)) {
			$data['website'] = $affiliate_info['website'];
		} else {
			$data['website'] = '';
		}

		if (isset($this->request->post['address_1'])) {
			$data['address_1'] = $this->request->post['address_1'];
		} else if (!empty($affiliate_info)) {
			$data['address_1'] = $affiliate_info['address_1'];
		} else {
			$data['address_1'] = '';
		}

		if (isset($this->request->post['address_2'])) {
			$data['address_2'] = $this->request->post['address_2'];
		} else if (!empty($affiliate_info)) {
			$data['address_2'] = $affiliate_info['address_2'];
		} else {
			$data['address_2'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} else if (!empty($affiliate_info)) {
			$data['city'] = $affiliate_info['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} else if (!empty($affiliate_info)) {
			$data['postcode'] = $affiliate_info['postcode'];
		} else {
			$data['postcode'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} else if (!empty($affiliate_info)) {
			$data['country_id'] = $affiliate_info['country_id'];
		} else {
			$data['country_id'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		} else if (!empty($affiliate_info)) {
			$data['zone_id'] = $affiliate_info['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} else if (!empty($affiliate_info)) {
			$data['code'] = $affiliate_info['code'];
		} else {
			$data['code'] = uniqid();
		}

		$this->load->model('affiliate/affiliate_group');
		$data['affiliate_groups'] = $this->model_affiliate_affiliate_group->getAffiliateGroups();

		if (isset($this->request->post['affiliate_group_id'])) {
			$data['affiliate_group_id'] = $this->request->post['affiliate_group_id'];
		} else if (!empty($affiliate_info)) {
			$data['affiliate_group_id'] = $affiliate_info['affiliate_group_id'];
		} else {
			$data['affiliate_group_id'] = 0;
		}

		if (isset($this->request->post['account_fee'])) {
			$data['account_fee'] = $this->request->post['account_fee'];
		} else if (!empty($affiliate_info)) {
			$data['account_fee'] = $affiliate_info['account_fee'];
		} else {
			$data['account_fee'] = 0.00;
		}

		if (isset($this->request->post['tax'])) {
			$data['tax'] = $this->request->post['tax'];
		} else if (!empty($affiliate_info)) {
			$data['tax'] = $affiliate_info['tax'];
		} else {
			$data['tax'] = '';
		}

		if (isset($this->request->post['payment'])) {
			$data['payment'] = $this->request->post['payment'];
		} else if (!empty($affiliate_info)) {
			$data['payment'] = $affiliate_info['payment'];
		} else {
			$data['payment'] = 'paypal';
		}

		if (isset($this->request->post['payment_data'])) {
			$data['payment_data'] = $this->request->post['payment_data'];
		} else if (!empty($affiliate_info)) {
			$data['payment_data'] = $affiliate_info['payment_data'];
		} else {
			$data['payment_data'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else if (!empty($affiliate_info)) {
			$data['status'] = $affiliate_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}
	
		if (isset($this->request->post['braintree_customer_id'])) {
			$data['braintree_customer_id'] = $this->request->post['braintree_customer_id'];
		} else if (!empty($affiliate_info)) {
			$data['braintree_customer_id'] = $affiliate_info['braintree_customer_id'];
		} else {
			$data['braintree_customer_id'] = '';
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/affiliate_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'affiliate/affiliate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['affiliate_group_id'])) {
			$this->error['affiliate_group'] = $this->language->get('error_affiliate_group');
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (utf8_strlen($this->request->post['email']) > 96 || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->request->post['payment'] == 'check') {
			if (empty($this->request->post['payment_data']['check'])) {
				$this->error['check'] = $this->language->get('error_check');
			}
		} else if ($this->request->post['payment'] == 'paypal') {
			if ((utf8_strlen($this->request->post['payment_data']['paypal']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['payment_data']['paypal'])) {
				$this->error['paypal'] = $this->language->get('error_paypal');
			}
		} else if ($this->request->post['payment'] == 'bank') {
			if (empty($this->request->post['payment_data']['bank_account_name'])) {
				$this->error['bank_account_name'] = $this->language->get('error_bank_account_name');
			}

			if (empty($this->request->post['payment_data']['bank_account_number'])) {
				$this->error['bank_account_number'] = $this->language->get('error_bank_account_number');
			}
		}

		$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByEmail($this->request->post['email']);

		if (!isset($this->request->get['affiliate_id'])) {
			if ($affiliate_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($affiliate_info && ($this->request->get['affiliate_id'] != $affiliate_info['affiliate_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if ($this->request->post['password'] || (!isset($this->request->get['affiliate_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}

		if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		if (empty($this->request->post['country_id'])) {
			$this->error['country'] = $this->language->get('error_country');
		}

		if (empty($this->request->post['zone_id'])) {
			$this->error['zone'] = $this->language->get('error_zone');
		}

		if (empty($this->request->post['code'])) {
			$this->error['code'] = $this->language->get('error_code');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'affiliate/affiliate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateApprove() {
		if (!$this->user->hasPermission('modify', 'affiliate/affiliate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateUnlock() {
		if (!$this->user->hasPermission('modify', 'affiliate/affiliate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function transaction() {
		$data = $this->load->language('affiliate/affiliate');

		$this->load->model('affiliate/affiliate');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$results = $this->model_affiliate_affiliate->getTransactions($this->request->get['affiliate_id'], ($page - 1) * $this->config->get('config_limit_admin'), $this->config->get('config_limit_admin'));

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$data['balance'] = $this->currency->format($this->model_affiliate_affiliate->getTransactionTotal($this->request->get['affiliate_id']), $this->config->get('config_currency'));

		$transaction_total = $this->model_affiliate_affiliate->getTotalTransactions($this->request->get['affiliate_id']);

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('affiliate/affiliate/transaction', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $this->request->get['affiliate_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($transaction_total - $this->config->get('config_limit_admin'))) ? $transaction_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $transaction_total, ceil($transaction_total / $this->config->get('config_limit_admin')));

		$this->response->setOutput($this->load->view('affiliate/affiliate_transaction', $data));
	}

	public function addTransaction() {
		$this->load->language('affiliate/affiliate');

		$json = array();

		if (!$this->user->hasPermission('modify', 'affiliate/affiliate')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('affiliate/affiliate');

			$this->model_affiliate_affiliate->addTransaction($this->request->get['affiliate_id'], $this->request->post['description'], $this->request->post['amount']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_affiliate_group_id']) && !in_array('affiliate_group_id', $blacklist)) {
			$url_data['filter_affiliate_group_id'] = html_entity_decode($this->request->get['filter_affiliate_group_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_name']) && !in_array('name', $blacklist)) {
			$url_data['filter_name'] = html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_email']) && !in_array('email', $blacklist)) {
			$url_data['filter_email'] = html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_status']) && !in_array('status', $blacklist)) {
			$url_data['filter_status'] = $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added']) && !in_array('date_added', $blacklist)) {
			$url_data['filter_date_added'] = $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort']) && !in_array('sort', $blacklist)) {
			$url_data['sort'] = $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && !in_array('order', $blacklist)) {
			$url_data['order'] = $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && !in_array('page', $blacklist)) {
			$url_data['page'] = $this->request->get['page'];
		}

		return http_build_query($url_data);
	}

	public function autocomplete() {
		$affiliate_data = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
				'start'        => 0,
				'limit'        => 5
			);

			$this->load->model('affiliate/affiliate');

			$results = $this->model_affiliate_affiliate->getAffiliates($filter_data);

			foreach ($results as $result) {
				$affiliate_data[] = array(
					'affiliate_id' => $result['affiliate_id'],
					'name'         => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'email'        => $result['email']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($affiliate_data));
	}

	public function login() {
		if (isset($this->request->get['affiliate_id'])) {
			$affiliate_id = $this->request->get['affiliate_id'];
		} else {
			$affiliate_id = 0;
		}

		$this->load->model('affiliate/affiliate');

		$affiliate = $this->model_affiliate_affiliate->getAffiliate($affiliate_id);

		if ($affiliate['status'] && $affiliate['approved'] && $this->affiliate->login($affiliate['email'], '', true)) {
			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($this->request->get['store_id']);

			if ($store_info) {
				$this->response->redirect($store_info['url'] . 'index.php?route=affiliate/dashboard');
			} else {
				$this->response->redirect(HTTP_CATALOG . 'index.php?route=affiliate/dashboard');
			}
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_not_found'] = $this->language->get('text_not_found');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], true)
			);

			$data['header']      = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer']      = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}