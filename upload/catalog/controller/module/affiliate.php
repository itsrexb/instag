<?php
class ControllerModuleAffiliate extends Controller {
	public function index() {
		$data = $this->load->language('module/affiliate');

		$data['logged']      = $this->affiliate->isLogged();
		$data['register']    = $this->url->link('affiliate/register', '', true);
		$data['login']       = $this->url->link('affiliate/login', '', true);
		$data['logout']      = $this->url->link('affiliate/logout', '', true);
		$data['forgotten']   = $this->url->link('affiliate/forgotten', '', true);
		$data['account']     = $this->url->link('affiliate/account', '', true);
		$data['edit']        = $this->url->link('affiliate/edit', '', true);
		$data['password']    = $this->url->link('affiliate/password', '', true);
		$data['payment']     = $this->url->link('affiliate/payment', '', true);
		$data['tracking']    = $this->url->link('affiliate/tracking', '', true);
		$data['transaction'] = $this->url->link('affiliate/transaction', '', true);

		return $this->load->view('module/affiliate', $data);
	}
}