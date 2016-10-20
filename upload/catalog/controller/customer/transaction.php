<?php
class ControllerCustomerTransaction extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('customer/transaction', '', true);

			$this->response->redirect($this->url->link('customer/login'));
		}

		$data = $this->load->language('customer/transaction');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['transactions'] = array();

		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$this->load->model('customer/transaction');

		$transaction_total = $this->model_customer_transaction->getTotalTransactions();

		$results = $this->model_customer_transaction->getTransactions($filter_data);

		foreach ($results as $result) {
			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page  = $page;
		$pagination->limit = 10;
		$pagination->url   = $this->url->link('customer/transaction', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($transaction_total - 10)) ? $transaction_total : ((($page - 1) * 10) + 10), $transaction_total, ceil($transaction_total / 10));

		$data['total'] = $this->currency->format($this->customer->getBalance());

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('customer/transaction', $data));
	}
}