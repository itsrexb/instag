<?php
class ControllerAnalyticsFacebook extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('analytics/facebook');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('facebook', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_analytics'),
			'href' => $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('analytics/facebook', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('analytics/facebook', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['facebook_code'])) {
			$data['facebook_code'] = $this->request->post['facebook_code'];
		} else {
			$data['facebook_code'] = $this->config->get('facebook_code');
		}

		if (isset($this->request->post['facebook_status'])) {
			$data['facebook_status'] = $this->request->post['facebook_status'];
		} else {
			$data['facebook_status'] = $this->config->get('facebook_status');
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('analytics/facebook', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'analytics/facebook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['facebook_code']) {
			$this->error['code'] = $this->language->get('error_code');
		}

		return !$this->error;
	}
}