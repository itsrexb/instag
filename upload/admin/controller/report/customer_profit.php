<?php
class ControllerReportCustomerProfit extends Controller {
	public function index() {
		$data = $this->load->language('report/customer_profit');

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

		if (isset($this->request->get['filter_group'])) {
			$filter_group = $this->request->get['filter_group'];
		} else {
			$filter_group = '';
		}

		if (isset($this->request->get['filter_affiliate_id'])) {
			$filter_affiliate_id = $this->request->get['filter_affiliate_id'];

			$this->load->model('affiliate/affiliate');

			$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($filter_affiliate_id);

			if ($affiliate_info) {
				if ($affiliate_info['path']) {
					$filter_affiliate = $affiliate_info['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
				} else {
					$filter_affiliate = $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
				}
			} else {
				$filter_affiliate = null;
			}
		} else {
			$filter_affiliate_id = null;
			$filter_affiliate    = null;
		}

		if (isset($this->request->get['filter_ext_aff_id'])) {
			$filter_ext_aff_id = $this->request->get['filter_ext_aff_id'];
		} else {
			$filter_ext_aff_id = null;
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
			'href' => $this->url->link('report/customer_profit', 'token=' . $this->session->data['token'] . $url, true)
		);

		$this->load->model('report/customer');

		$data['orders'] = array();

		$filter_data = array(
			'filter_date_start'	  => $filter_date_start,
			'filter_date_end'	    => $filter_date_end,
			'filter_group'        => $filter_group,
			'filter_affiliate_id' => $filter_affiliate_id,
			'filter_ext_aff_id'   => $filter_ext_aff_id,
			'start'               => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'               => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_report_customer->getTotalProfit($filter_data);

		$results = $this->model_report_customer->getProfit($filter_data);

		foreach ($results as $result) {
			$total = $this->model_report_customer->getTotalRevenue(date($this->language->get('date_format_short'), strtotime($result['date_start'])),date($this->language->get('date_format_short'), strtotime($result['date_end'])), $filter_data);
			$total_paid_accounts = $this->model_report_customer->getTotalPaidAccounts(date($this->language->get('date_format_short'), strtotime($result['date_start'])),date($this->language->get('date_format_short'), strtotime($result['date_end'])), $filter_data);
			
			$data['orders'][] = array(
				'date_start'          => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
				'date_end'            => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
				'total_customers'     => $result['total_customers'],
				'total_paid_accounts' => $total_paid_accounts,
				'total'               => $this->currency->format($total, $this->config->get('config_currency'))
			);
		}

		$data['token'] = $this->session->data['token'];

		$data['groups'] = array();
		
		$data['groups'][] = array(
			'text'  => $this->language->get('text_all_group'),
			'value' => '',
		);
		
		$data['groups'][] = array(
			'text'  => $this->language->get('text_year'),
			'value' => 'year',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_month'),
			'value' => 'month',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_week'),
			'value' => 'week',
		);

		$data['groups'][] = array(
			'text'  => $this->language->get('text_day'),
			'value' => 'day',
		);

		$url = $this->getUrl(array('page'));

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('report/customer_profit', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start']   = $filter_date_start;
		$data['filter_date_end']     = $filter_date_end;
		$data['filter_group']        = $filter_group;
		$data['filter_affiliate_id'] = $filter_affiliate_id;
		$data['filter_affiliate']    = $filter_affiliate;
		$data['filter_ext_aff_id']   = $filter_ext_aff_id;
		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/customer_profit', $data));
	}

	protected function getUrl($blacklist = array()) {
		$url_data = array();

		$url_data['token'] = $this->session->data['token'];

		if (isset($this->request->get['filter_date_start']) && !in_array('date_start', $blacklist)) {
			$url_data['filter_date_start'] = html_entity_decode($this->request->get['filter_date_start'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_date_end']) && !in_array('date_end', $blacklist)) {
			$url_data['filter_date_end'] = html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_group']) && !in_array('group', $blacklist)) {
			$url_data['filter_group'] = html_entity_decode($this->request->get['filter_group'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_affiliate_id']) && !in_array('affiliate_id', $blacklist)) {
			$url_data['filter_affiliate_id'] = html_entity_decode($this->request->get['filter_affiliate_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_ext_aff_id']) && !in_array('ext_aff_id', $blacklist)) {
			$url_data['filter_ext_aff_id'] = html_entity_decode($this->request->get['filter_ext_aff_id'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['page']) && !in_array('page', $blacklist)) {
			$url_data['page'] = $this->request->get['page'];
		}

		return http_build_query($url_data);
	}
}
