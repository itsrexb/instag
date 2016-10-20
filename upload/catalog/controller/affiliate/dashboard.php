<?php
class ControllerAffiliateDashboard extends Controller {
	public function index() {
		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/dashboard', '', true);

			$this->response->redirect($this->url->link('affiliate/login', '', true));
		}

		$data = $this->load->language('affiliate/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['tracking_link'] = $this->url->link('common/home', 'tracking=' . $this->affiliate->getCode(), true);

		$data['href_customers']   = $this->url->link('affiliate/customer', '', true);
		$data['href_profile']     = $this->url->link('affiliate/profile', '', true);
		$data['href_transaction'] = $this->url->link('affiliate/transaction', '', true);

		$this->load->model('affiliate/transaction');

		$data['balance'] = $this->currency->format($this->model_affiliate_transaction->getBalance());

		$data['customers'] = array();

		$this->load->model('affiliate/customer');
		$customer_data = $this->model_affiliate_customer->getCustomers(array(
			'sort'  => 'c.date_affiliate',
			'order' => 'DESC',
			'limit' => 10
		));

		$affiliate_timezone = new DateTimeZone($this->affiliate->getTimeZone());

		foreach ($customer_data as $customer) {
			if ($customer['date_affiliate'] != '0000-00-00 00:00:00') {
				$date_added = new DateTime($customer['date_affiliate']);
			} else {
				$date_added = new DateTime($customer['date_added']);
			}

			$date_added->setTimezone($affiliate_timezone);

			$data['customers'][] = array(
				'customer_id' => $customer['customer_id'],
				'name'        => $customer['name'],
				'date_added'  => $date_added->format($this->language->get('date_format_short')),
				'href'        => $this->url->link('affiliate/customer/info', '&customer_id=' . $customer['customer_id'], true)
			);
		}

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/dashboard', $data));
	}
}