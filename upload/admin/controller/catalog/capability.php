<?php
class ControllerCatalogCapability extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/capability');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/capability');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/capability');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/capability');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_capability->addCapability($this->request->post);

			$this->session->data['success'] = $this->language->get('success_add');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('catalog/capability', $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/capability');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/capability');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_capability->editCapability($this->request->get['capability_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('success_edit');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('catalog/capability', $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/capability');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/capability');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $capability_id) {
				$this->model_catalog_capability->deleteCapability($capability_id);
			}

			$this->session->data['success'] = $this->language->get('success_delete');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('catalog/capability', $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
			'href' => $this->url->link('catalog/capability', $url, true)
		);

		$data['add']    = $this->url->link('catalog/capability/add', $url, true);
		$data['delete'] = $this->url->link('catalog/capability/delete', $url, true);

		$data['capabilities'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$capability_total = $this->model_catalog_capability->getTotalCapabilities();

		$results = $this->model_catalog_capability->getCapabilities($filter_data);

		foreach ($results as $result) {
			$data['capabilities'][] = array(
				'capability_id'   => $result['capability_id'],
				'name'            => $result['name'],
				'edit'            => $this->url->link('catalog/capability/edit', $url . '&capability_id=' . $result['capability_id'], true)
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

		$data['sort_name'] = $this->url->link('catalog/capability', $url . '&sort=name', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $capability_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('catalog/capability', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($capability_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($capability_total - $this->config->get('config_limit_admin'))) ? $capability_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $capability_total, ceil($capability_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/capability_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['capability_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/capability', $url, true)
		);

		if (!isset($this->request->get['capability_id'])) {
			$data['action'] = $this->url->link('catalog/capability/add', $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/capability/edit', $url . '&capability_id=' . $this->request->get['capability_id'], true);
		}

		$data['cancel'] = $this->url->link('catalog/capability', $url, true);

		if (isset($this->request->get['capability_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$capability_info = $this->model_catalog_capability->getCapability($this->request->get['capability_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else if (isset($capability_info)) {
			$data['name'] = $capability_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['free_trial'])) {
			$data['free_trial'] = $this->request->post['free_trial'];
		} else if (isset($capability_info)) {
			$data['free_trial'] = $capability_info['free_trial'];
		} else {
			$data['free_trial'] = 0;
		}

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/capability_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/capability')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/capability')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $capability_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByCapabilityId($capability_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
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

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/capability');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->capability->getCapabilities($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'capability_id' => $result['capability_id'],
					'name'          => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}