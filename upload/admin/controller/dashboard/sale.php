<?php
class ControllerDashboardSale extends Controller {
	public function index() {
		$data = $this->load->language('dashboard/sale');

		$data['token'] = $this->session->data['token'];

		// Total Orders
		$this->load->model('report/sale');

		$sale_total = $this->model_report_sale->getTotalSales();

		if ($sale_total > 1000000000000) {
			$data['total'] = round($sale_total / 1000000000000, 1) . 'T';
		} elseif ($sale_total > 1000000000) {
			$data['total'] = round($sale_total / 1000000000, 1) . 'B';
		} elseif ($sale_total > 1000000) {
			$data['total'] = round($sale_total / 1000000, 1) . 'M';
		} elseif ($sale_total > 1000) {
			$data['total'] = round($sale_total / 1000, 1) . 'K';
		} else {
			$data['total'] = round($sale_total);
		}

		$data['sale'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], true);

		return $this->load->view('dashboard/sale', $data);
	}
}