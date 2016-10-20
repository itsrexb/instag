<?php
class ControllerCatalogAccountTip extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/account_tip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/account_tip');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/account_tip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/account_tip');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_account_tip->addAccountTip($this->request->post);

			$this->session->data['success'] = $this->language->get('success_add');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('catalog/account_tip', $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/account_tip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/account_tip');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_account_tip->editAccountTip($this->request->get['account_tip_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('success_edit');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('catalog/account_tip', $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/account_tip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/account_tip');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $account_tip_id) {
				$this->model_catalog_account_tip->deleteAccountTip($account_tip_id);
			}

			$this->session->data['success'] = $this->language->get('success_delete');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('catalog/account_tip', $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'atd.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/account_tip', $url, true)
		);

		$data['add']    = $this->url->link('catalog/account_tip/add', $url, true);
		$data['delete'] = $this->url->link('catalog/account_tip/delete', $url, true);

		$data['account_tips'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$account_tip_total = $this->model_catalog_account_tip->getTotalAccountTips();

		$results = $this->model_catalog_account_tip->getAccountTips($filter_data);

		foreach ($results as $result) {
			$data['account_tips'][] = array(
				'account_tip_id' => $result['account_tip_id'],
				'title'          => $result['title'],
				'edit'           => $this->url->link('catalog/account_tip/edit', $url . '&account_tip_id=' . $result['account_tip_id'], true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_title'] = $this->url->link('catalog/account_tip', $url . '&sort=atd.title', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $account_tip_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('catalog/account_tip', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($account_tip_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($account_tip_total - $this->config->get('config_limit_admin'))) ? $account_tip_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $account_tip_total, ceil($account_tip_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/account_tip_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['account_tip_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/account_tip', $url, true)
		);

		if (!isset($this->request->get['account_tip_id'])) {
			$data['action'] = $this->url->link('catalog/account_tip/add', $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/account_tip/edit', $url . '&account_tip_id=' . $this->request->get['account_tip_id'], true);
		}

		$data['cancel'] = $this->url->link('catalog/account_tip', $url, true);

		if (isset($this->request->get['account_tip_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$account_tip_info = $this->model_catalog_account_tip->getAccountTip($this->request->get['account_tip_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['account_tip_description'])) {
			$data['account_tip_description'] = $this->request->post['account_tip_description'];
		} elseif (isset($this->request->get['account_tip_id'])) {
			$data['account_tip_description'] = $this->model_catalog_account_tip->getAccountTipDescriptions($this->request->get['account_tip_id']);
		} else {
			$data['account_tip_description'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($account_tip_info)) {
			$data['status'] = $account_tip_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/account_tip_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/account_tip')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['account_tip_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/account_tip')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['sort']) && !in_array('sort', $blacklist)) {
			$url_data['sort'] = $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && !in_array('order', $blacklist)) {
			$url_data['order'] = $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && !in_array('page', $blacklist)) {
			$url_data['page'] = $this->request->get['page'];
		}

		return http_build_query($url_data);
	}
}