<?php
class ControllerAffiliateAffiliateGroup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('affiliate/affiliate_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate_group');

		$this->getList();
	}

	public function add() {
		$this->load->language('affiliate/affiliate_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_affiliate_affiliate_group->addAffiliateGroup($this->request->post);

			$this->session->data['success'] = $this->language->get('success_insert');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate_group', $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('affiliate/affiliate_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_affiliate_affiliate_group->editAffiliateGroup($this->request->get['affiliate_group_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('success_update');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate_group', $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('affiliate/affiliate_group');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/affiliate_group');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $affiliate_group_id) {
				$this->model_affiliate_affiliate_group->deleteAffiliateGroup($affiliate_group_id);
			}

			$this->session->data['success'] = sprintf($this->language->get('success_delete'), count($this->request->post['selected']));

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('affiliate/affiliate_group', $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$data = $this->language->all();

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'agd.name';
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
			'href' => $this->url->link('affiliate/affiliate_group', $url, true)
		);

		$data['add'] = $this->url->link('affiliate/affiliate_group/add', $url, true);
		$data['delete'] = $this->url->link('affiliate/affiliate_group/delete', $url, true);

		$data['affiliate_groups'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$affiliate_group_total = $this->model_affiliate_affiliate_group->getTotalAffiliateGroups();

		$results = $this->model_affiliate_affiliate_group->getAffiliateGroups($filter_data);

		foreach ($results as $result) {
			$data['affiliate_groups'][] = array(
				'affiliate_group_id' => $result['affiliate_group_id'],
				'name'               => $result['name'] . (($result['affiliate_group_id'] == $this->config->get('config_affiliate_group_id')) ? $this->language->get('text_default') : null),
				'commission'         => (float)$result['commission'] . '%',
				'edit'               => $this->url->link('affiliate/affiliate_group/edit', $url . '&affiliate_group_id=' . $result['affiliate_group_id'], true)
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

		$data['sort_name']       = $this->url->link('affiliate/affiliate_group', $url . '&sort=agd.name', true);
		$data['sort_commission'] = $this->url->link('affiliate/affiliate_group', $url . '&sort=commission', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $affiliate_group_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('affiliate/affiliate_group', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($affiliate_group_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($affiliate_group_total - $this->config->get('config_limit_admin'))) ? $affiliate_group_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $affiliate_group_total, ceil($affiliate_group_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/affiliate_group_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['affiliate_group_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('affiliate/affiliate_group', $url, true)
		);

		if (!isset($this->request->get['affiliate_group_id'])) {
			$data['action'] = $this->url->link('affiliate/affiliate_group/add', $url, true);
		} else {
			$data['action'] = $this->url->link('affiliate/affiliate_group/edit', $url . '&affiliate_group_id=' . $this->request->get['affiliate_group_id'], true);
		}

		$data['cancel'] = $this->url->link('affiliate/affiliate_group', $url, true);

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['affiliate_group_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$affiliate_group_info = $this->model_affiliate_affiliate_group->getAffiliateGroup($this->request->get['affiliate_group_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['affiliate_group_descriptions'])) {
			$data['affiliate_group_descriptions'] = $this->request->post['affiliate_group_descriptions'];
		} elseif (isset($this->request->get['affiliate_group_id'])) {
			$data['affiliate_group_descriptions'] = $this->model_affiliate_affiliate_group->getAffiliateGroupDescriptions($this->request->get['affiliate_group_id']);
		} else {
			$data['affiliate_group_descriptions'] = array();
		}

		if (isset($this->request->post['affiliate_group_commissions'])) {
			$data['affiliate_group_commissions'] = $this->request->post['affiliate_group_commissions'];
		} elseif (isset($this->request->get['affiliate_group_id'])) {
			$data['affiliate_group_commissions'] = $this->model_affiliate_affiliate_group->getAffiliateGroupCommissions($this->request->get['affiliate_group_id']);
		} else {
			$data['affiliate_group_commissions'] = array(array($this->config->get('config_affiliate_commission')));
		}

		$data['affiliate_groups'] = $this->model_affiliate_affiliate_group->getAffiliateGroups();

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/affiliate_group_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'affiliate/affiliate_group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['affiliate_group_descriptions'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'affiliate/affiliate_group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');
		$this->load->model('affiliate/affiliate');

		foreach ($this->request->post['selected'] as $affiliate_group_id) {
			if ($this->config->get('config_affiliate_group_id') == $affiliate_group_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}

			$affiliate_total = $this->model_affiliate_affiliate->getTotalAffiliatesByAffiliateGroupId($affiliate_group_id);

			if ($affiliate_total) {
				$this->error['warning'] = sprintf($this->language->get('error_affiliate'), $affiliate_total);
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
}