<?php
class ControllerReportSaleProfit extends Controller {
	public function index() {
		$data = $this->load->language('report/sale_profit');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_month'])) {
			$filter_month = trim($this->request->get['filter_month']);
		} else {
			$filter_month = "";
		}

		if (isset($this->request->get['filter_year'])) {
			$filter_year = trim($this->request->get['filter_year']);
		} else {
			$filter_year = date('Y');
		}

		$url = '';

		if (isset($this->request->get['filter_month'])) {
			$url .= '&filter_month=' . $this->request->get['filter_month'];
		}

		if (isset($this->request->get['filter_year'])) {
			$url .= '&filter_year=' . $this->request->get['filter_year'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}else{
			$page = 1;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/sale_profit', 'token=' . $this->session->data['token'] . $url, true)
		);

		$this->load->model('report/sale');

		$data['orders'] = array();

		$filter_data = array(
			'filter_month'	    	 => $filter_month,
			'filter_year'	   		 => $filter_year,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_report_sale->getProfitTotal($filter_data);

		$results = $this->model_report_sale->getProfit($filter_data);

		$data['gross_total']			= 0;
		$data['acquisition_cost_total'] = 0;
		$data['discount_total'] 		= 0;
		$data['net_total']				= 0;
		$data['credit_total']			= 0;
		$credit 						= 0;

		foreach ($results as $result) {

			$credit = $result['gross'] - (abs($result['acquisition_cost']) + abs($result['discount']) + abs($result['net']));

			$data['gross_total'] 			 += abs($result['gross']);
			$data['acquisition_cost_total']  += abs($result['acquisition_cost']);
			$data['discount_total'] 		 += abs($result['discount']);
			$data['net_total'] 				 += abs($result['net']);
			$data['credit_total'] 			 += abs($result['credit']);

			$data['orders'][] = array(
				'month' 			=> $result['month'],
				'orders'     		=> $result['orders'],
				'products'   		=> $result['products'],
				'gross'   			=> $this->currency->format(abs($result['gross']), $this->config->get('config_currency')),
				'acquisition_cost'  => $this->currency->format(abs($result['acquisition_cost']), $this->config->get('config_currency')),
				'discount' 		    => $this->currency->format(abs($result['discount']), $this->config->get('config_currency')),
				'net'     			=> $this->currency->format(abs($result['net']), $this->config->get('config_currency')),
				'credit'     		=> $this->currency->format($credit, $this->config->get('config_currency'))
			);
			
			$data['credit_total'] += $credit;
			$credit = 0;
		}
		$data['gross_total']				= $this->currency->format($data['gross_total'], $this->config->get('config_currency'));
		$data['acquisition_cost_total'] 	= $this->currency->format($data['acquisition_cost_total'], $this->config->get('config_currency'));
		$data['discount_total'] 			= $this->currency->format($data['discount_total'], $this->config->get('config_currency'));
		$data['net_total'] 					= $this->currency->format($data['net_total'], $this->config->get('config_currency'));
		$data['credit_total'] 				= $this->currency->format($data['credit_total'], $this->config->get('config_currency'));

		$data['token'] = $this->session->data['token'];


		$url = '';

		if (isset($this->request->get['filter_month'])) {
			$url .= '&filter_month=' . $this->request->get['filter_month'];
		}

		if (isset($this->request->get['filter_year'])) {
			$url .= '&filter_year=' . $this->request->get['filter_year'];
		}

		$data['months'] =  array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "Novemeber", "December");
		$data['years'] = array();
		for($year=(date('Y')-1); $year >= (date('Y')-5); $year--){
			$data['years'][] = $year;
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/sale_profit', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_month'] 	= $filter_month;
		$data['filter_year'] 	= $filter_year;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/sale_profit', $data));
	}
}