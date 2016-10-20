<?php
class ControllerConversionCustomer extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('conversion/customer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('conversion_customer', $this->request->post);

			$this->session->data['success'] = $this->language->get('success_update');

			$this->response->redirect($this->url->link('extension/conversion', 'token=' . $this->session->data['token'], true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_conversion'),
			'href' => $this->url->link('extension/conversion', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('conversion/customer', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('conversion/customer', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/conversion', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['conversion_customer_code'])) {
			$data['conversion_customer_code'] = $this->request->post['conversion_customer_code'];
		} else {
			$data['conversion_customer_code'] = $this->config->get('conversion_customer_code');
		}

		if (isset($this->request->post['conversion_customer_status'])) {
			$data['conversion_customer_status'] = $this->request->post['conversion_customer_status'];
		} else {
			$data['conversion_customer_status'] = $this->config->get('conversion_customer_status');
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('conversion/customer', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'conversion/customer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}