<?php
class ControllerTotalCoupon extends Controller {
	public function index() {
		if ($this->config->get('coupon_status')) {
			$data = $this->load->language('total/coupon');

			if (isset($this->session->data['coupon'])) {
				$data['coupon'] = $this->session->data['coupon'];
			} else {
				$data['coupon'] = '';
			}

			return $this->load->view('default/template/total/coupon', $data);
		}
	}

	public function coupon() {
		$this->load->language('total/coupon');

		$json = array('success' => false);

		if (empty($this->request->post['coupon'])) {
			unset($this->session->data['coupon']);

			$json['success'] = true;
		} else {
			$this->load->model('total/coupon');

			$coupon_info = $this->model_total_coupon->getCoupon($this->request->post['coupon']);

			if ($coupon_info) {
				$this->session->data['coupon'] = $this->request->post['coupon'];

				$json['success'] = true;
			} else {
				$json['error'] = $this->language->get('error_coupon');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}