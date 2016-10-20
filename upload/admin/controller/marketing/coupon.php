<?php
class ControllerMarketingCoupon extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		$this->getList();
	}

	public function add() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_marketing_coupon->addCoupon($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('marketing/coupon', $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_marketing_coupon->editCoupon($this->request->get['coupon_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('marketing/coupon', $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $coupon_id) {
				$this->model_marketing_coupon->deleteCoupon($coupon_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->getUrl();

			$this->response->redirect($this->url->link('marketing/coupon', $url, true));
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
			'href' => $this->url->link('marketing/coupon', $url, true)
		);
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

		$data['add'] = $this->url->link('marketing/coupon/add', $url, true);
		$data['delete'] = $this->url->link('marketing/coupon/delete', $url, true);

		$data['coupons'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$coupon_total = $this->model_marketing_coupon->getTotalCoupons();

		$results = $this->model_marketing_coupon->getCoupons($filter_data);

		foreach ($results as $result) {
			$data['coupons'][] = array(
				'coupon_id'  => $result['coupon_id'],
				'name'       => $result['name'],
				'code'       => $result['code'],
				'discount'   => $result['discount'],
				'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
				'date_end'   => ($result['date_end'] != '0000-00-00')? date($this->language->get('date_format_short'), strtotime($result['date_end'])):'Forever',
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'edit'       => $this->url->link('marketing/coupon/edit', $url . '&coupon_id=' . $result['coupon_id'], true)
			);
		}


		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_name']       = $this->url->link('marketing/coupon', $url . '&sort=name', true);
		$data['sort_code']       = $this->url->link('marketing/coupon', $url . '&sort=code', true);
		$data['sort_discount']   = $this->url->link('marketing/coupon', $url . '&sort=discount', true);
		$data['sort_date_start'] = $this->url->link('marketing/coupon', $url . '&sort=date_start', true);
		$data['sort_date_end']   = $this->url->link('marketing/coupon', $url . '&sort=date_end', true);
		$data['sort_status']     = $this->url->link('marketing/coupon', $url . '&sort=status', true);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $coupon_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('marketing/coupon', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($coupon_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($coupon_total - $this->config->get('config_limit_admin'))) ? $coupon_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $coupon_total, ceil($coupon_total / $this->config->get('config_limit_admin')));

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/coupon_list', $data));
	}

	protected function getForm() {
		$data = $this->language->all();

		$data['text_form'] = !isset($this->request->get['coupon_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}

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

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		if (isset($this->error['date_start'])) {
			$data['error_date_start'] = $this->error['date_start'];
		} else {
			$data['error_date_start'] = '';
		}

		if (isset($this->error['date_end'])) {
			$data['error_date_end'] = $this->error['date_end'];
		} else {
			$data['error_date_end'] = '';
		}

		$url = $this->getUrl();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/coupon', $url, true)
		);

		if (!isset($this->request->get['coupon_id'])) {
			$data['action'] = $this->url->link('marketing/coupon/add', $url, true);
		} else {
			$data['action'] = $this->url->link('marketing/coupon/edit', $url . '&coupon_id=' . $this->request->get['coupon_id'], true);
		}

		$data['cancel'] = $this->url->link('marketing/coupon', $url, true);

		if (isset($this->request->get['coupon_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
			$coupon_info = $this->model_marketing_coupon->getCoupon($this->request->get['coupon_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else if (!empty($coupon_info)) {
			$data['name'] = $coupon_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} else if (!empty($coupon_info)) {
			$data['code'] = $coupon_info['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} else if (!empty($coupon_info)) {
			$data['type'] = $coupon_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($this->request->post['discount'])) {
			$data['discount'] = $this->request->post['discount'];
		} else if (!empty($coupon_info)) {
			$data['discount'] = $coupon_info['discount'];
		} else {
			$data['discount'] = '';
		}

		if (isset($this->request->post['recurring'])) {
			$data['recurring'] = $this->request->post['recurring'];
		} else if (!empty($coupon_info)) {
			$data['recurring'] = $coupon_info['recurring'];
		} else {
			$data['recurring'] = '';
		}

		if (isset($this->request->post['recurring_limit'])) {
			$data['recurring_limit'] = $this->request->post['recurring_limit'];
		} else if (!empty($coupon_info)) {
			$data['recurring_limit'] = $coupon_info['recurring_limit'];
		} else {
			$data['recurring_limit'] = '';
		}

		if (isset($this->request->post['total'])) {
			$data['total'] = $this->request->post['total'];
		} else if (!empty($coupon_info)) {
			$data['total'] = $coupon_info['total'];
		} else {
			$data['total'] = '';
		}

		if (isset($this->request->post['coupon_product'])) {
			$products = $this->request->post['coupon_product'];
		} else if (isset($this->request->get['coupon_id'])) {
			$products = $this->model_marketing_coupon->getCouponProducts($this->request->get['coupon_id']);
		} else {
			$products = array();
		}

		$this->load->model('catalog/product');

		$data['coupon_product'] = array();

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['coupon_product'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}

		if (isset($this->request->post['coupon_category'])) {
			$categories = $this->request->post['coupon_category'];
		} else if (isset($this->request->get['coupon_id'])) {
			$categories = $this->model_marketing_coupon->getCouponCategories($this->request->get['coupon_id']);
		} else {
			$categories = array();
		}

		$this->load->model('catalog/category');

		$data['coupon_category'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['coupon_category'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
				);
			}
		}

		if (isset($this->request->post['affiliate'])) {
			$data['affiliate'] = $this->request->post['affiliate'];
		} else if (!empty($coupon_info)) {
			$data['affiliate'] = $coupon_info['affiliate'];
		} else {
			$data['affiliate'] = '';
		}

		if (isset($this->request->post['affiliate_id'])) {
			$data['affiliate_id'] = $this->request->post['affiliate_id'];
		} else if (!empty($coupon_info)) {
			$data['affiliate_id'] = $coupon_info['affiliate_id'];
		} else {
			$data['affiliate_id'] = '';
		}

		if (isset($this->request->post['affiliate_fee'])) {
			$data['affiliate_fee'] = $this->request->post['affiliate_fee'];
		} else if (!empty($coupon_info)) {
			$data['affiliate_fee'] = $coupon_info['affiliate_fee'];
		} else {
			$data['affiliate_fee'] = '';
		}

		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} else if (!empty($coupon_info)) {
			$data['date_start'] = ($coupon_info['date_start'] != '0000-00-00' ? $coupon_info['date_start'] : '');
		} else {
			$data['date_start'] = date('Y-m-d', time());
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} else if (!empty($coupon_info)) {
			$data['date_end'] = ($coupon_info['date_end'] != '0000-00-00' ? $coupon_info['date_end'] : '');
		} else {
			$data['date_end'] = date('Y-m-d', strtotime('+1 month'));
		}

		if (isset($this->request->post['uses_total'])) {
			$data['uses_total'] = $this->request->post['uses_total'];
		} else if (!empty($coupon_info)) {
			$data['uses_total'] = $coupon_info['uses_total'];
		} else {
			$data['uses_total'] = 1;
		}

		if (isset($this->request->post['uses_customer'])) {
			$data['uses_customer'] = $this->request->post['uses_customer'];
		} else if (!empty($coupon_info)) {
			$data['uses_customer'] = $coupon_info['uses_customer'];
		} else {
			$data['uses_customer'] = 1;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else if (!empty($coupon_info)) {
			$data['status'] = $coupon_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/coupon_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'marketing/coupon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['code']) < 3) || (utf8_strlen($this->request->post['code']) > 255)) {
			$this->error['code'] = $this->language->get('error_code');
		}

		$coupon_info = $this->model_marketing_coupon->getCouponByCode($this->request->post['code']);

		if ($coupon_info) {
			if (!isset($this->request->get['coupon_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} else if ($coupon_info['coupon_id'] != $this->request->get['coupon_id']) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'marketing/coupon')) {
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

	public function history() {
		$data = $this->load->language('marketing/coupon');

		$this->load->model('marketing/coupon');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_marketing_coupon->getCouponHistories($this->request->get['coupon_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $result['amount'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_marketing_coupon->getTotalCouponHistories($this->request->get['coupon_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page  = $page;
		$pagination->limit = 10;
		$pagination->url   = $this->url->link('marketing/coupon/history', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('marketing/coupon_history', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('marketing/coupon');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'filter_code' => $this->request->get['filter_name'],
				'sort'        => 'code',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_marketing_coupon->getCoupons($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'coupon_id' => $result['coupon_id'],
					'name'      => strip_tags(html_entity_decode($result['code'] . ' - ' . $result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}

			$sort_order = array();

			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			array_multisort($sort_order, SORT_ASC, $json);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
