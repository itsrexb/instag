<?php
class ControllerAffiliateTransaction extends Controller {
	public function index() {
		if (!$this->affiliate->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('affiliate/transaction', '', true);

			$this->response->redirect($this->url->link('affiliate/login', '', true));
		}

		$data = $this->load->language('affiliate/transaction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('affiliate/transaction');

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
			'start' => ($page - 1) * $this->config->get('config_product_limit'),
			'limit' => $this->config->get('config_product_limit')
		);

		$transaction_total = $this->model_affiliate_transaction->getTotalTransactions();

		$results = $this->model_affiliate_transaction->getTransactions($filter_data);

		$affiliate_timezone = new DateTimeZone($this->affiliate->getTimeZone());

		foreach ($results as $result) {
			$date_added = new DateTime($result['date_added']);

			$date_added->setTimezone($affiliate_timezone);

			$data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => $date_added->format($this->language->get('date_format_short'))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $transaction_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_product_limit');
		$pagination->url   = $this->url->link('affiliate/transaction', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * $this->config->get('config_product_limit')) + 1 : 0, ((($page - 1) * $this->config->get('config_product_limit')) > ($transaction_total - $this->config->get('config_product_limit'))) ? $transaction_total : ((($page - 1) * $this->config->get('config_product_limit')) + $this->config->get('config_product_limit')), $transaction_total, ceil($transaction_total / $this->config->get('config_product_limit')));

		$data['balance'] = $this->currency->format($this->model_affiliate_transaction->getBalance());

		$data['dashboard'] = $this->url->link('affiliate/dashboard', '', true);

		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['column_right']   = $this->load->controller('common/column_right');
		$data['content_top']    = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('affiliate/transaction', $data));
	}
}
