<?php
class ControllerReportAccountDecline extends Controller {
	public function export() {
		$this->load->language('report/account_decline');

		if ($this->validateExport()) {
			$this->load->model('report/account');

			$limit = 5000;
			$page  = 0;

			$export = new Export('csv', 'account-decline-export');

			do {
				$results = $this->model_report_account->getAccoutDeclines(array(
					'filter_country'         => (isset($this->request->get['filter_country']) ? $this->request->get['filter_country'] : null),
					'filter_recurring_order' => (isset($this->request->get['filter_recurring_order']) ? $this->request->get['filter_recurring_order'] : null),
					'filter_date_start'      => (isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null),
					'filter_date_end'        => (isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null),
					'sort'                   => (isset($this->request->get['sort']) ? $this->request->get['sort'] : 'o.dated_added'),
					'order'                  => (isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC'),
					'start'                  => $page * $limit,
					'limit'                  => $limit
				));

				if ($results) {
					foreach ($results as $result) {
						$data = array(
							'customer'          => $result['customer'],
							'account'           => $result['username'],
							'email'             => $result['customer_email'],
							'telephone'         => $result['customer_telephone'],
							'country'           => $result['customer_country'],
							'recurring_order'   => ($result['recurring_order'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
							'total_spent'       => $this->currency->format($result['total_spent']),
							'date_last_decline' => date($this->language->get('date_format_short'), strtotime($result['date_last_decline']))
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

		$this->response->redirect($this->url->link('customer/customer', $this->getUrl(), true));
	}

	public function index() {
		$data = $this->load->language('report/account_decline');

		if (isset($this->request->get['filter_country'])) {
			$filter_country = $this->request->get['filter_country'];
		} else {
			$filter_country = null;
		}

		if (isset($this->request->get['filter_recurring_order'])) {
			$filter_recurring_order = $this->request->get['filter_recurring_order'];
		} else {
			$filter_recurring_order = null;
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = null;
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = trim($this->request->get['order']);
		} else {
			$order = 'DESC';
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
			'href' => $this->url->link('report/account_decline', $url, true)
		);

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->user->hasPermission('access', 'common/export')) {
			$data['export'] = $this->url->link('report/account_decline/export', $url, true);
		} else {
			$data['export'] = '';
		}

		$data['token'] = $this->session->data['token'];

		$filter_data = array(
			'filter_country'         => $filter_country,
			'filter_recurring_order' => $filter_recurring_order,
			'filter_date_start'      => $filter_date_start,
			'filter_date_end'        => $filter_date_end,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$this->load->model('report/account');

		$account_decline_total = $this->model_report_account->getTotalAccoutDeclines($filter_data);

		$account_decline_data = $this->model_report_account->getAccoutDeclines($filter_data);

		$data['account_declines'] = array();

		foreach ($account_decline_data as $account_decline) {
			$data['account_declines'][] = array(
				'customer'          => $account_decline['customer'],
				'username'          => $account_decline['username'],
				'email'             => $account_decline['customer_email'],
				'telephone'         => $account_decline['customer_telephone'],
				'country'           => $account_decline['customer_country'],
				'recurring_order'   => ($account_decline['recurring_order'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
				'total_spent'       => $this->currency->format($account_decline['total_spent']),
				'date_last_decline' => date($this->language->get('date_format_short'), strtotime($account_decline['date_last_decline'])),
				'href_customer'     => $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $account_decline['customer_id'], true),
				'href_account'      => $this->url->link('customer/account/edit', 'token=' . $this->session->data['token'] . '&account_id=' . $account_decline['account_id'], true),
				'href_order'        => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $account_decline['order_id'], true),
				'href_login'        => $this->url->link('customer/customer/login', 'token=' . $this->session->data['token'] . '&customer_id=' . $account_decline['customer_id'] . '&store_id=0', true)
			);
		}

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_customer']        = $this->url->link('sale/order', $url . '&sort=customer', true);
		$data['sort_username']        = $this->url->link('sale/order', $url . '&sort=a.username', true);
		$data['sort_email']           = $this->url->link('sale/order', $url . '&sort=c.email', true);
		$data['sort_telephone']       = $this->url->link('sale/order', $url . '&sort=c.telephone', true);
		$data['sort_country']         = $this->url->link('sale/order', $url . '&sort=co.name', true);
		$data['sort_recurring_order'] = $this->url->link('sale/order', $url . '&sort=o.recurring_order', true);
		$data['sort_total_spent']     = $this->url->link('sale/order', $url . '&sort=total_spent', true);
		$data['sort_date_added']      = $this->url->link('sale/order', $url . '&sort=o.date_added', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $account_decline_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('report/account_decline', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($account_decline_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($account_decline_total - $this->config->get('config_limit_admin'))) ? $account_decline_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $account_decline_total, ceil($account_decline_total / $this->config->get('config_limit_admin')));

		$data['filter_country']         = $filter_country;
		$data['filter_recurring_order'] = $filter_recurring_order;
		$data['filter_date_start']      = $filter_date_start;
		$data['filter_date_end']        = $filter_date_end;

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/account_decline', $data));
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

		if (isset($this->request->get['filter_country']) && !in_array('country', $blacklist)) {
			$url_data['filter_country'] = html_entity_decode($this->request->get['filter_country'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_date_start']) && !in_array('date_start', $blacklist)) {
			$url_data['filter_date_start'] = $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end']) && !in_array('date_end', $blacklist)) {
			$url_data['filter_date_end'] = $this->request->get['filter_date_end'];
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