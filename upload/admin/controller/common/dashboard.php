<?php
class ControllerCommonDashboard extends Controller {
	public function index() {
		$data = $this->load->language('common/dashboard');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['token'] = $this->session->data['token'];

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['order']          = $this->load->controller('dashboard/order');
		$data['sale']           = $this->load->controller('dashboard/sale');
		$data['customer']       = $this->load->controller('dashboard/customer');
		$data['account']        = $this->load->controller('dashboard/account');
		$data['map']            = $this->load->controller('dashboard/map');
		$data['chart']          = $this->load->controller('dashboard/chart');
		$data['chart_customer'] = $this->load->controller('dashboard/chart_customer');
		$data['chart_revenue']  = $this->load->controller('dashboard/chart_revenue');
		$data['activity']       = $this->load->controller('dashboard/activity');
		$data['recent']         = $this->load->controller('dashboard/recent');
		$data['footer']         = $this->load->controller('common/footer');

		// Run currency update
		if ($this->config->get('config_currency_auto')) {
			$this->load->model('localisation/currency');

			$this->model_localisation_currency->refresh();
		}

		$this->response->setOutput($this->load->view('common/dashboard', $data));
	}
}