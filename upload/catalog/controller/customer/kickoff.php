<?php 
class ControllerCustomerKickoff extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/dashboard', '', true);

			$this->response->redirect($this->url->link('customer/login'));
		}

		$this->load->model('account/account');

		$instagram_accounts = $this->model_account_account->getAccounts('instagram');

		// if this customer has accounts, redirect to the dashboard
		if ($instagram_accounts) {
			$this->response->redirect($this->url->link('account/dashboard'));
		}

		$this->load->language('customer/kickoff');

		$data = $this->language->all();

		$this->document->setTitle($this->language->get('heading_title'));

		$data['link_logout']    = $this->url->link('customer/logout', '', true);
		$data['link_profile']   = $this->url->link('customer/profile', '', true);

		$data['url_instagram_insert'] = $this->url->link('account/instagram/insert', '', true);

		$data['conversions'] = array();

		// only show conversions for brand new customers
		if (!empty($this->session->data['new_customer'])) {
			if ($this->config->get('conversion_customer_status')) {
				$data['conversions'][] = $this->load->controller('conversion/customer');
			}

			unset($this->session->data['new_customer']);
		}

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/kickoff', $data));
	}
}