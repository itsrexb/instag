<?php
class ControllerDashboardAccount extends Controller {
	public function index() {
		$data = $this->load->language('dashboard/account');

		$data['token'] = $this->session->data['token'];

		// Total Accounts
		$this->load->model('customer/account');

		$account_total = $this->model_customer_account->getTotalAccounts();

		if ($account_total > 1000000000000) {
			$data['total'] = round($account_total / 1000000000000, 1) . 'T';
		} elseif ($account_total > 1000000000) {
			$data['total'] = round($account_total / 1000000000, 1) . 'B';
		} elseif ($account_total > 1000000) {
			$data['total'] = round($account_total / 1000000, 1) . 'M';
		} elseif ($account_total > 1000) {
			$data['total'] = round($account_total / 1000, 1) . 'K';
		} else {
			$data['total'] = round($account_total);
		}

		$data['account'] = $this->url->link('customer/account', 'token=' . $this->session->data['token'], true);

		return $this->load->view('dashboard/account', $data);
	}
}