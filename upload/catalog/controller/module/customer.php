<?php
class ControllerModuleCustomer extends Controller {
	public function index() {
		$data = $this->load->language('module/customer');

		$data['dashboard']   = $this->url->link('account/dashboard', '', true);
		$data['forgotten']   = $this->url->link('customer/forgotten', '', true);
		$data['login']       = $this->url->link('customer/login', '', true);
		$data['logout']      = $this->url->link('customer/logout', '', true);
		$data['profile']     = $this->url->link('customer/profile', '', true);
		$data['register']    = $this->url->link('customer/register', '', true);
		$data['transaction'] = $this->url->link('customer/transaction', '', true);

		if ($this->config->get('reward_status')) {
			$data['reward'] = $this->url->link('customer/reward', '', true);
		} else {
			$data['reward'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		return $this->load->view('module/customer', $data);
	}
}