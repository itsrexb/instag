<?php
class ControllerDashboardOrder extends Controller {
	public function index() {
		$data = $this->load->language('dashboard/order');

		$data['token'] = $this->session->data['token'];

		// Total Orders
		$this->load->model('sale/order');

		$order_total = $this->model_sale_order->getTotalOrders();

		if ($order_total > 1000000000000) {
			$data['total'] = round($order_total / 1000000000000, 1) . 'T';
		} elseif ($order_total > 1000000000) {
			$data['total'] = round($order_total / 1000000000, 1) . 'B';
		} elseif ($order_total > 1000000) {
			$data['total'] = round($order_total / 1000000, 1) . 'M';
		} elseif ($order_total > 1000) {
			$data['total'] = round($order_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $order_total;
		}

		$data['order'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'], true);

		return $this->load->view('dashboard/order', $data);
	}
}