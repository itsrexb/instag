<?php
class ControllerAffiliateCustomer extends Controller {
	public function index() {
		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/customer', '', true);

			$this->response->redirect($this->url->link('affiliate/login', '', true));
		}

		$data = $this->load->language('affiliate/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('affiliate/transaction');

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

		if (isset($this->request->get['filter_active'])) {
			$filter_active = $this->request->get['filter_active'];
		} else {
			$filter_active = null;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$customers_filter_data = array(
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'filter_active'     => $filter_active,
			'sort'              => 'c.date_affiliate',
			'order'             => 'DESC',
			'start'             => ($page - 1) * $this->config->get('config_product_limit'),
			'limit'             => $this->config->get('config_product_limit')
		);

		$data['customers'] = array();

		$this->load->model('affiliate/customer');

		$total_customers = $this->model_affiliate_customer->getTotalCustomers($customers_filter_data);

		$customer_data = $this->model_affiliate_customer->getCustomers($customers_filter_data);

		$affiliate_timezone = new DateTimeZone($this->affiliate->getTimeZone());

		foreach ($customer_data as $customer) {
			if ($customer['date_affiliate'] != '0000-00-00 00:00:00') {
				$date_added = new DateTime($customer['date_affiliate']);
			} else {
				$date_added = new DateTime($customer['date_added']);
			}

			$date_added->setTimezone($affiliate_timezone);

			$data['customers'][] = array(
				'customer_id'       => $customer['customer_id'],
				'name'              => $customer['name'],
				'total_commissions' => $this->currency->format($customer['total_commission']),
				'active'            => ($customer['active'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
				'date_added'        => $date_added->format($this->language->get('date_format_short')),
				'href'              => $this->url->link('affiliate/customer/info', '&customer_id=' . $customer['customer_id'], true)
			);
		}

		$data['filter_active']     = $filter_active;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end']   = $filter_date_end;

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $total_customers;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_product_limit');

		if ($url) {
			$pagination->url = $this->url->link('affiliate/customer', $url . '&page={page}', true);
		} else {
			$pagination->url = $this->url->link('affiliate/customer', 'page={page}', true);
		}

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total_customers) ? (($page - 1) * $this->config->get('config_product_limit')) + 1 : 0, ((($page - 1) * $this->config->get('config_product_limit')) > ($total_customers - $this->config->get('config_product_limit'))) ? $total_customers : ((($page - 1) * $this->config->get('config_product_limit')) + $this->config->get('config_product_limit')), $total_customers, ceil($total_customers / $this->config->get('config_product_limit')));

		$data['dashboard'] = $this->url->link('affiliate/dashboard', '', true);

		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->document->addStyle('catalog/view/javascript/chosen/chosen.bootstrap.min.css');
		$this->document->addScript('catalog/view/javascript/chosen/chosen.jquery.min.js', 'footer');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js', 'footer');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js', 'footer');

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/customer_list', $data));
	}

	public function info() {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} else {
			$customer_id = null;
		}

		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/dashboard', 'customer_id=' . $customer_id, true);

			$this->response->redirect($this->url->link('affiliate/login', '', true));
		}

		// try to load customer, if not able to redirect to customer listing
		if ($customer_id) {
			$this->load->model('affiliate/customer');

			$customer_info = $this->model_affiliate_customer->getCustomer($customer_id);
		} else {
			$customer_info = array();
		}

		if ($customer_info) {
			$data = $this->load->language('affiliate/customer');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['href_customers'] = $this->url->link('affiliate/customer', '', true);

			$affiliate_timezone = new DateTimeZone($this->affiliate->getTimeZone());

			if ($customer_info['date_affiliate'] != '0000-00-00 00:00:00') {
				$date_added = new DateTime($customer_info['date_affiliate']);
			} else {
				$date_added = new DateTime($customer_info['date_added']);
			}

			$date_added->setTimezone($affiliate_timezone);

			$data['name']              = $customer_info['name'];
			$data['email']             = $customer_info['email'];
			$data['telephone']         = $customer_info['telephone'];
			$data['active']            = ($customer_info['active'] ? $this->language->get('text_yes') : $this->language->get('text_no'));
			$data['date_added']        = $date_added->format($this->language->get('date_format_long'));
			$data['total_commissions'] = $this->currency->format($customer_info['total_commission']);

			$data['commissions'] = array();

			$this->load->model('affiliate/transaction');
			$commissions = $this->model_affiliate_transaction->getTransactions(array(
				'filter_customer_id' => $customer_id
			));

			foreach ($commissions as $commission) {
				$date_added = new DateTime($commission['date_added']);

				$date_added->setTimezone($affiliate_timezone);

				$data['commissions'][] = array(
					'date_added'  => $date_added->format($this->language->get('date_format_long')),
					'description' => $commission['description'],
					'amount'      => $this->currency->format($commission['amount'])
				);
			}
		} else {
			$this->response->redirect($this->url->link('affiliate/customer', '', true));
		}

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('affiliate/customer_info', $data));
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		if (isset($this->request->get['filter_date_start']) && !in_array('date_start', $blacklist)) {
			$url_data['filter_date_start'] = html_entity_decode($this->request->get['filter_date_start'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_date_end']) && !in_array('date_end', $blacklist)) {
			$url_data['filter_date_end'] = html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_active']) && !in_array('active', $blacklist)) {
			$url_data['filter_active'] = html_entity_decode($this->request->get['filter_active'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['page']) && !in_array('page', $blacklist)) {
			$url_data['page'] = $this->request->get['page'];
		}

		return http_build_query($url_data);
	}
}