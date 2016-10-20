<?php
class ControllerSettingUrlAlias extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('setting/url_alias');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/url_alias');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_url_alias->updateUrlAliases((isset($this->request->post['url_alias']) ? $this->request->post['url_alias'] : array()));

			$this->session->data['success'] = $this->language->get('success_update');

			$this->response->redirect($this->url->link('setting/url_alias', 'token=' . $this->session->data['token'], true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('setting/url_alias', 'token=' . $this->session->data['token'], true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['button_add_url_alias'] = $this->language->get('button_add_url_alias');
		$data['button_cancel']        = $this->language->get('button_cancel');
		$data['button_remove']        = $this->language->get('button_remove');
		$data['button_save']          = $this->language->get('button_save');

		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_route']   = $this->language->get('entry_route');

		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['route'])) {
			$data['error_route'] = $this->error['route'];
		} else {
			$data['error_route'] = array();
		}

		$data['action'] = $this->url->link('setting/url_alias', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('setting/url_alias', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->post['url_alias'])) {
			$data['url_aliases'] = $this->request->post['url_alias'];
		} else {
			$data['url_aliases'] = $this->model_setting_url_alias->getUrlAliases();
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('setting/url_alias.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'setting/url_alias')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['url_alias'])) {
			foreach ($this->request->post['url_alias'] as $key => $value) {
				if (!$value['route']) {
					$this->error['route'][$key] = $this->language->get('error_route');
				}
			}
		}

		return !$this->error;
	}
}