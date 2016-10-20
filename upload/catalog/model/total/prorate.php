<?php
class ModelTotalProrate extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('prorate_status')) {
			if (isset($this->session->data['prorate_credit'])) {
				unset($this->session->data['prorate_credit']);
			}

			$prorate = 0;

			$this->load->model('account/account');
			$this->load->model('checkout/order');
			$this->load->model('checkout/recurring_order');

			foreach ($this->cart->getRecurringProducts() as $product) {
				$recurring_order = $this->model_checkout_recurring_order->getLastRecurringOrder($this->customer->getId(), $product['account_id']);

				if ($recurring_order) {
					$order = $this->model_checkout_order->getLastOrderForRecurringOrder($recurring_order['recurring_order_id']);

					if ($order && $order['total'] > 0) {
						// get expiration date from cached expiration date
						$account_info = $this->model_account_account->getAccountFromCache($product['account_id']);

						if ($account_info && strtotime($account_info['date_expires']) > time()) {
							$seconds_in_cycle           = strtotime($account_info['date_expires']) - strtotime($order['date_added']);
							$seconds_remaining_in_cycle = strtotime($account_info['date_expires']) - time();

							$price_per_second = $order['total'] / $seconds_in_cycle;

							$prorate += ($price_per_second * $seconds_remaining_in_cycle);
						}
					}
				}
			}

			if ($prorate > 0) {
				$this->load->language('total/prorate');

				if ($prorate > $total) {
					$prorate_credit = $prorate - $total;
				} else {
					$prorate_credit = 0;
				}

				$prorate = min($prorate, $total);

				$total_data[] = array(
					'code'       => 'prorate',
					'title'      => $this->language->get('text_prorate'),
					'value'      => -$prorate,
					'sort_order' => $this->config->get('prorate_sort_order')
				);

				if ($prorate_credit) {
					$this->session->data['prorate_credit'] = $prorate_credit;
				}

				$total -= $prorate;
			}
		}
	}
}