<?php
class ModelTotalProrateCredit extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('prorate_status') && $this->config->get('prorate_credit_status') && isset($this->session->data['prorate_credit'])) {
			$this->load->language('total/prorate_credit');

			$total_data[] = array(
				'code'       => 'prorate_credit',
				'title'      => $this->language->get('text_prorate_credit'),
				'help'       => $this->language->get('help_prorate_credit'),
				'value'      => $this->session->data['prorate_credit'],
				'sort_order' => $this->config->get('prorate_credit_sort_order')
			);
		}
	}

	public function confirm($order_info, $order_total) {
		if ($order_info['customer_id']) {
			$this->load->language('total/prorate_credit');

			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$order_info['customer_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])) . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
		}
	}

	public function unconfirm($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");
	}
}