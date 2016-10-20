<?php
class ControllerDashboardChartRevenue extends Controller {
	public function index() {
		$data = $this->load->language('dashboard/chart_revenue');

		$data['token'] = $this->session->data['token'];

		return $this->load->view('dashboard/chart_revenue', $data);
	}

	public function chart() {
		$this->load->language('dashboard/chart_revenue');

		$json = array(
			'total_revenue'      => array(),
			'unknown_revenue'    => array(),
			'affiliate_revenue'  => array(),
			'ext_aff_id_revenue' => array(),
			'xaxis'              => array()
		);

		if (isset($this->request->get['range'])) {
			$range = $this->request->get['range'];
		} else {
			$range = 'day';
		}

		$this->load->model('report/sale');

		switch ($range) {
			case 'day':
				$results = $this->model_report_sale->getChartTotalRevenue('day');

				foreach ($results['total'] as $key => $value) {
					$json['total_revenue'][] = array($key, $value);
				}

				foreach ($results['unknown'] as $key => $value) {
					$json['unknown_revenue'][] = array($key, $value);
				}

				foreach ($results['affiliate'] as $key => $value) {
					$json['affiliate_revenue'][] = array($key, $value);
				}

				foreach ($results['ext_aff_id'] as $key => $value) {
					$json['ext_aff_id_revenue'][] = array($key, $value);
				}

				$filter_date_added_start = new \DateTime('today', new \DateTimeZone($this->config->get('config_timezone')));

				for ($i = 0; $i < 24; $i++) {
					$json['xaxis'][]         = array($i, $i);
					$json['xaxis_links'][$i] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_date_added_start=' . $filter_date_added_start->format('Y-m-d') . '&filter_date_added_end=' . $filter_date_added_start->format('Y-m-d'), true);
				}
				break;
			case 'sevendays':
				$results = $this->model_report_sale->getChartTotalRevenue('7days');

				foreach ($results['total'] as $key => $value) {
					$json['total_revenue'][] = array($key, $value);
				}

				foreach ($results['unknown'] as $key => $value) {
					$json['unknown_revenue'][] = array($key, $value);
				}

				foreach ($results['affiliate'] as $key => $value) {
					$json['affiliate_revenue'][] = array($key, $value);
				}

				foreach ($results['ext_aff_id'] as $key => $value) {
					$json['ext_aff_id_revenue'][] = array($key, $value);
				}

				$datetime = new \DateTime('-6 days', new \DateTimeZone($this->config->get('config_timezone')));
				$datetime->modify('today');

				$datetime_end = new \DateTime('+1 day', new \DateTimeZone($this->config->get('config_timezone')));
				$datetime_end->modify('today');

				$datetime_diff = (int)$datetime->diff($datetime_end)->days;

				for ($i = 0; $i < $datetime_diff; $i++) {
					$json['xaxis'][]         = array($i, $datetime->format('d'));
					$json['xaxis_links'][$i] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_date_added_start=' . $datetime->format('Y-m-d') . '&filter_date_added_end=' . $datetime->format('Y-m-d'), true);

					$datetime->modify('+1 day');
				}
				break;
			case 'week':
				$results = $this->model_report_sale->getChartTotalRevenue('week');

				foreach ($results['total'] as $key => $value) {
					$json['total_revenue'][] = array($key, $value);
				}

				foreach ($results['unknown'] as $key => $value) {
					$json['unknown_revenue'][] = array($key, $value);
				}

				foreach ($results['affiliate'] as $key => $value) {
					$json['affiliate_revenue'][] = array($key, $value);
				}

				foreach ($results['ext_aff_id'] as $key => $value) {
					$json['ext_aff_id_revenue'][] = array($key, $value);
				}

				$datetime = new \DateTime('-' . date('w') . ' days', new \DateTimeZone($this->config->get('config_timezone')));

				for ($i = 0; $i < 7; $i++) {
					$json['xaxis'][]         = array($i, $datetime->format('D'));
					$json['xaxis_links'][$i] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_date_added_start=' . $datetime->format('Y-m-d') . '&filter_date_added_end=' . $datetime->format('Y-m-d'), true);

					$datetime->modify('+1 day');
				}
				break;
			case 'thirtydays':
				$results = $this->model_report_sale->getChartTotalRevenue('30days');

				foreach ($results['total'] as $key => $value) {
					$json['total_revenue'][] = array($key, $value);
				}

				foreach ($results['unknown'] as $key => $value) {
					$json['unknown_revenue'][] = array($key, $value);
				}

				foreach ($results['affiliate'] as $key => $value) {
					$json['affiliate_revenue'][] = array($key, $value);
				}

				foreach ($results['ext_aff_id'] as $key => $value) {
					$json['ext_aff_id_revenue'][] = array($key, $value);
				}

				$datetime = new \DateTime('-29 days', new \DateTimeZone($this->config->get('config_timezone')));
				$datetime->modify('today');

				$datetime_end = new \DateTime('+1 day', new \DateTimeZone($this->config->get('config_timezone')));
				$datetime_end->modify('today');

				$datetime_diff = (int)$datetime->diff($datetime_end)->days;

				for ($i = 0; $i < $datetime_diff; $i++) {
					$json['xaxis'][]         = array($i, $datetime->format('d'));
					$json['xaxis_links'][$i] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_date_added_start=' . $datetime->format('Y-m-d') . '&filter_date_added_end=' . $datetime->format('Y-m-d'), true);

					$datetime->modify('+1 day');
				}
				break;
			case 'month':
				$results = $this->model_report_sale->getChartTotalRevenue('month');

				foreach ($results['total'] as $key => $value) {
					$json['total_revenue'][] = array($key, $value);
				}

				foreach ($results['unknown'] as $key => $value) {
					$json['unknown_revenue'][] = array($key, $value);
				}

				foreach ($results['affiliate'] as $key => $value) {
					$json['affiliate_revenue'][] = array($key, $value);
				}

				foreach ($results['ext_aff_id'] as $key => $value) {
					$json['ext_aff_id_revenue'][] = array($key, $value);
				}

				$datetime = new \DateTime(date('Y-m-01'), new \DateTimeZone($this->config->get('config_timezone')));

				$max_days = $datetime->format('t');

				for ($i = 1; $i <= $max_days; $i++) {
					$json['xaxis'][]         = array($i, $datetime->format('d'));
					$json['xaxis_links'][$i] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_date_added_start=' . $datetime->format('Y-m-d') . '&filter_date_added_end=' . $datetime->format('Y-m-d'), true);

					$datetime->modify('+1 day');
				}
				break;
			case 'year':
				$results = $this->model_report_sale->getChartTotalRevenue('year');

				foreach ($results['total'] as $key => $value) {
					$json['total_revenue'][] = array($key, $value);
				}

				foreach ($results['unknown'] as $key => $value) {
					$json['unknown_revenue'][] = array($key, $value);
				}

				foreach ($results['affiliate'] as $key => $value) {
					$json['affiliate_revenue'][] = array($key, $value);
				}

				foreach ($results['ext_aff_id'] as $key => $value) {
					$json['ext_aff_id_revenue'][] = array($key, $value);
				}

				for ($i = 1; $i <= 12; $i++) {
					$filter_date_added_start = new \DateTime(date('Y-' . $i . '-01'), new \DateTimeZone($this->config->get('config_timezone')));

					$json['xaxis'][]         = array($i, date('M', mktime(0, 0, 0, $i)));
					$json['xaxis_links'][$i] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_date_added_start=' . $filter_date_added_start->format('Y-m-d') . '&filter_date_added_end=' . $filter_date_added_start->format('Y-m-t'), true);
				}
				break;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}