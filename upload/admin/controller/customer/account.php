<?php
class ControllerCustomerAccount extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('customer/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/account');

		$this->getList();
	}

	public function edit() {
		$this->load->language('customer/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('customer/account');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_customer_account->editAccount($this->request->get['account_id'], $this->request->post);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing . '_status')) {
					$this->{$marketing} = new $marketing($this->registry);

					$this->load->model('extension/marketing/' . $marketing);
					$this->{'model_extension_marketing_' . $marketing}->updateCustomer($this->request->post['customer_id']);

					if (!empty($this->request->post['new_customer_id'])) {
						$this->load->model('extension/marketing/' . $marketing);
						$this->{'model_extension_marketing_' . $marketing}->updateCustomer($this->request->post['new_customer_id']);
					}
				}
			}

			$this->session->data['success'] = $this->language->get('success_update');

			$this->response->redirect($this->url->link('customer/account', $this->getUrl(), true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('customer/account');

		if ($this->validateDelete()) {
			$this->load->model('customer/account');
			$this->model_customer_account->deleteAccount($this->request->get['customer_id'], $this->request->get['account_id']);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing . '_status')) {
					$this->{$marketing} = new $marketing($this->registry);

					$this->load->model('extension/marketing/' . $marketing);
					$this->{'model_extension_marketing_' . $marketing}->updateCustomer($this->request->get['customer_id']);
				}
			}

			$this->session->data['success'] = $this->language->get('success_delete');

			$this->response->redirect($this->url->link('customer/account', $this->getUrl(), true));
		}
	}

	public function reactivate() {
		$this->load->language('customer/account');

		if ($this->validateReactivate()) {
			$this->load->model('customer/account');
			$this->model_customer_account->reactivateAccount($this->request->get['customer_id'], $this->request->get['account_id']);

			// update any enabled marketing extensions
			$this->load->model('extension/extension');
			$marketing_extensions = $this->model_extension_extension->getInstalled('marketing');

			foreach ($marketing_extensions as $marketing) {
				if ($this->config->get($marketing . '_status')) {
					$this->{$marketing} = new $marketing($this->registry);

					$this->load->model('extension/marketing/' . $marketing);
					$this->{'model_extension_marketing_' . $marketing}->updateCustomer($this->request->get['customer_id']);
				}
			}

			$this->session->data['success'] = $this->language->get('success_reactivate');

			$this->response->redirect($this->url->link('customer/account/edit', $this->getUrl() . '&account_id=' . $this->request->get['account_id'], true));
		}
	}

	public function export() {
		$this->load->language('customer/account');

		if ($this->validateExport()) {
			$this->load->model('customer/account');

			$limit = 5000;
			$page  = 0;

			$export = new Export('csv', 'accounts-export');

			do {
				$results = $this->model_customer_account->getAccounts(array(
					'filter_network_id'         => (isset($this->request->get['filter_network_id']) ? $this->request->get['filter_network_id'] : null),
					'filter_username'           => (isset($this->request->get['filter_username']) ? $this->request->get['filter_username'] : null),
					'filter_type'               => (isset($this->request->get['filter_type']) ? $this->request->get['filter_type'] : null),
					'filter_customer_id'        => (isset($this->request->get['filter_customer_id']) ? $this->request->get['filter_customer_id'] : null),
					'filter_customer'           => (isset($this->request->get['filter_customer']) ? $this->request->get['filter_customer'] : null),
					'filter_email'              => (isset($this->request->get['filter_email']) ? $this->request->get['filter_email'] : null),
					'filter_status'             => (isset($this->request->get['filter_status']) ? $this->request->get['filter_status'] : null),
					'filter_deleted'            => (isset($this->request->get['filter_deleted']) ? $this->request->get['filter_deleted'] : null),
					'filter_date_expires_start' => (isset($this->request->get['filter_date_expires_start']) ? $this->request->get['filter_date_expires_start'] : null),
					'filter_date_expires_end'   => (isset($this->request->get['filter_date_expires_end']) ? $this->request->get['filter_date_expires_end'] : null),
					'filter_date_added_start'   => (isset($this->request->get['filter_date_added_start']) ? $this->request->get['filter_date_added_start'] : null),
					'filter_date_added_end'     => (isset($this->request->get['filter_date_added_end']) ? $this->request->get['filter_date_added_end'] : null),
					'sort'                      => (isset($this->request->get['sort']) ? $this->request->get['sort'] : 'a.username'),
					'order'                     => (isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC'),
					'start'                     => $page * $limit,
					'limit'                     => $limit
				));

				if ($results) {
					foreach ($results as $result) {
						$data = array(
							'customer'     => $result['customer'],
							'account_id'   => $result['account_id'],
							'network_id'   => $result['network_id'],
							'username'     => $result['username'],
							'type'         => ucwords($result['type']),
							'status'       => $result['status'],
							'deleted'      => ($result['deleted'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
							'date_expires' => (($result['date_expires'] != '0000-00-00 00:00:00') ? date($this->language->get('datetime_format'), strtotime($result['date_expires'])) : ''),
							'date_added'   => date($this->language->get('datetime_format'), strtotime($result['date_added']))
						);

						if (!$export->hasHeader()) {
							$export->addHeader(array_keys($data));
						}

						$export->write($data);
					}
				}

				$page++;
			} while (count($results) == $limit);

			$export->close();
		}

		$this->response->redirect($this->url->link('customer/account', $this->getUrl(), true));
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['filter_network_id'])) {
			$filter_network_id = trim($this->request->get['filter_network_id']);
		} else {
			$filter_network_id = null;
		}

		if (isset($this->request->get['filter_username'])) {
			$filter_username = trim($this->request->get['filter_username']);
		} else {
			$filter_username = null;
		}

		if (isset($this->request->get['filter_type'])) {
			$filter_type = trim($this->request->get['filter_type']);
		} else {
			$filter_type = null;
		}

		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = trim($this->request->get['filter_customer_id']);
		} else {
			$filter_customer_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = trim($this->request->get['filter_customer']);
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = trim($this->request->get['filter_email']);
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = trim($this->request->get['filter_status']);
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_deleted'])) {
			$filter_deleted = trim($this->request->get['filter_deleted']);
		} else {
			$filter_deleted = null;
		}

		if (isset($this->request->get['filter_date_expires_start'])) {
			$filter_date_expires_start = trim($this->request->get['filter_date_expires_start']);
		} else {
			$filter_date_expires_start = null;
		}

		if (isset($this->request->get['filter_date_expires_end'])) {
			$filter_date_expires_end = trim($this->request->get['filter_date_expires_end']);
		} else {
			$filter_date_expires_end = null;
		}

		if (isset($this->request->get['filter_date_added_start'])) {
			$filter_date_added_start = trim($this->request->get['filter_date_added_start']);
		} else {
			$filter_date_added_start = null;
		}

		if (isset($this->request->get['filter_date_added_end'])) {
			$filter_date_added_end = trim($this->request->get['filter_date_added_end']);
		} else {
			$filter_date_added_end = null;
		}


		if (isset($this->request->get['sort'])) {
			$sort = trim($this->request->get['sort']);
		} else {
			$sort = 'a.username';
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
			'href' => $this->url->link('customer/account', $url, true)
		);

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

		$data['token'] = $this->session->data['token'];

		$data['accounts'] = array();

		$filter_data = array(
			'filter_network_id'         => $filter_network_id,
			'filter_username'           => $filter_username,
			'filter_type'               => $filter_type,
			'filter_customer_id'        => $filter_customer_id,
			'filter_customer'           => $filter_customer,
			'filter_email'              => $filter_email,
			'filter_status'             => $filter_status,
			'filter_deleted'            => $filter_deleted,
			'filter_date_expires_start' => $filter_date_expires_start,
			'filter_date_expires_end'   => $filter_date_expires_end,
			'filter_date_added_start'   => $filter_date_added_start,
			'filter_date_added_end'     => $filter_date_added_end,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$account_total = $this->model_customer_account->getTotalAccounts($filter_data);

		$account_data = $this->model_customer_account->getAccounts($filter_data);

		foreach ($account_data as $account) {
			$data['accounts'][] = array(
				'username'      => $account['username'],
				'type'          => ucwords($account['type']),
				'customer'      => $account['customer'],
				'customer_id'   => $account['customer_id'],
				'email'         => $account['email'],
				'status'        => ucwords($account['status']),
				'deleted'       => ($account['deleted']) ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'date_expires'  => (($account['date_expires'] != '0000-00-00 00:00:00') ? date($this->language->get('datetime_format'), strtotime($account['date_expires'])) : ''),
				'date_added'    => date($this->language->get('datetime_format'), strtotime($account['date_added'])),
				'edit'          => $this->url->link('customer/account/edit', $url . '&account_id=' . $account['account_id'], true),
				'href_customer' => $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $account['customer_id'], true)
			);
		}

		$data['account_types'] = array(
			'*'         => $this->language->get('text_all_types'),
			'instagram' => $this->language->get('text_instagram')
		);

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_account_id']   = $this->url->link('customer/account', $url . '&sort=a.account_id', true);
		$data['sort_username']     = $this->url->link('customer/account', $url . '&sort=a.username', true);
		$data['sort_type']         = $this->url->link('customer/account', $url . '&sort=a.type', true);
		$data['sort_customer']     = $this->url->link('customer/account', $url . '&sort=customer', true);
		$data['sort_email']   	   = $this->url->link('customer/account', $url . '&sort=c.email', true);
		$data['sort_status']       = $this->url->link('customer/account', $url . '&sort=a.status', true);
		$data['sort_deleted']      = $this->url->link('customer/account', $url . '&sort=a.deleted', true);
		$data['sort_date_expires'] = $this->url->link('customer/account', $url . '&sort=a.date_expires', true);
		$data['sort_date_added']   = $this->url->link('customer/account', $url . '&sort=a.date_added', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $account_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('customer/account', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($account_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($account_total - $this->config->get('config_limit_admin'))) ? $account_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $account_total, ceil($account_total / $this->config->get('config_limit_admin')));

		$data['filter_network_id']         = $filter_network_id;
		$data['filter_username']           = $filter_username;
		$data['filter_type']               = $filter_type;
		$data['filter_customer']           = $filter_customer;
		$data['filter_email']              = $filter_email;
		$data['filter_status']             = $filter_status;
		$data['filter_deleted']            = $filter_deleted;
		$data['filter_date_expires_start'] = $filter_date_expires_start;
		$data['filter_date_expires_end']   = $filter_date_expires_end;
		$data['filter_date_added_start']   = $filter_date_added_start;
		$data['filter_date_added_end']     = $filter_date_added_end;

		$data['sort']  = $sort;
		$data['order'] = $order;

		if ($this->user->hasPermission('access', 'common/export')) {
			$data['export'] = $this->url->link('customer/account/export', $url, true);
		} else {
			$data['export'] = '';
		}

		$data['statuses'] = array();

		$statuses  = $this->model_customer_account->getStatuses();

		if ($statuses) {
			foreach($statuses as $status){
				$data['statuses'][] = ucwords($status['status']);
			}
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/account_list', $data));
	}

	protected function getForm() {
		$url = $this->getUrl();

		if (isset($this->request->get['account_id'])) {
			$account_info = $this->model_customer_account->getAccount($this->request->get['account_id']);
		} else {
			$this->response->redirect($this->url->link('customer/account', $url, true));
		}

		$data = $this->language->all();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('customer/account', $url, true)
		);

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

		if (isset($this->error['date_expires'])) {
			$data['error_date_expires'] = $this->error['date_expires'];
		} else {
			$data['error_date_expires'] = '';
		}

		$data['token'] = $this->session->data['token'];

		$data['action'] = $this->url->link('customer/account/edit', $url . '&account_id=' . $this->request->get['account_id'], true);
		$data['cancel'] = $this->url->link('customer/account', $url, true);
		$data['delete'] = $this->url->link('customer/account/delete', $url . '&customer_id=' . $account_info->CustomerId . '&account_id=' . $this->request->get['account_id'], true);

		$data['customer_id'] = $account_info->CustomerId;
		$data['account_id']  = $account_info->Id;
		$data['network_id']  = $account_info->NetworkId;
		$data['username']    = $account_info->Username;
		$data['deleted']     = $account_info->Deleted;
		$data['type']        = ucwords($account_info->Type);
		$data['customer']    = $account_info->Customer;

		$data['info_deleted'] = sprintf($this->language->get('text_info_deleted'), $this->url->link('customer/account/reactivate', $url . '&customer_id=' . $account_info->CustomerId . '&account_id=' . $this->request->get['account_id'], true));
		
		if ($account_info->ExpiresDateTime) {
			$data['date_expires'] = date($this->language->get('datetime_format'), strtotime($account_info->ExpiresDateTime));
		} else {
			$data['date_expires'] = '';
		}

		$data['date_added'] = date($this->language->get('datetime_format'), strtotime($account_info->AddedDateTime));

		$data['href_customer'] = $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $account_info->CustomerId, true);

		$this->document->addStyle('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addScript('view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/account_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'customer/account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!empty($this->request->post['date_expires']) && strtotime($this->request->post['date_expires']) === false) {
			$this->error['date_expires'] = $this->language->get('error_date_expires');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'customer/account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->get['customer_id'])) {
			$this->error['customer_id'] = $this->language->get('error_customer_id');
		}

		if (empty($this->request->get['account_id'])) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateReactivate() {
		if (!$this->user->hasPermission('modify', 'customer/account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->get['customer_id'])) {
			$this->error['customer_id'] = $this->language->get('error_customer_id');
		}

		if (empty($this->request->get['account_id'])) {
			$this->error['account_id'] = $this->language->get('error_account_id');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateExport() {
		if (!$this->user->hasPermission('access', 'common/export')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_network_id']) && !in_array('network_id', $blacklist)) {
			$url_data['filter_network_id'] = html_entity_decode($this->request->get['filter_network_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_username']) && !in_array('username', $blacklist)) {
			$url_data['filter_username'] = html_entity_decode($this->request->get['filter_username'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_type']) && !in_array('type', $blacklist)) {
			$url_data['filter_type'] = html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_customer_id']) && !in_array('customer_id', $blacklist)) {
			$url_data['filter_customer_id'] = html_entity_decode($this->request->get['filter_customer_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_customer']) && !in_array('customer', $blacklist)) {
			$url_data['filter_customer'] = html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_email']) && !in_array('email', $blacklist)) {
			$url_data['filter_email'] = html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_status']) && !in_array('status', $blacklist)) {
			$url_data['filter_status'] = $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_deleted']) && !in_array('deleted', $blacklist)) {
			$url_data['filter_deleted'] = $this->request->get['filter_deleted'];
		}

		if (isset($this->request->get['filter_date_expires_start']) && !in_array('date_expires_start', $blacklist)) {
			$url_data['filter_date_expires_start'] = $this->request->get['filter_date_expires_start'];
		}

		if (isset($this->request->get['filter_date_expires_end']) && !in_array('date_expires_end', $blacklist)) {
			$url_data['filter_date_expires_end'] = $this->request->get['filter_date_expires_end'];
		}

		if (isset($this->request->get['filter_date_added_start']) && !in_array('date_added_start', $blacklist)) {
			$url_data['filter_date_added_start'] = $this->request->get['filter_date_added_start'];
		}

		if (isset($this->request->get['filter_date_added_end']) && !in_array('date_added_end', $blacklist)) {
			$url_data['filter_date_added_end'] = $this->request->get['filter_date_added_end'];
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
}