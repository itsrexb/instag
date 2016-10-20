<?php
class ControllerDashboardCustomer extends Controller {
	public function index() {
		$data = $this->load->language('dashboard/customer');

		$data['token'] = $this->session->data['token'];

		// Total Customers
		$this->load->model('customer/customer');

		$customer_total = $this->model_customer_customer->getTotalCustomers();

		if ($customer_total > 1000000000000) {
			$data['total'] = round($customer_total / 1000000000000, 1) . 'T';
		} elseif ($customer_total > 1000000000) {
			$data['total'] = round($customer_total / 1000000000, 1) . 'B';
		} elseif ($customer_total > 1000000) {
			$data['total'] = round($customer_total / 1000000, 1) . 'M';
		} elseif ($customer_total > 1000) {
			$data['total'] = round($customer_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $customer_total;
		}

		$data['customer'] = $this->url->link('customer/customer', 'token=' . $this->session->data['token'], true);

		return $this->load->view('dashboard/customer', $data);
	}
}