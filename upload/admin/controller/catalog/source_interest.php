<?php
class ControllerCatalogSourceInterest extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/source_interest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/source_interest');
		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/source_interest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/source_interest');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_source_interest->addSourceInterest($this->request->post);

			$this->session->data['success'] = $this->language->get('success_add');

			$this->response->redirect($this->url->link('catalog/source_interest', $this->getUrl(), true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/source_interest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/source_interest');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_source_interest->editSourceInterest($this->request->get['source_interest_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('success_edit');

			$this->response->redirect($this->url->link('catalog/source_interest', $this->getUrl(), true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/source_interest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/source_interest');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $source_interest_id) {
				$this->model_catalog_source_interest->deleteSourceInterest($source_interest_id);
			}

			$this->session->data['success'] = $this->language->get('success_delete');

			$this->response->redirect($this->url->link('catalog/source_interest', $this->getUrl(), true));
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
			'href' => $this->url->link('catalog/source_interest', $url, true)
		);

		$data['add']    = $this->url->link('catalog/source_interest/add', $url, true);
		$data['delete'] = $this->url->link('catalog/source_interest/delete', $url, true);

		$data['source_interests'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$source_interest_total = $this->model_catalog_source_interest->getTotalSourceInterests();

		$results = $this->model_catalog_source_interest->getSourceInterests($filter_data);

		foreach ($results as $result) {
			$total_accounts        = 0;
			$total_accounts_low    = 0;
			$total_accounts_medium = 0;
			$total_accounts_high   = 0;
			$total_tags            = 0;
			$total_tags_low        = 0;
			$total_tags_medium     = 0;
			$total_tags_high       = 0;

			$account_data = $this->model_catalog_source_interest->getSourceInterestAccounts($result['source_interest_id']);

			foreach ($account_data as $accounts) {
				foreach ($accounts as $account) {
					${'total_accounts_' . $account['quality']}++;
					$total_accounts++;
				}
			}

			$tag_data = $this->model_catalog_source_interest->getSourceInterestTags($result['source_interest_id']);

			foreach ($tag_data as $tags) {
				foreach ($tags as $tag) {
					${'total_tags_' . $tag['quality']}++;
					$total_tags++;
				}
			}

			$data['source_interests'][] = array(
				'source_interest_id'    => $result['source_interest_id'],
				'name'                  => $result['name'],
				'total_accounts'        => $total_accounts,
				'total_accounts_low'    => $total_accounts_low,
				'total_accounts_medium' => $total_accounts_medium,
				'total_accounts_high'   => $total_accounts_high,
				'total_tags'            => $total_tags,
				'total_tags_low'        => $total_tags_low,
				'total_tags_medium'     => $total_tags_medium,
				'total_tags_high'       => $total_tags_high,
				'edit'                  => $this->url->link('catalog/source_interest/edit', $url . '&source_interest_id=' . $result['source_interest_id'], true),
				'delete'                => $this->url->link('catalog/source_interest/delete', $url . '&source_interest_id=' . $result['source_interest_id'], true)
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

		$data['sort_name'] = $this->url->link('catalog/source_interest', $url . '&sort=name', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $source_interest_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('catalog/source_interest', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($source_interest_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($source_interest_total - $this->config->get('config_limit_admin'))) ? $source_interest_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $source_interest_total, ceil($source_interest_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/source_interest_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['source_interest_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['account'])) {
			$data['error_account'] = $this->error['account'];
		} else {
			$data['error_account'] = array();
		}

		if (isset($this->error['tag'])) {
			$data['error_tag'] = $this->error['tag'];
		} else {
			$data['error_tag'] = array();
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/source_interest', $url, true)
		);

		if (!isset($this->request->get['source_interest_id'])) {
			$data['action'] = $this->url->link('catalog/source_interest/add', $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/source_interest/edit', $url . '&source_interest_id=' . $this->request->get['source_interest_id'], true);
		}

		$data['cancel'] = $this->url->link('catalog/source_interest', $url, true);

		if (isset($this->request->get['source_interest_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$source_interest_info = $this->model_catalog_source_interest->getSourceInterest($this->request->get['source_interest_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if(isset($this->request->get['source_interest_id'])) {
			$data['source_interest_id'] = $this->request->get['source_interest_id'];
		} else {
			$data['source_interest_id'] = '';
		}

		if (isset($this->request->post['source_interest_description'])) {
			$data['source_interest_description'] = $this->request->post['source_interest_description'];
		} else if (isset($this->request->get['source_interest_id'])) {
			$data['source_interest_description'] = $this->model_catalog_source_interest->getSourceInterestDescriptions($this->request->get['source_interest_id']);
		} else {
			$data['source_interest_description'] = array();
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} else if (!empty($source_interest_info)) {
			$data['path'] = $source_interest_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} else if (!empty($source_interest_info)) {
			$data['parent_id'] = $source_interest_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else if (!empty($source_interest_info)) {
			$data['status'] = $source_interest_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		if (isset($this->request->post['source_interest_excluded_countries'])) {
			$data['source_interest_excluded_countries'] = $this->request->post['source_interest_excluded_countries'];
		} else if (isset($this->request->get['source_interest_id'])) {
			$data['source_interest_excluded_countries'] = $this->model_catalog_source_interest->getSourceInterestExcludedCountries($this->request->get['source_interest_id']);
		} else {
			$data['source_interest_excluded_countries'] = array();
		}

		$data['qualities'] = array(
			'low'    => $this->language->get('text_low'),
			'medium' => $this->language->get('text_medium'),
			'high'   => $this->language->get('text_high')
		);

		if (isset($this->request->post['source_interest_accounts'])) {
			$source_interest_accounts = $this->request->post['source_interest_accounts'];
		} else if (isset($this->request->get['source_interest_id'])) {
			$source_interest_accounts = $this->model_catalog_source_interest->getSourceInterestAccounts($this->request->get['source_interest_id']);
		} else {
			$source_interest_accounts = array();
		}

		if (!isset($source_interest_accounts[0])) {
			$source_interest_accounts[0] = array();
		}

		$data['source_interest_accounts'] = array();

		foreach ($source_interest_accounts as $country_id => $accounts) {
			if ($country_id) {
				$country_info = $this->model_localisation_country->getCountry($country_id);
			} else {
				$country_info = array('name' => $this->language->get('text_default'));
			}

			$data['source_interest_accounts'][] = array(
				'country_id' => $country_id,
				'name'       => $country_info['name'],
				'accounts'   => $accounts
			);
		}

		uasort($data['source_interest_accounts'], array($this, 'account_tag_sort'));

		if (isset($this->request->post['source_interest_tags'])) {
			$source_interest_tags = $this->request->post['source_interest_tags'];
		} else if (isset($this->request->get['source_interest_id'])) {
			$source_interest_tags = $this->model_catalog_source_interest->getSourceInterestTags($this->request->get['source_interest_id']);
		} else {
			$source_interest_tags = array();
		}

		if (!isset($source_interest_tags[0])) {
			$source_interest_tags[0] = array();
		}

		$data['source_interest_tags'] = array();

		foreach ($source_interest_tags as $country_id => $tags) {
			if ($country_id) {
				$country_info = $this->model_localisation_country->getCountry($country_id);
			} else {
				$country_info = array('name' => $this->language->get('text_default'));
			}

			$data['source_interest_tags'][] = array(
				'country_id' => $country_id,
				'name'       => $country_info['name'],
				'tags'       => $tags
			);
		}

		uasort($data['source_interest_tags'], array($this, 'account_tag_sort'));

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

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'source_interest_id' => $this->request->get['source_interest_id']
		);

		$source_interest_total = $this->model_catalog_source_interest->getSourceHistoryTotal($filter_data);

		$data['histories'] = $this->model_catalog_source_interest->getSourceHistory($filter_data);

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_username'] = $this->url->link('catalog/source_interest/edit', $url .'&source_interest_id='.$this->request->get['source_interest_id']. '&sort=a.username', true);
		$data['sort_customer'] = $this->url->link('catalog/source_interest/edit', $url .'&source_interest_id='.$this->request->get['source_interest_id']. '&sort=c.firstname', true);
		$data['sort_date']     = $this->url->link('catalog/source_interest/edit', $url .'&source_interest_id='.$this->request->get['source_interest_id']. '&sort=sih.date_added', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $source_interest_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('catalog/source_interest', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($source_interest_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($source_interest_total - $this->config->get('config_limit_admin'))) ? $source_interest_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $source_interest_total, ceil($source_interest_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/source_interest_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/source_interest')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['source_interest_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}
		
		if (isset($this->request->post['source_interest_accounts'])) {
			foreach ($this->request->post['source_interest_accounts'] as $country_id => $accounts) {
				foreach ($accounts as $key => $account) {
					if (utf8_strlen($account['account']) < 1) {
						$this->error['account'][$country_id][$key] = $this->language->get('error_account');
					}
				}
			}
		}

		if (isset($this->request->post['source_interest_tags'])) {
			foreach ($this->request->post['source_interest_tags'] as $country_id => $tags) {
				foreach ($tags as $key => $tag) {
					if (utf8_strlen($tag['tag']) < 1) {
						$this->error['tag'][$country_id][$key] = $this->language->get('error_tag');
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/source_interest')) {
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

	public function country_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('localisation/country');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_localisation_country->getCountries($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'country_id' => $result['country_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/source_interest');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_source_interest->getSourceInterests($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'source_interest_id' => $result['source_interest_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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

	protected function account_tag_sort($a, $b) {
		if (!$a['country_id']) {
			return -1;
		}

		if (!$b['country_id']) {
			return 1;
		}

		return ($a['name'] < $b['name']) ? -1 : 1; 
	}
}