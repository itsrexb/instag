<?php
class ControllerReportAccountSourceInterest extends Controller {
	public function index() {
		$data = $this->load->language('report/account_source_interest');

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = trim($this->request->get['order']);
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = trim($this->request->get['page']);
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
			'href' => $this->url->link('report/account_source_interest', $url, true)
		);

		$this->document->setTitle($this->language->get('heading_title'));

		$data['token'] = $this->session->data['token'];

		$filter_data = array(
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$this->load->model('report/source_interest');
		$this->load->model('catalog/source_interest');

		$account_source_interest_total = $this->model_report_source_interest->getTotalSourceInterests($filter_data);

		$account_source_interest_data = $this->model_report_source_interest->getSourceInterests($filter_data);

		$data['account_source_interest'] = array();

		foreach ($account_source_interest_data as $account_source_interest) {

			$total_accounts        = 0;
			$total_accounts_low    = 0;
			$total_accounts_medium = 0;
			$total_accounts_high   = 0;
			$total_tags            = 0;
			$total_tags_low        = 0;
			$total_tags_medium     = 0;
			$total_tags_high       = 0;

			$account_data = $this->model_catalog_source_interest->getSourceInterestAccounts($account_source_interest['source_interest_id']);

			foreach ($account_data as $accounts) {
				foreach ($accounts as $account) {
					${'total_accounts_' . $account['quality']}++;
					$total_accounts++;
				}
			}

			$tag_data = $this->model_catalog_source_interest->getSourceInterestTags($account_source_interest['source_interest_id']);

			foreach ($tag_data as $tags) {
				foreach ($tags as $tag) {
					${'total_tags_' . $tag['quality']}++;
					$total_tags++;
				}
			}

			$data['account_source_interest'][] = array(
				'source_interest_id'    => $account_source_interest['source_interest_id'],
				'name'                  => $account_source_interest['name'],
				'history'               => $account_source_interest['history'],
				'total_accounts'        => $total_accounts,
				'total_accounts_low'    => $total_accounts_low,
				'total_accounts_medium' => $total_accounts_medium,
				'total_accounts_high'   => $total_accounts_high,
				'total_tags'            => $total_tags,
				'total_tags_low'        => $total_tags_low,
				'total_tags_medium'     => $total_tags_medium,
				'total_tags_high'       => $total_tags_high,
			);
		}

		$url = $this->getUrl(array('sort', 'order'));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_name']    = $this->url->link('report/account_source_interest', $url . '&sort=name', true);


		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $account_source_interest_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('report/account_source_interest', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($account_source_interest_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($account_source_interest_total - $this->config->get('config_limit_admin'))) ? $account_source_interest_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $account_source_interest_total, ceil($account_source_interest_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end']   = $filter_date_end;

		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/account_sources_interest', $data));
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_date_start']) && !in_array('date_start', $blacklist)) {
			$url_data['filter_date_start'] = $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end']) && !in_array('date_end', $blacklist)) {
			$url_data['filter_date_end'] = $this->request->get['filter_date_end'];
		}

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