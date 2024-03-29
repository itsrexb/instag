<?php
class ControllerAnalyticsGoogleAnalytics extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('analytics/google_analytics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_analytics', $this->request->post);

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
			'href' => $this->url->link('analytics/google_analytics', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('analytics/google_analytics', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/analytics', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['google_analytics_code'])) {
			$data['google_analytics_code'] = $this->request->post['google_analytics_code'];
		} else {
			$data['google_analytics_code'] = $this->config->get('google_analytics_code');
		}

		if (isset($this->request->post['google_analytics_status'])) {
			$data['google_analytics_status'] = $this->request->post['google_analytics_status'];
		} else {
			$data['google_analytics_status'] = $this->config->get('google_analytics_status');
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('analytics/google_analytics', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'analytics/google_analytics')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['google_analytics_code']) {
			$this->error['code'] = $this->language->get('error_code');
		}

		return !$this->error;
	}
}