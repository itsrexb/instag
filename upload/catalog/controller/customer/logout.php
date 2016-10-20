<?php
class ControllerCustomerLogout extends Controller {
	public function index() {
		if ($this->customer->isLogged()) {
			$this->customer->logout();
			$this->instaghive->logout();

			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}

		$this->response->redirect($this->url->link('customer/login', '', true));
	}
}