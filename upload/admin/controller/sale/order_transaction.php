<?php
class ControllerSaleOrderTransaction extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/order_transaction');

		$this->load->model('sale/order_transaction');

		$this->response->setOutput($this->getList());
	}

	public function capture() {
		$json = array();

		$this->load->language('sale/order_transaction');

		$this->load->model('sale/order_transaction');

		$order_transaction_info = $this->model_sale_order_transaction->getOrderTransaction($this->request->get['order_transaction_id']);

		if ($order_transaction_info && ($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateCapture()) {
			if (file_exists(DIR_APPLICATION . 'model/payment/' . $order_transaction_info['payment_code'] . '.php')) {
				$this->load->model('payment/' . $order_transaction_info['payment_code']);

				if (method_exists($this->{'model_payment_' . $order_transaction_info['payment_code']}, 'capture')) {
					$result = $this->{'model_payment_' . $order_transaction_info['payment_code']}->capture($order_transaction_info, $this->request->post['amount']);

					if ($result === true) {
						$this->session->data['success'] = $this->language->get('success_capture');
					} else {
						$this->error['warning'] = sprintf($this->language->get('error_gateway'), $result);
					}
				} else {
					$this->error['warning'] = $this->language->get('error_no_capture_method');
				}
			} else {
				$this->error['warning'] = $this->language->get('error_no_capture_method');
			}
		}

		$json['html'] = $this->getList();

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function refund() {
		$json = array();

		$this->load->language('sale/order_transaction');

		$this->load->model('sale/order_transaction');

		$order_transaction_info = $this->model_sale_order_transaction->getOrderTransaction($this->request->get['order_transaction_id']);

		if ($order_transaction_info && ($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateRefund()) {
			// set the amount of the transaction based on the currency used
			$order_transaction_info['amount'] = $this->currency->format(
				$order_transaction_info['amount'],
				$order_transaction_info['currency_code'],
				$order_transaction_info['currency_value'],
				false
			);

			if (file_exists(DIR_APPLICATION . 'model/payment/' . $order_transaction_info['payment_code'] . '.php')) {
				$this->load->model('payment/' . $order_transaction_info['payment_code']);

				if (method_exists($this->{'model_payment_' . $order_transaction_info['payment_code']}, 'refund')) {
					$result = $this->{'model_payment_' . $order_transaction_info['payment_code']}->refund($order_transaction_info, $this->request->post['amount']);

					if ($result == 'voided' || $result == 'refunded') {
						// get how much has been paid on this order
						$order_total_paid = $this->model_sale_order_transaction->getTotalAmountPaidForOrder($this->request->get['order_id']);

						// change order status to refunded if the total is now 0
						if (!$this->currency->format($order_total_paid, $order_transaction_info['currency_code'], $order_transaction_info['currency_value'], false)) {
							// change $order_total_paid back to the original amount
							$order_total_data = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

							foreach ($order_total_data as $order_total) {
								if ($order_total['code'] == 'total') {
									$order_total_paid = $order_total['value'];
								}
							}

							$json['order_status_id'] = $this->config->get('config_' . $result . '_status_id');
							$json['comment']         = sprintf($this->language->get('comment_' . $result), $this->user->getUserName());
						}

						$this->load->model('sale/order');
						$this->model_sale_order->editOrderTotal($this->request->get['order_id'], $order_total_paid);

						$this->session->data['success'] = $this->language->get('success_refund');
					} else {
						$this->error['warning'] = sprintf($this->language->get('error_gateway'), $result);
					}
				} else {
					$this->error['warning'] = $this->language->get('error_no_refund_method');
				}
			} else {
				$this->error['warning'] = $this->language->get('error_no_refund_method');
			}
		}

		$json['html'] = $this->getList();

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function getList() {
		$data = $this->language->all();

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['permission_modify'] = $this->user->hasPermission('modify', 'sale/order_transaction');

		$transaction_statuses = array(
			$this->language->get('text_pending'),
			$this->language->get('text_complete'),
			$this->language->get('text_voided')
		);

		$this->load->model('sale/order');

		$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

		if ($order_info) {
			$total_amount_paid = 0;

			$data['order_transactions'] = array();

			$order_transaction_data = $this->model_sale_order_transaction->getOrderTransactions($this->request->get['order_id']);

			if ($order_transaction_data) {
				foreach ($order_transaction_data as $order_transaction) {
					$total_amount_paid += $order_transaction['amount'];

					$actions = array();

					if ($this->user->hasPermission('modify', 'sale/order_transaction')) {
						if (file_exists(DIR_APPLICATION . 'model/payment/' . $order_transaction['payment_code'] . '.php')) {
							$this->load->model('payment/' . $order_transaction['payment_code']);

							if ($order_transaction['status'] == 0) {
								if (method_exists($this->{'model_payment_' . $order_transaction['payment_code']}, 'capture')) {
									$actions[] = array(
										'label' => $this->language->get('button_capture'),
										'class' => 'capture',
										'href'  => $this->url->link('sale/order_transaction/capture', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&order_transaction_id=' . $order_transaction['order_transaction_id'], true)
									);
								}
							}

							if ($order_transaction['status'] == 1 && $order_transaction['amount'] > 0) {
								if (method_exists($this->{'model_payment_' . $order_transaction['payment_code']}, 'refund')) {
									$actions[] = array(
										'label' => $this->language->get('button_refund'),
										'class' => 'refund',
										'href'  => $this->url->link('sale/order_transaction/refund', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&order_transaction_id=' . $order_transaction['order_transaction_id'], true)
									);
								}
							}
						}
					}

					$data['order_transactions'][] = array(
						'payment_method' => $order_transaction['payment_method'],
						'transaction_id' => $order_transaction['transaction_id'],
						'status'         => (isset($transaction_statuses[$order_transaction['status']]) ? $transaction_statuses[$order_transaction['status']] : ''),
						'amount'         => $this->currency->format($order_transaction['amount'], $order_transaction['currency_code'], $order_transaction['currency_value']),
						'amount_value'   => $this->currency->format($order_transaction['amount'], $order_transaction['currency_code'], $order_transaction['currency_value'], false),
						'date_added'     => date($this->language->get('date_format_short'), strtotime($order_transaction['date_added'])),
						'actions'        => $actions
					);
				}
			}

			$data['total_amount_paid']       = $this->currency->format($total_amount_paid, $order_info['currency_code'], $order_info['currency_value']);
			$data['total_amount_paid_value'] = $this->currency->format($total_amount_paid, $order_info['currency_code'], $order_info['currency_value'], false);

			$data['store'] = HTTPS_CATALOG;

			$data['order_id'] = $this->request->get['order_id'];
			$data['token']    = $this->session->data['token'];

			return $this->load->view('sale/order_transaction_list', $data);
		}
	}

	private function validateCapture() {
		if (!$this->user->hasPermission('modify', 'sale/order_transaction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function validateRefund() {
		if (!$this->user->hasPermission('modify', 'sale/order_transaction')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['amount'])) {
			$this->error['warning'] = $this->language->get('error_no_refund_amount');
		}

		return !$this->error;
	}
}