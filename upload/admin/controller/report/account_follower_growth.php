<?php
class ControllerReportAccountFollowerGrowth extends Controller {
	public function index() {
		$this->load->language('report/account_follower_growth');

		$this->document->setTitle($this->language->get('heading_title'));

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

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_account'])) {
			$filter_account = $this->request->get['filter_account'];
		} else {
			$filter_account = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . $this->request->get['filter_customer'];
		}

		if (isset($this->request->get['filter_account'])) {
			$url .= '&filter_account=' . $this->request->get['filter_account'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/account_follower_growth', 'token=' . $this->session->data['token'] . $url, true)
		);

		$this->load->model('report/account');

		$data['account_followers'] = array();
		$total_customer = 0;
		$total_account = 0;
		$total_followers_beginning = 0;
		$total_followers_ending = 0;
		$total_change = 0;
		$total_change_percentage = 0;

		$account_followers_total = 0;
		if (isset($this->request->get['filter_customer']) || isset($this->request->get['filter_account'])) {
			$filter_data = array(
				'filter_date_start'	 => $filter_date_start,
				'filter_date_end'	 => $filter_date_end,
				'filter_customer'        => $filter_customer,
				'filter_account'         => $filter_account,
				'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
				'limit'                  => $this->config->get('config_limit_admin')
			);

			$results = $this->model_report_account->getAccountFollowers($filter_data);
		
			foreach ($results as $result) {
				$total_customer += 1;
				$total_account += 1;
				$total_followers_beginning += $result['followers_beginning'];
				$total_followers_ending += $result['followers_ending'];
				$change = $result['followers_ending'] - $result['followers_beginning'];
				if($result['followers_beginning']){
					$change_percentage = ($change/$result['followers_beginning'])*100;
				}
				$data['account_followers'][] = array(
					'customer'            => $result['customer'],
					'username'            => $result['username'],
					'followers_beginning' => $result['followers_beginning'],
					'followers_ending'    => $result['followers_ending'],
					'change'              => ($change > 0)? '+ '.$change:$change,
					'change_percentage'   => round($change_percentage,2).'%',
				);
			}

			$data['total_customer'] = $total_customer;
			$data['total_account'] = $total_account;
			$data['total_followers_beginning'] = $total_followers_beginning;
			$data['total_followers_ending'] = $total_followers_ending;			
			$total_change = $total_followers_ending - $total_followers_beginning;
			if($total_followers_beginning){
				$total_change_percentage = ($total_change/$total_followers_beginning)*100;
			}
			$data['total_change'] = ($total_change > 0)? '+ '.$total_change:$total_change;
			$data['total_change_percentage'] = round($total_change_percentage,2).'%';
		}
	
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_account'] = $this->language->get('column_account');
		$data['column_followers_beginning'] = $this->language->get('column_followers_beginning');
		$data['column_followers_ending'] = $this->language->get('column_followers_ending');
		$data['column_change'] = $this->language->get('column_change');
		$data['column_change_percentage'] = $this->language->get('column_change_percentage');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_account'] = $this->language->get('entry_account');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . $this->request->get['filter_customer'];
		}

		if (isset($this->request->get['filter_account'])) {
			$url .= '&filter_account=' . $this->request->get['filter_account'];
		}

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_customer'] = $filter_customer;
		$data['filter_account'] = $filter_account;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/account_follower_growth.tpl', $data));
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_username'])) {
			if (isset($this->request->get['filter_username'])) {
				$filter_username = $this->request->get['filter_username'];
			} else {
				$filter_username = '';
			}

			$this->load->model('customer/account');

			$filter_data = array(
				'filter_deleted'   => 0,
				'filter_username'  => $filter_username,
				'start'            => 0,
				'limit'            => 5
			);

			$results = $this->model_customer_account->getAccounts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'account_id'       => $result['account_id'],
					'username'         => $result['username']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['username'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	
}
