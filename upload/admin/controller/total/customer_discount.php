<?php
class ControllerTotalCustomerDiscount extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('total/customer_discount');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('customer_discount', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_total'),
			'href' => $this->url->link('extension/total', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('total/customer_discount', 'token=' . $this->session->data['token'], true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('total/customer_discount', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['customer_discount_status'])) {
			$data['customer_discount_status'] = $this->request->post['customer_discount_status'];
		} else {
			$data['customer_discount_status'] = $this->config->get('customer_discount_status');
		}

		if (isset($this->request->post['customer_discount_sort_order'])) {
			$data['customer_discount_sort_order'] = $this->request->post['customer_discount_sort_order'];
		} else {
			$data['customer_discount_sort_order'] = $this->config->get('customer_discount_sort_order');
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/customer_discount', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/customer_discount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}