<?php
class ControllerDashboardActivity extends Controller {
	public function index() {
		$data = $this->load->language('dashboard/activity');

		$data['token'] = $this->session->data['token'];

		$data['activities'] = array();

		$this->load->model('report/activity');

		$results = $this->model_report_activity->getActivities();

		foreach ($results as $result) {
			$comment = vsprintf($this->language->get('text_' . $result['key']), json_decode($result['data'], true));

			$find = array(
				'customer_id=',
				'order_id=',
				'affiliate_id=',
				'return_id='
			);

			$replace = array(
				$this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=', true),
				$this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=', true),
				$this->url->link('affiliate/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=', true),
				$this->url->link('sale/return/edit', 'token=' . $this->session->data['token'] . '&return_id=', true)
			);

			$data['activities'][] = array(
				'comment'    => str_replace($find, $replace, $comment),
				'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added']))
			);
		}

		return $this->load->view('dashboard/activity.tpl', $data);
	}
}