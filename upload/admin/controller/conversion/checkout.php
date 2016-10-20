<?php
class ControllerConversionCheckout extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('conversion/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('conversion_checkout', $this->request->post);

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
			'href' => $this->url->link('conversion/checkout', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('conversion/checkout', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/conversion', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['conversion_checkout_code'])) {
			$data['conversion_checkout_code'] = $this->request->post['conversion_checkout_code'];
		} else {
			$data['conversion_checkout_code'] = $this->config->get('conversion_checkout_code');
		}

		if (isset($this->request->post['conversion_checkout_status'])) {
			$data['conversion_checkout_status'] = $this->request->post['conversion_checkout_status'];
		} else {
			$data['conversion_checkout_status'] = $this->config->get('conversion_checkout_status');
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('conversion/checkout', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'conversion/checkout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}